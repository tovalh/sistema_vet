<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $branches = Branch::where('tenant_id', $user->tenant_id)
            ->with(['assignedUsers:id,name,email'])
            ->withCount('assignedUsers')
            ->orderBy('is_main', 'desc')
            ->orderBy('name')
            ->get();

        return Inertia::render('Branches/Index', [
            'branches' => $branches
        ]);
    }

    public function create()
    {
        $users = User::where('tenant_id', Auth::user()->tenant_id)
            ->where('id', '!=', Auth::id())
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return Inertia::render('Branches/Create', [
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:branches,code',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $branch = Branch::create([
            'tenant_id' => $user->tenant_id,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_main' => false,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Asignar usuarios a la sucursal
        if ($request->user_ids) {
            $validUsers = User::where('tenant_id', $user->tenant_id)
                ->whereIn('id', $request->user_ids)
                ->pluck('id');
            
            $branch->assignedUsers()->attach($validUsers);
        }

        // Los owners automáticamente tienen acceso a todas las sucursales
        $owners = User::where('tenant_id', $user->tenant_id)
            ->whereHas('roles', function($query) {
                $query->where('name', 'clinic-owner');
            })
            ->pluck('id');
            
        $branch->assignedUsers()->syncWithoutDetaching($owners);

        return redirect()->route('branches.index')
            ->with('success', 'Sucursal creada exitosamente.');
    }

    public function show(Branch $branch)
    {
        $user = Auth::user();
        
        if ($branch->tenant_id !== $user->tenant_id) {
            abort(403);
        }

        $users = User::where('tenant_id', $user->tenant_id)
            ->where('id', '!=', $user->id)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $branch->load(['assignedUsers' => function($query) {
            $query->select('users.id', 'users.name', 'users.email', 'users.phone');
        }]);

        return Inertia::render('Branches/Show', [
            'branch' => $branch,
            'users' => $users
        ]);
    }

    public function edit(Branch $branch)
    {
        $user = Auth::user();
        
        if ($branch->tenant_id !== $user->tenant_id) {
            abort(403);
        }

        $users = User::where('tenant_id', $user->tenant_id)
            ->where('id', '!=', $user->id)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $branch->load(['assignedUsers:id,name,email']);

        return Inertia::render('Branches/Edit', [
            'branch' => $branch,
            'users' => $users
        ]);
    }

    public function update(Request $request, Branch $branch)
    {
        $user = Auth::user();
        
        if ($branch->tenant_id !== $user->tenant_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:branches,code,' . $branch->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $branch->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Sincronizar usuarios asignados
        $userIds = $request->user_ids ?: [];
        
        // Asegurar que los owners siempre tengan acceso
        $owners = User::where('tenant_id', $user->tenant_id)
            ->whereHas('roles', function($query) {
                $query->where('name', 'clinic-owner');
            })
            ->pluck('id')
            ->toArray();
            
        $userIds = array_unique(array_merge($userIds, $owners));

        $branch->assignedUsers()->sync($userIds);

        return redirect()->route('branches.index')
            ->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Branch $branch)
    {
        $user = Auth::user();
        
        if ($branch->tenant_id !== $user->tenant_id) {
            abort(403);
        }

        if ($branch->is_main) {
            return back()->withErrors('No se puede eliminar la sucursal principal.');
        }

        // Verificar si hay usuarios asignados únicamente a esta sucursal
        $exclusiveUsers = $branch->assignedUsers()
            ->whereDoesntHave('branches', function($query) use ($branch) {
                $query->where('branch_id', '!=', $branch->id);
            })
            ->whereDoesntHave('roles', function($query) {
                $query->where('name', 'clinic-owner');
            })
            ->count();

        if ($exclusiveUsers > 0) {
            return back()->withErrors('No se puede eliminar la sucursal porque hay usuarios que solo tienen acceso a esta sucursal. Reasígnalos primero.');
        }

        $branch->assignedUsers()->detach();
        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', 'Sucursal eliminada exitosamente.');
    }

    public function switch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);
        
        $user = auth()->user();
        $branch = Branch::findOrFail($request->branch_id);
        
        // Verificar que la sucursal pertenezca al tenant del usuario
        if ($branch->tenant_id !== $user->tenant_id) {
            abort(403, 'No tienes acceso a esta sucursal');
        }
        
        // Verificar que el usuario tenga acceso a esta sucursal
        if (!$user->hasAccessToBranch($branch->id)) {
            abort(403, 'No tienes acceso a esta sucursal');
        }
        
        // Verificar que la sucursal esté activa
        if (!$branch->is_active) {
            return back()->withErrors('Esta sucursal no está activa');
        }
        
        // Actualizar el usuario con la nueva sucursal
        $user->update(['branch_id' => $branch->id]);
        
        // Actualizar la sesión
        session(['branch_id' => $branch->id]);
        
        return back()->with('success', 'Sucursal cambiada a: ' . $branch->name);
    }
}

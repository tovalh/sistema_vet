<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BranchController extends Controller
{
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

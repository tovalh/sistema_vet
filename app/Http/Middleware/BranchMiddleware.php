<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->isSuperAdmin() && session('tenant_id')) {
            $user = auth()->user();
            
            // Obtener las sucursales accesibles para este usuario
            $accessibleBranches = $user->getAccessibleBranches();
            
            // Si el usuario no tiene sucursal actual asignada o no tiene acceso a ella
            $currentBranchId = $user->branch_id;
            if (!$currentBranchId || !$user->hasAccessToBranch($currentBranchId)) {
                // Asignar la primera sucursal accesible (preferir la principal)
                $newBranch = $accessibleBranches->where('is_main', true)->first() 
                    ?: $accessibleBranches->first();
                    
                if ($newBranch) {
                    $user->update(['branch_id' => $newBranch->id]);
                    $currentBranchId = $newBranch->id;
                }
            }
            
            // Establecer branch_id en sesión
            if ($currentBranchId) {
                session(['branch_id' => $currentBranchId]);
                
                // Obtener información de la sucursal actual
                $currentBranch = Branch::find($currentBranchId);
                view()->share('currentBranch', $currentBranch);
                
                // Compartir sucursales disponibles para el selector (solo las accesibles)
                $availableBranches = $accessibleBranches->where('is_active', true);
                view()->share('availableBranches', $availableBranches);
            } else {
                // Usuario sin acceso a ninguna sucursal
                if (!$user->isOwner()) {
                    abort(403, 'No tienes acceso a ninguna sucursal. Contacta al administrador.');
                }
            }
        }
        
        return $next($request);
    }
}

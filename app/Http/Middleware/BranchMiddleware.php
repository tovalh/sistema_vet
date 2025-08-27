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
            
            // Si el usuario no tiene sucursal asignada, asignar la principal
            if (!$user->branch_id) {
                $mainBranch = Branch::where('tenant_id', $user->tenant_id)
                    ->where('is_main', true)
                    ->first();
                    
                if ($mainBranch) {
                    $user->update(['branch_id' => $mainBranch->id]);
                }
            }
            
            // Establecer branch_id en sesión
            if ($user->branch_id) {
                session(['branch_id' => $user->branch_id]);
                
                // Obtener información de la sucursal actual
                $currentBranch = Branch::find($user->branch_id);
                view()->share('currentBranch', $currentBranch);
                
                // Obtener todas las sucursales del tenant para el selector
                $availableBranches = Branch::where('tenant_id', $user->tenant_id)
                    ->where('is_active', true)
                    ->get();
                view()->share('availableBranches', $availableBranches);
            }
        }
        
        return $next($request);
    }
}

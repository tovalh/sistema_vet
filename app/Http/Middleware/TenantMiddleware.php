<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->isSuperAdmin()) {
            $user = auth()->user();
            
            // Verificar que el usuario tenga un tenant asociado
            if (!$user->tenant_id) {
                auth()->logout();
                return redirect()->route('login')->withErrors('Usuario no asociado a ninguna clínica.');
            }
            
            // Verificar que el tenant esté activo
            $tenant = Tenant::find($user->tenant_id);
            if (!$tenant || !$tenant->isActive()) {
                auth()->logout();
                return redirect()->route('login')->withErrors('Clínica inactiva o suspendida.');
            }
            
            // Verificar suscripción activa (excepto para período de prueba)
            if (!$tenant->hasActiveSubscription() && !$tenant->isOnTrial()) {
                return redirect()->route('subscription.expired')
                    ->with('error', 'Suscripción expirada. Renueve su plan para continuar.');
            }
            
            // Establecer tenant en sesión para los scopes
            session(['tenant_id' => $user->tenant_id]);
            
            // Compartir tenant con todas las vistas
            view()->share('currentTenant', $tenant);
        }
        
        return $next($request);
    }
}

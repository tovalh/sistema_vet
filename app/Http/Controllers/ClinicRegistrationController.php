<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Validation\Rules\Password;

class ClinicRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        $plans = SubscriptionPlan::active()->get();
        
        return Inertia::render('Auth/ClinicRegister', [
            'plans' => $plans,
        ]);
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'clinic_name' => 'required|string|max:255',
            'clinic_email' => 'required|string|email|max:255|unique:tenants,email',
            'clinic_phone' => 'nullable|string|max:20',
            'clinic_address' => 'nullable|string|max:500',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'plan_id' => 'required|exists:subscription_plans,id',
            'terms' => 'accepted',
        ]);
        
        DB::transaction(function () use ($request) {
            // Crear el tenant (clínica)
            $tenant = Tenant::create([
                'name' => $request->clinic_name,
                'slug' => Str::slug($request->clinic_name) . '-' . Str::random(6),
                'email' => $request->clinic_email,
                'phone' => $request->clinic_phone,
                'address' => $request->clinic_address,
                'status' => 'active',
                'trial_ends_at' => now()->addDays(14), // 14 días de prueba
            ]);
            
            // Crear el usuario owner
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->owner_name,
                'email' => $request->owner_email,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);
            
            // Asignar rol de owner
            $user->assignRole('clinic-owner');
            
            // Crear suscripción de prueba
            $plan = SubscriptionPlan::find($request->plan_id);
            Subscription::create([
                'tenant_id' => $tenant->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'amount' => 0, // Gratis durante el período de prueba
                'starts_at' => now(),
                'ends_at' => now()->addDays(14),
                'trial_ends_at' => now()->addDays(14),
            ]);
            
            // Autenticar usuario
            auth()->login($user);
        });
        
        return redirect()->route('dashboard')->with('success', '¡Clínica registrada exitosamente! Disfruta de tu prueba gratuita de 14 días.');
    }
}

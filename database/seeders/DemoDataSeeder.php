<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = SubscriptionPlan::all();
        
        // Crear clínicas de demostración
        $clinics = [
            [
                'name' => 'Clínica Veterinaria San José',
                'email' => 'admin@clinicasanjose.com',
                'phone' => '+506 2234-5678',
                'address' => 'Avenida Central, San José, Costa Rica',
                'owner' => [
                    'name' => 'Dr. María González',
                    'email' => 'maria.gonzalez@clinicasanjose.com',
                ],
                'plan_slug' => 'profesional',
            ],
            [
                'name' => 'Hospital Veterinario Los Ángeles',
                'email' => 'contacto@hospitalangeles.com',
                'phone' => '+1 555-0123',
                'address' => '123 Main Street, Los Angeles, CA',
                'owner' => [
                    'name' => 'Dr. Carlos Mendoza',
                    'email' => 'carlos.mendoza@hospitalangeles.com',
                ],
                'plan_slug' => 'premium',
            ],
            [
                'name' => 'Clínica Mascotas Felices',
                'email' => 'info@mascotasfelices.com',
                'phone' => '+52 55 1234-5678',
                'address' => 'Colonia Roma Norte, Ciudad de México',
                'owner' => [
                    'name' => 'Dra. Ana Rodríguez',
                    'email' => 'ana.rodriguez@mascotasfelices.com',
                ],
                'plan_slug' => 'basico',
            ],
        ];

        foreach ($clinics as $clinicData) {
            // Crear el tenant
            $tenant = Tenant::create([
                'name' => $clinicData['name'],
                'slug' => Str::slug($clinicData['name']) . '-' . Str::random(4),
                'email' => $clinicData['email'],
                'phone' => $clinicData['phone'],
                'address' => $clinicData['address'],
                'status' => 'active',
                'trial_ends_at' => now()->addDays(7), // 7 días restantes de prueba
            ]);

            // Crear el usuario owner
            $owner = User::create([
                'tenant_id' => $tenant->id,
                'name' => $clinicData['owner']['name'],
                'email' => $clinicData['owner']['email'],
                'password' => Hash::make('password123'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            
            $owner->assignRole('clinic-owner');

            // Crear usuarios adicionales para cada clínica
            $additionalUsers = [
                [
                    'name' => 'Dr. Juan Pérez',
                    'email' => 'juan.perez@' . explode('@', $clinicData['email'])[1],
                    'role' => 'doctor',
                ],
                [
                    'name' => 'Laura Martínez',
                    'email' => 'laura.martinez@' . explode('@', $clinicData['email'])[1],
                    'role' => 'secretary',
                ],
            ];

            foreach ($additionalUsers as $userData) {
                $user = User::create([
                    'tenant_id' => $tenant->id,
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password123'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
                
                $user->assignRole($userData['role']);
            }

            // Crear suscripción
            $plan = $plans->where('slug', $clinicData['plan_slug'])->first();
            Subscription::create([
                'tenant_id' => $tenant->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'amount' => 0, // Gratis durante el período de prueba
                'starts_at' => now()->subDays(7),
                'ends_at' => now()->addDays(7),
                'trial_ends_at' => now()->addDays(7),
            ]);
        }
        
        $this->command->info('✅ Creadas 3 clínicas de demostración con usuarios y suscripciones');
        $this->command->info('📧 Emails de acceso:');
        foreach ($clinics as $clinic) {
            $this->command->info('   - ' . $clinic['owner']['email'] . ' (Dueño: ' . $clinic['name'] . ')');
        }
        $this->command->info('🔑 Contraseña para todos: password123');
    }
}

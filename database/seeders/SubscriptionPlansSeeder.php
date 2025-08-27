<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Plan Básico',
                'slug' => 'basico',
                'description' => 'Perfecto para clínicas pequeñas que están comenzando',
                'price' => 29.99,
                'features' => [
                    'Hasta 3 usuarios',
                    'Hasta 100 pacientes',
                    'Gestión de citas',
                    'Historias clínicas básicas',
                    'Soporte por email',
                ],
                'max_users' => 3,
                'max_patients' => 100,
                'has_inventory' => false,
                'has_reports' => false,
                'has_api_access' => false,
            ],
            [
                'name' => 'Plan Profesional',
                'slug' => 'profesional',
                'description' => 'Ideal para clínicas en crecimiento con necesidades avanzadas',
                'price' => 59.99,
                'features' => [
                    'Hasta 10 usuarios',
                    'Hasta 500 pacientes',
                    'Gestión completa de citas',
                    'Historias clínicas avanzadas',
                    'Inventario básico',
                    'Reportes básicos',
                    'Soporte prioritario',
                ],
                'max_users' => 10,
                'max_patients' => 500,
                'has_inventory' => true,
                'has_reports' => true,
                'has_api_access' => false,
            ],
            [
                'name' => 'Plan Premium',
                'slug' => 'premium',
                'description' => 'Para clínicas grandes con necesidades empresariales',
                'price' => 99.99,
                'features' => [
                    'Usuarios ilimitados',
                    'Pacientes ilimitados',
                    'Todas las funcionalidades',
                    'Inventario avanzado',
                    'Reportes avanzados y analytics',
                    'Acceso a API',
                    'Integraciones',
                    'Soporte 24/7',
                    'Backups automatizados',
                ],
                'max_users' => 999999,
                'max_patients' => null,
                'has_inventory' => true,
                'has_reports' => true,
                'has_api_access' => true,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::create($planData);
        }
    }
}

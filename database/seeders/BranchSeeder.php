<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            // Crear sucursal principal
            $mainBranch = Branch::create([
                'tenant_id' => $tenant->id,
                'name' => 'Sede Principal',
                'code' => 'MAIN',
                'phone' => $tenant->phone,
                'address' => $tenant->address,
                'is_main' => true,
                'is_active' => true,
            ]);
            
            // Crear sucursales adicionales según el tenant
            $additionalBranches = [];
            
            if ($tenant->name === 'Clínica Veterinaria San José') {
                $additionalBranches = [
                    [
                        'name' => 'Sucursal Escazú',
                        'code' => 'ESC',
                        'address' => 'Centro Comercial Multiplaza, Escazú',
                        'phone' => '+506 2289-1234',
                    ],
                    [
                        'name' => 'Sucursal Cartago',
                        'code' => 'CAR',
                        'address' => 'Centro de Cartago, 200m norte del parque central',
                        'phone' => '+506 2591-5678',
                    ],
                ];
            } elseif ($tenant->name === 'Hospital Veterinario Los Ángeles') {
                $additionalBranches = [
                    [
                        'name' => 'Sucursal Beverly Hills',
                        'code' => 'BH',
                        'address' => '456 Beverly Drive, Beverly Hills, CA',
                        'phone' => '+1 555-0456',
                    ],
                    [
                        'name' => 'Sucursal Santa Monica',
                        'code' => 'SM',
                        'address' => '789 Ocean Ave, Santa Monica, CA',
                        'phone' => '+1 555-0789',
                    ],
                    [
                        'name' => 'Sucursal Pasadena',
                        'code' => 'PAS',
                        'address' => '321 Colorado Blvd, Pasadena, CA',
                        'phone' => '+1 555-0321',
                    ],
                ];
            } elseif ($tenant->name === 'Clínica Mascotas Felices') {
                $additionalBranches = [
                    [
                        'name' => 'Sucursal Condesa',
                        'code' => 'CON',
                        'address' => 'Colonia Condesa, Ciudad de México',
                        'phone' => '+52 55 5678-9012',
                    ],
                ];
            }
            
            // Crear las sucursales adicionales
            foreach ($additionalBranches as $branchData) {
                Branch::create([
                    'tenant_id' => $tenant->id,
                    'name' => $branchData['name'],
                    'code' => $branchData['code'],
                    'address' => $branchData['address'],
                    'phone' => $branchData['phone'],
                    'is_main' => false,
                    'is_active' => true,
                ]);
            }
            
            // Asignar usuarios a sucursales
            $users = $tenant->users;
            $branches = $tenant->branches;
            
            foreach ($users as $index => $user) {
                // El primer usuario (owner) va a la sucursal principal
                // Los demás se distribuyen entre las sucursales
                $branchIndex = $index === 0 ? 0 : ($index % $branches->count());
                $user->update(['branch_id' => $branches[$branchIndex]->id]);
            }
        }
        
        $this->command->info('✅ Creadas sucursales para todas las clínicas');
        $this->command->info('🏢 Sucursales por clínica:');
        
        foreach ($tenants as $tenant) {
            $branchCount = $tenant->branches()->count();
            $this->command->info("   - {$tenant->name}: {$branchCount} sucursales");
        }
    }
}

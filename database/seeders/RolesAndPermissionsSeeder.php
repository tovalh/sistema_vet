<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetear caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Gestión de usuarios
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Gestión de pacientes
            'patients.view',
            'patients.create',
            'patients.edit',
            'patients.delete',
            
            // Gestión de citas
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.delete',
            
            // Gestión de historias clínicas
            'medical-records.view',
            'medical-records.create',
            'medical-records.edit',
            'medical-records.delete',
            
            // Gestión de inventario
            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',
            
            // Reportes
            'reports.view',
            'reports.export',
            
            // Configuración de clínica
            'clinic.settings',
            'clinic.billing',
            
            // Facturación
            'billing.view',
            'billing.create',
            'billing.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        
        // Super Administrador (Global)
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());
        
        // Administrador de Clínica (Owner)
        $clinicOwner = Role::create(['name' => 'clinic-owner']);
        $clinicOwner->givePermissionTo([
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'patients.view', 'patients.create', 'patients.edit', 'patients.delete',
            'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.delete',
            'medical-records.view', 'medical-records.create', 'medical-records.edit',
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
            'reports.view', 'reports.export',
            'clinic.settings', 'clinic.billing',
            'billing.view', 'billing.create', 'billing.edit',
        ]);
        
        // Doctor/Veterinario
        $doctor = Role::create(['name' => 'doctor']);
        $doctor->givePermissionTo([
            'patients.view', 'patients.create', 'patients.edit',
            'appointments.view', 'appointments.edit',
            'medical-records.view', 'medical-records.create', 'medical-records.edit', 'medical-records.delete',
            'inventory.view',
            'reports.view',
        ]);
        
        // Secretaria/Recepcionista
        $secretary = Role::create(['name' => 'secretary']);
        $secretary->givePermissionTo([
            'patients.view', 'patients.create', 'patients.edit',
            'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.delete',
            'billing.view', 'billing.create', 'billing.edit',
        ]);
    }
}

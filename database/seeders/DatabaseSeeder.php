<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SubscriptionPlansSeeder::class,
            DemoDataSeeder::class,
        ]);

        // Crear super administrador
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@sistemavet.com',
            'is_super_admin' => true,
            'status' => 'active',
        ]);
        
        $superAdmin->assignRole('super-admin');

        // Usuario de prueba
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}

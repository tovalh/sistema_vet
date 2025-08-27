<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Branch;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientAppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::with(['branches', 'users'])->get();
        
        foreach ($tenants as $tenant) {
            $branches = $tenant->branches;
            $doctors = $tenant->users()->whereHas('roles', function ($query) {
                $query->whereIn('name', ['doctor', 'clinic-owner']);
            })->get();
            
            if ($doctors->isEmpty()) {
                continue;
            }
            
            // Crear pacientes para cada sucursal
            foreach ($branches as $branch) {
                $patientsData = [
                    [
                        'name' => 'Max',
                        'species' => 'Perro',
                        'breed' => 'Labrador',
                        'gender' => 'male',
                        'birth_date' => now()->subYears(3)->subMonths(2),
                        'weight' => 28.5,
                        'color' => 'Dorado',
                        'owner_name' => 'Juan Pérez',
                        'owner_phone' => '+1-555-0101',
                        'owner_email' => 'juan.perez@email.com',
                    ],
                    [
                        'name' => 'Luna',
                        'species' => 'Gato',
                        'breed' => 'Persa',
                        'gender' => 'female',
                        'birth_date' => now()->subYears(2),
                        'weight' => 4.2,
                        'color' => 'Blanco',
                        'owner_name' => 'María González',
                        'owner_phone' => '+1-555-0102',
                        'owner_email' => 'maria.gonzalez@email.com',
                    ],
                    [
                        'name' => 'Rocky',
                        'species' => 'Perro',
                        'breed' => 'Bulldog',
                        'gender' => 'male',
                        'birth_date' => now()->subMonths(8),
                        'weight' => 15.0,
                        'color' => 'Marrón',
                        'owner_name' => 'Carlos Rodríguez',
                        'owner_phone' => '+1-555-0103',
                    ],
                    [
                        'name' => 'Mimi',
                        'species' => 'Gato',
                        'breed' => 'Siames',
                        'gender' => 'female',
                        'birth_date' => now()->subYears(5),
                        'weight' => 3.8,
                        'color' => 'Café',
                        'owner_name' => 'Ana Martínez',
                        'owner_phone' => '+1-555-0104',
                        'owner_email' => 'ana.martinez@email.com',
                    ],
                    [
                        'name' => 'Zeus',
                        'species' => 'Perro',
                        'breed' => 'Pastor Alemán',
                        'gender' => 'male',
                        'birth_date' => now()->subYears(4)->subMonths(6),
                        'weight' => 35.2,
                        'color' => 'Negro y café',
                        'owner_name' => 'Pedro López',
                        'owner_phone' => '+1-555-0105',
                    ],
                ];
                
                foreach ($patientsData as $patientData) {
                    $patient = Patient::create([
                        'tenant_id' => $tenant->id,
                        'branch_id' => $branch->id,
                        ...$patientData
                    ]);
                    
                    // Crear citas para cada paciente
                    $appointmentTypes = ['Consulta General', 'Vacunación', 'Control', 'Cirugía Menor', 'Desparasitación'];
                    $statuses = ['scheduled', 'confirmed', 'completed', 'in_progress'];
                    
                    // Citas pasadas
                    for ($i = 0; $i < 2; $i++) {
                        Appointment::create([
                            'tenant_id' => $tenant->id,
                            'branch_id' => $branch->id,
                            'patient_id' => $patient->id,
                            'doctor_id' => $doctors->random()->id,
                            'scheduled_at' => now()->subDays(rand(1, 30))->setTime(rand(8, 17), [0, 30][rand(0, 1)]),
                            'duration_minutes' => [30, 45, 60][rand(0, 2)],
                            'status' => 'completed',
                            'type' => $appointmentTypes[array_rand($appointmentTypes)],
                            'reason' => 'Cita médica de rutina',
                            'price' => rand(50, 200),
                        ]);
                    }
                    
                    // Citas futuras
                    for ($i = 0; $i < 3; $i++) {
                        Appointment::create([
                            'tenant_id' => $tenant->id,
                            'branch_id' => $branch->id,
                            'patient_id' => $patient->id,
                            'doctor_id' => $doctors->random()->id,
                            'scheduled_at' => now()->addDays(rand(1, 30))->setTime(rand(8, 17), [0, 30][rand(0, 1)]),
                            'duration_minutes' => [30, 45, 60][rand(0, 2)],
                            'status' => $statuses[array_rand($statuses)],
                            'type' => $appointmentTypes[array_rand($appointmentTypes)],
                            'reason' => 'Consulta programada',
                            'price' => rand(50, 200),
                        ]);
                    }
                }
            }
        }
        
        $this->command->info('✅ Creados pacientes y citas para todas las sucursales');
        
        $totalPatients = Patient::count();
        $totalAppointments = Appointment::count();
        
        $this->command->info("🐶 Total pacientes: {$totalPatients}");
        $this->command->info("📅 Total citas: {$totalAppointments}");
    }
}

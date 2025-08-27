<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $branchId = $user->branch_id;
        
        // Estadísticas generales
        $stats = [
            'total_patients' => Patient::where('tenant_id', $tenantId)->count(),
            'branch_patients' => $branchId ? Patient::where('branch_id', $branchId)->count() : 0,
            'today_appointments' => Appointment::where('tenant_id', $tenantId)
                ->when($branchId, fn($query) => $query->where('branch_id', $branchId))
                ->whereDate('scheduled_at', today())
                ->count(),
            'pending_appointments' => Appointment::where('tenant_id', $tenantId)
                ->when($branchId, fn($query) => $query->where('branch_id', $branchId))
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->where('scheduled_at', '>', now())
                ->count(),
        ];
        
        // Citas de hoy
        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->where('tenant_id', $tenantId)
            ->when($branchId, fn($query) => $query->where('branch_id', $branchId))
            ->whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();
            
        // Próximas citas
        $upcomingAppointments = Appointment::with(['patient', 'doctor'])
            ->where('tenant_id', $tenantId)
            ->when($branchId, fn($query) => $query->where('branch_id', $branchId))
            ->where('scheduled_at', '>', now())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();
            
        // Pacientes recientes
        $recentPatients = Patient::where('tenant_id', $tenantId)
            ->when($branchId, fn($query) => $query->where('branch_id', $branchId))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Estadísticas de la semana
        $weekStats = [
            'completed_appointments' => Appointment::where('tenant_id', $tenantId)
                ->when($branchId, fn($query) => $query->where('branch_id', $branchId))
                ->where('status', 'completed')
                ->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'week_revenue' => (float) Appointment::where('tenant_id', $tenantId)
                ->when($branchId, fn($query) => $query->where('branch_id', $branchId))
                ->where('status', 'completed')
                ->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('price'),
            'new_patients' => Patient::where('tenant_id', $tenantId)
                ->when($branchId, fn($query) => $query->where('branch_id', $branchId))
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];
        
        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'weekStats' => $weekStats,
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'recentPatients' => $recentPatients,
        ]);
    }
}

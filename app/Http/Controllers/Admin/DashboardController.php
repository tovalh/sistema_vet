<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Verificar que sea super admin
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Acceso denegado');
        }
        
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'total_users' => User::where('is_super_admin', false)->count(),
            'total_subscriptions' => Subscription::where('status', 'active')->count(),
            'total_branches' => Branch::count(),
            'total_patients' => Patient::count(),
            'total_appointments' => Appointment::count(),
            'monthly_revenue' => Subscription::where('status', 'active')->sum('amount'),
        ];
        
        $tenants = Tenant::with(['users', 'activeSubscription.plan', 'branches'])
            ->withCount(['users', 'branches', 'branches as patients_count' => function($query) {
                $query->join('patients', 'branches.id', '=', 'patients.branch_id');
            }])
            ->latest()
            ->get();
            
        $plans = SubscriptionPlan::withCount('subscriptions')->get();
        
        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'tenants' => $tenants,
            'plans' => $plans,
        ]);
    }
}

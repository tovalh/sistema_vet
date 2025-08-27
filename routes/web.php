<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\ClinicRegistrationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing Pages
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');
Route::get('/clear-theme', function() { return \Inertia\Inertia::render('Landing/ClearTheme'); });

// Clinic Registration
Route::get('/register', [ClinicRegistrationController::class, 'showRegistrationForm'])->name('clinic.register');
Route::post('/clinic/register', [ClinicRegistrationController::class, 'register'])->name('clinic.register.store');

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin routes (solo super admin)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    });
    
    // Tenant routes (con middleware tenant y branch)
    Route::middleware(['tenant', 'branch'])->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        
        // Branch switching
        Route::post('branch/switch', [\App\Http\Controllers\BranchController::class, 'switch'])->name('branch.switch');
    });
});

// Subscription expired page
Route::get('/subscription/expired', function () {
    return Inertia::render('Subscription/Expired');
})->name('subscription.expired');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

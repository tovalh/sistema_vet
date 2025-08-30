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

// Onboarding routes (requieren auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/create-tenant', [\App\Http\Controllers\OnboardingController::class, 'createTenant'])->name('onboarding.create-tenant');
    Route::get('/onboarding/welcome', [\App\Http\Controllers\OnboardingController::class, 'welcome'])->name('onboarding.welcome');
    Route::get('/onboarding/complete', [\App\Http\Controllers\OnboardingController::class, 'complete'])->name('onboarding.complete');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin routes (solo super admin)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Clinic Management
        Route::get('/clinics', [\App\Http\Controllers\Admin\ClinicController::class, 'index'])->name('clinics.index');
        Route::get('/clinics/{clinic}', [\App\Http\Controllers\Admin\ClinicController::class, 'show'])->name('clinics.show');
        Route::get('/clinics/{clinic}/edit', [\App\Http\Controllers\Admin\ClinicController::class, 'edit'])->name('clinics.edit');
        Route::put('/clinics/{clinic}', [\App\Http\Controllers\Admin\ClinicController::class, 'update'])->name('clinics.update');
        Route::post('/clinics/{clinic}/suspend', [\App\Http\Controllers\Admin\ClinicController::class, 'suspend'])->name('clinics.suspend');
        Route::post('/clinics/{clinic}/activate', [\App\Http\Controllers\Admin\ClinicController::class, 'activate'])->name('clinics.activate');
        
        // User Management
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        
        // User Impersonation
        Route::get('/impersonate', [\App\Http\Controllers\Admin\ImpersonateController::class, 'index'])->name('impersonate.index');
        Route::post('/impersonate/{user}', [\App\Http\Controllers\Admin\ImpersonateController::class, 'impersonate'])->name('impersonate.start');
        
        // Subscription Plans Management
        Route::resource('/subscription-plans', \App\Http\Controllers\Admin\SubscriptionPlanController::class)->names('subscription-plans');
    });
    
    // Stop impersonation (available outside admin group)
    Route::post('/admin/stop-impersonating', [\App\Http\Controllers\Admin\ImpersonateController::class, 'stopImpersonating'])->name('admin.impersonate.stop');
    
    // Tenant routes (con middleware tenant y branch)
    Route::middleware(['tenant', 'branch'])->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        
        // Branch management
        Route::resource('branches', \App\Http\Controllers\BranchController::class);
        Route::post('branch/switch', [\App\Http\Controllers\BranchController::class, 'switch'])->name('branch.switch');
        
        // User management (tenant-specific)
        Route::resource('users', \App\Http\Controllers\UserController::class);
        
        // CRM Module - Tutors/Clients management
        Route::resource('tutors', \App\Http\Controllers\TutorController::class);
        
        // Patient management (tenant-specific)
        Route::resource('patients', \App\Http\Controllers\PatientController::class);

        // Appointments Module - Citas y Agenda
        Route::resource('appointments', \App\Http\Controllers\AppointmentController::class);
    });
});

// Subscription expired page
Route::get('/subscription/expired', function () {
    return Inertia::render('Subscription/Expired');
})->name('subscription.expired');

// Public Booking Portal
Route::get('/book/{subdomain}', [\App\Http\Controllers\PublicBookingController::class, 'index'])->name('booking.index');
Route::post('/book/{subdomain}/identify', [\App\Http\Controllers\PublicBookingController::class, 'identify'])->name('booking.identify');
Route::get('/book/{subdomain}/pets', [\App\Http\Controllers\PublicBookingController::class, 'selectPet'])->name('booking.pets');
Route::post('/book/{subdomain}/pets', [\App\Http\Controllers\PublicBookingController::class, 'storePet'])->name('booking.pets.store');
Route::get('/book/{subdomain}/type', [\App\Http\Controllers\PublicBookingController::class, 'selectType'])->name('booking.type');
Route::post('/book/{subdomain}/type', [\App\Http\Controllers\PublicBookingController::class, 'storeType'])->name('booking.type.store');
Route::get('/book/{subdomain}/time', [\App\Http\Controllers\PublicBookingController::class, 'selectTime'])->name('booking.time');
Route::post('/book/{subdomain}/time', [\App\Http\Controllers\PublicBookingController::class, 'storeTime'])->name('booking.time.store');
Route::get('/book/{subdomain}/confirm', [\App\Http\Controllers\PublicBookingController::class, 'confirm'])->name('booking.confirm');
Route::post('/book/{subdomain}/confirm', [\App\Http\Controllers\PublicBookingController::class, 'store'])->name('booking.store');
Route::get('/book/{subdomain}/success', [\App\Http\Controllers\PublicBookingController::class, 'success'])->name('booking.success');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

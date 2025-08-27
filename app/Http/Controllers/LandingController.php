<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::active()->get();
        
        return Inertia::render('Landing/Index', [
            'plans' => $plans,
        ]);
    }
    
    public function pricing()
    {
        $plans = SubscriptionPlan::active()->get();
        
        return Inertia::render('Landing/Pricing', [
            'plans' => $plans,
        ]);
    }
}

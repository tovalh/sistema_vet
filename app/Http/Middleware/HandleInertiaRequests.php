<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $currentBranch = $this->getCurrentBranch($request);
        $availableBranches = $this->getAvailableBranches($request);

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'currentBranch' => $currentBranch,
            'availableBranches' => $availableBranches,
            'impersonating' => [
                'is_impersonating' => session()->has('impersonating_admin_id'),
                'admin_id' => session('impersonating_admin_id'),
                'start_time' => session('impersonating_start'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
    
    private function getCurrentBranch($request)
    {
        $user = $request->user();
        if (!$user || $user->is_super_admin || !$user->branch_id) {
            return null;
        }
        
        return \App\Models\Branch::find($user->branch_id);
    }
    
    private function getAvailableBranches($request)
    {
        $user = $request->user();
        if (!$user || $user->is_super_admin || !$user->tenant_id) {
            return [];
        }
        
        return \App\Models\Branch::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->orderBy('is_main', 'desc')
            ->orderBy('name')
            ->get();
    }
}

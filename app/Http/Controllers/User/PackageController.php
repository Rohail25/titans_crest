<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Services\PackageSubscriptionService;
use Illuminate\Http\RedirectResponse;

class PackageController extends Controller
{
    public function __construct(
        protected PackageSubscriptionService $packageSubscriptionService,
    ) {}

    public function subscribe(Package $package): RedirectResponse
    {
        try {
            $this->packageSubscriptionService->subscribe(auth()->user(), $package);

            return redirect()->route('user.dashboard')
                ->with('success', 'Package subscribed successfully. Daily profit is now active.');
        } catch (\Exception $e) {
            return back()->withErrors(['subscription' => $e->getMessage()]);
        }
    }
}

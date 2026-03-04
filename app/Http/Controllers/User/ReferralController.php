<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use App\Services\WalletService;

class ReferralController extends Controller
{
    public function __construct(
        protected ReferralService $referralService,
        protected WalletService $walletService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $referralStats = $this->referralService->getReferralStats($user);
        $downline = $this->referralService->getDownline($user);
        $walletSummary = $this->walletService->getWalletSummary($user);

        return view('user.referral.index', [
            'referral' => $referralStats,
            'downline' => $downline,
            'wallet' => $walletSummary,
        ]);
    }
}

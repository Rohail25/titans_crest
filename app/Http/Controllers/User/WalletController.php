<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Services\WalletService;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $walletSummary = $this->walletService->getWalletSummary($user);

        return view('user.wallet.index', [
            'wallet' => $walletSummary,
        ]);
    }
}

<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Services\DepositService;
use App\Services\WalletService;

class DepositController extends Controller
{
    public function __construct(
        protected DepositService $depositService,
        protected WalletService $walletService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $walletSummary = $this->walletService->getWalletSummary($user);
        $depositHistory = $this->depositService->getUserDepositHistory($user);

        // BNB wallet address (would be stored in settings)
        $bnbAddress = \App\Models\Setting::get('bnb_wallet_address', '0x...');

        return view('user.deposit.index', [
            'wallet' => $walletSummary,
            'deposits' => $depositHistory,
            'bnbAddress' => $bnbAddress,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:999999'],
            'tx_hash' => ['required', 'string', 'unique:deposits,tx_hash'],
        ]);

        try {
            $user = auth()->user();
            $deposit = $this->depositService->createDeposit(
                $user,
                (float) $validated['amount'],
                $validated['tx_hash']
            );

            // In production, verify tx_hash on blockchain
            // If verified, call: $this->depositService->confirmDeposit($deposit);

            return redirect()->route('user.deposit.index')
                ->with('success', 'Deposit submitted. Waiting for blockchain confirmation.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

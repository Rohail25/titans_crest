<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\DepositService;
use App\Services\WalletService;

class DepositController extends Controller
{
    public function __construct(
        protected DepositService $depositService,
        protected WalletService $walletService,
    ) {}

    /**
     * Confirm a pending deposit - credit user wallet
     */
    public function confirmDeposit(Deposit $deposit): RedirectResponse
    {
        if ($deposit->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending deposits can be confirmed.']);
        }

        try {
            // Confirm deposit in service
            $this->depositService->confirmDeposit($deposit);

            return back()->with('success', sprintf(
                'Deposit of %s %s confirmed for %s',
                $deposit->currency ?? 'BNB',
                number_format((float) $deposit->amount, 2),
                $deposit->user->name
            ));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to confirm deposit: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a pending deposit
     */
    public function rejectDeposit(Request $request, Deposit $deposit): RedirectResponse
    {
        if ($deposit->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending deposits can be rejected.']);
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->depositService->rejectDeposit($deposit, $request->rejection_reason);

            return back()->with('success', sprintf(
                'Deposit of %s %s rejected for %s',
                $deposit->currency ?? 'BNB',
                number_format((float) $deposit->amount, 2),
                $deposit->user->name
            ));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to reject deposit: ' . $e->getMessage()]);
        }
    }
}

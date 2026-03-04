<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Services\WithdrawalService;
use App\Services\OTPService;
use App\Services\WalletService;
use App\Models\Withdrawal;

class WithdrawalController extends Controller
{
    public function __construct(
        protected WithdrawalService $withdrawalService,
        protected OTPService $otpService,
        protected WalletService $walletService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $walletSummary = $this->walletService->getWalletSummary($user);
        $withdrawalHistory = $this->withdrawalService->getWithdrawalHistory($user);

        return view('user.withdrawal.index', [
            'wallet' => $walletSummary,
            'withdrawals' => $withdrawalHistory,
            'minWithdrawal' => WithdrawalService::MINIMUM_WITHDRAWAL,
            'deductionPercent' => WithdrawalService::WITHDRAWAL_DEDUCTION_PERCENT,
        ]);
    }

    public function initiate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10']
        ]);

        try {
            $user = auth()->user();
            $withdrawal = $this->withdrawalService->initiateWithdrawal($user, (float) $validated['amount']);

            // Generate OTP
            $otp = $this->otpService->generateOTP($user, 'withdrawal');

            // Return OTP Modal response
            return response()->json([
                'success' => true,
                'withdrawal_id' => $withdrawal->id,
                'message' => 'OTP sent. Please enter to confirm.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function verifyOTP(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'withdrawal_id' => ['required', 'exists:withdrawals,id'],
            'otp' => ['required', 'string'],
        ]);

        try {
            $user = auth()->user();
            $withdrawal = Withdrawal::where('id', $validated['withdrawal_id'])
                ->where('user_id', $user->id)
                ->first();

            if (!$withdrawal) {
                throw new \Exception('Withdrawal not found');
            }

            $verified = $this->withdrawalService->verifyOTPAndLockFunds($withdrawal, $validated['otp']);

            if (!$verified) {
                return response()->json(['error' => 'Invalid OTP'], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP verified. Withdrawal pending admin approval.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function cancel(Withdrawal $withdrawal): RedirectResponse
    {
        $user = auth()->user();

        if ($withdrawal->user_id !== $user->id) {
            abort(403);
        }

        if ($withdrawal->status === 'pending_otp') {
            $withdrawal->update(['status' => 'cancelled']);
            return redirect()->back()->with('success', 'Withdrawal cancelled.');
        }

        return redirect()->back()->withErrors(['error' => 'Cannot cancel this withdrawal']);
    }

    public function calculator(Request $request): JsonResponse
    {
        $amount = (float) ($request->query('amount') ?? 0);
        $deduction = $this->withdrawalService->calculateDeduction($amount);
        $netAmount = $amount - $deduction;

        return response()->json([
            'amount' => $amount,
            'deduction' => $deduction,
            'net_amount' => $netAmount,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\Admin\AdminFundService;
use Illuminate\Http\Request;

class FundManagementController extends Controller
{
    public function index()
    {
        $stats = AdminFundService::getFundStats();

        return view('admin.fund-management.index', compact('stats'));
    }

    public function addFunds(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        AdminFundService::addFundsToWallet($user, auth()->user(), $request->amount, $request->reason);

        return back()->with('success', 'Funds added successfully.');
    }

    public function deductFunds(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|min:10',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        AdminFundService::deductFundsFromWallet($user, auth()->user(), $request->amount, $request->reason);

        return back()->with('success', 'Funds deducted successfully.');
    }

    public function convertSuspicious(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|min:10',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        AdminFundService::addFundsToWallet($user, auth()->user(), $request->amount, $request->reason);

        return back()->with('success', 'Suspicious funds successfully.');
    }

    public function showLedger($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $ledger = AdminFundService::getLedgerForUser($user);

        return view('admin.fund-management.ledger', compact('user', 'ledger'));
    }

    public function depositsList(Request $request)
    {
        $query = Deposit::with('user')->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by network type (normal or admin)
        if ($request->filled('network')) {
            $query->where('network', $request->network);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search by tx_hash or user email/name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tx_hash', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        $deposits = $query->paginate(15);

        return view('admin.deposits.index', compact('deposits'));
    }
}

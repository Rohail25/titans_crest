<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\AdminUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,banned,suspended',
            'sort' => 'nullable|in:id,name,email,status,created_at',
            'direction' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:5|max:200',
        ]);

        $perPage = $validated['per_page'] ?? 15;

        $users = AdminUserService::getFilteredUsers($validated, $perPage);

        $stats = AdminUserService::getUserStats();

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function show($id)
    {
        $user = AdminUserService::getUserById($id);
        $detailMetrics = AdminUserService::getUserDetailMetrics($user);
        $referralNetwork = AdminUserService::getReferralNetwork($user);

        return view('admin.users.show', [
            'user' => $user,
            'recentDeposits' => $detailMetrics['recentDeposits'],
            'recentWithdrawals' => $detailMetrics['recentWithdrawals'],
            'recentEarnings' => $detailMetrics['recentEarnings'],
            'earningsSummary' => $detailMetrics['earningsSummary'],
            'referralNetwork' => $referralNetwork,
        ]);
    }

    public function editEmail($id)
    {
        $user = User::where('role', 'user')->findOrFail($id);

        return view('admin.users.edit-email', compact('user'));
    }

    public function updateEmail(Request $request, $id)
    {
        $user = User::where('role', 'user')->findOrFail($id);

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $email = strtolower(trim($validated['email']));

        if ($email === strtolower((string) $user->email)) {
            return redirect()->route('admin.users.show', $id)->with('success', 'Email is already up to date.');
        }

        AdminUserService::updateUserEmail($user, Auth::user(), $email);

        return redirect()->route('admin.users.show', $id)->with('success', 'User email updated successfully.');
    }

    public function ban(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $user = User::findOrFail($id);

        AdminUserService::banUser($user, Auth::user(), $request->reason);

        return redirect()->route('admin.users.show', $id)->with('success', 'User banned successfully.');
    }

    public function activate($id)
    {
        $user = User::findOrFail($id);

        AdminUserService::activateUser($user, Auth::user());

        return redirect()->route('admin.users.show', $id)->with('success', 'User activated successfully.');
    }

    public function addCredit(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|min:10',
        ]);

        $user = User::findOrFail($id);

        AdminUserService::addManualCredit($user, Auth::user(), $request->amount, $request->reason);

        return redirect()->route('admin.users.show', $id)->with('success', 'Manual credit added successfully.');
    }
}

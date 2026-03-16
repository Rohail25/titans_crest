<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,banned,suspended',
            'sort' => 'nullable|in:id,name,email,status,created_at',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $users = AdminUserService::getFilteredUsers($validated);

        $stats = AdminUserService::getUserStats();

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function show($id)
    {
        $user = AdminUserService::getUserById($id);

        return view('admin.users.show', compact('user'));
    }

    public function ban(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $user = \App\Models\User::findOrFail($id);

        AdminUserService::banUser($user, Auth::user(), $request->reason);

        return redirect()->route('admin.users.show', $id)->with('success', 'User banned successfully.');
    }

    public function activate($id)
    {
        $user = \App\Models\User::findOrFail($id);

        AdminUserService::activateUser($user, Auth::user());

        return redirect()->route('admin.users.show', $id)->with('success', 'User activated successfully.');
    }

    public function addCredit(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|min:10',
        ]);

        $user = \App\Models\User::findOrFail($id);

        AdminUserService::addManualCredit($user, Auth::user(), $request->amount, $request->reason);

        return redirect()->route('admin.users.show', $id)->with('success', 'Manual credit added successfully.');
    }
}

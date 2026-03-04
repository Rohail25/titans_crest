@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="page-title">
    <i class="fas fa-history"></i>
    Audit Logs
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <select name="action" class="form-select" style="max-width: 200px;">
                <option value="">All Actions</option>
                <option value="approve_withdrawal" {{ request('action') === 'approve_withdrawal' ? 'selected' : '' }}>Approve Withdrawal</option>
                <option value="reject_withdrawal" {{ request('action') === 'reject_withdrawal' ? 'selected' : '' }}>Reject Withdrawal</option>
                <option value="ban_user" {{ request('action') === 'ban_user' ? 'selected' : '' }}>Ban User</option>
                <option value="activate_user" {{ request('action') === 'activate_user' ? 'selected' : '' }}>Activate User</option>
                <option value="manual_credit" {{ request('action') === 'manual_credit' ? 'selected' : '' }}>Manual Credit</option>
                <option value="update_setting" {{ request('action') === 'update_setting' ? 'selected' : '' }}>Update Setting</option>
                <option value="update_package" {{ request('action') === 'update_package' ? 'selected' : '' }}>Update Package</option>
            </select>
            <select name="target_type" class="form-select" style="max-width: 150px;">
                <option value="">All Targets</option>
                <option value="Withdrawal" {{ request('target_type') === 'Withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                <option value="User" {{ request('target_type') === 'User' ? 'selected' : '' }}>User</option>
                <option value="Setting" {{ request('target_type') === 'Setting' ? 'selected' : '' }}>Setting</option>
                <option value="Package" {{ request('target_type') === 'Package' ? 'selected' : '' }}>Package</option>
                <option value="Wallet" {{ request('target_type') === 'Wallet' ? 'selected' : '' }}>Wallet</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary">Clear</a>
        </form>
    </div>
</div>

<!-- Audit Logs Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Admin Actions
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Target</th>
                    <th>Changes</th>
                    <th>Timestamp</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td><strong>{{ $log->admin->name }}</strong></td>
                        <td>
                            <span class="badge badge-success">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $log->target_type }}</small><br>
                            <code style="font-size: 0.8rem;">#{{ $log->target_id }}</code>
                        </td>
                        <td>
                            @if($log->old_values || $log->new_values)
                                <span class="badge badge-info">Modified</span>
                            @else
                                <span class="badge badge-secondary">No changes</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $log->created_at->format('M d, Y H:i') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No audit logs found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

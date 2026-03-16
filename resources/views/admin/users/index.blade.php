@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="page-title">
    <i class="fas fa-users"></i>
    User Management
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Total Users</div>
            <div class="kpi-value">{{ $stats['total_users'] }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Active</div>
            <div class="kpi-value text-success">{{ $stats['active_users'] }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Banned</div>
            <div class="kpi-value text-danger">{{ $stats['banned_users'] }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Suspended</div>
            <div class="kpi-value text-warning">{{ $stats['suspended_users'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="kpi-label">Total Wallet Balance</div>
            <div class="kpi-value">${{ number_format($stats['total_wallet_balance'], 2) }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email, referral code" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort By</label>
                <select name="sort" class="form-select">
                    <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>Date</option>
                    <option value="id" {{ request('sort') === 'id' ? 'selected' : '' }}>ID</option>
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                    <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Status</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Order</label>
                <select name="direction" class="form-select">
                    <option value="desc" {{ request('direction', 'desc') === 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary grow">
                    <i class="fas fa-search"></i> Apply
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Users
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Balance</th>
                    <th>Total Earned</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td><small>{{ $user->email }}</small></td>
                        <td>
                            <span class="badge badge-{{ $user->status === 'active' ? 'success' : ($user->status === 'banned' ? 'danger' : 'warning') }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td>${{ number_format($user->wallet->balance ?? 0, 2) }}</td>
                        <td>${{ number_format($user->wallet->total_earned ?? 0, 2) }}</td>
                        <td><small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small></td>
                        <td>
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No users found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body" style="border-top: 1px solid rgba(212, 175, 55, 0.1);">
        {{ $users->links() }}
    </div>
</div>
@endsection

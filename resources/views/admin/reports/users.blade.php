@extends('layouts.admin')

@section('title', 'User Reports')

@section('content')
<div class="page-title">
    <i class="fas fa-users"></i>
    User Reports
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">Filter Users</div>
    <div class="card-body">
        <form action="{{ route('admin.reports.users') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-3 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.reports.users') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <a href="{{ route('admin.reports.users', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Users</div>
            <div class="kpi-value">{{ $users->count() }}</div>
            <small class="text-muted">registered users</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Active Users</div>
            <div class="kpi-value">{{ $users->where('status', 'active')->count() }}</div>
            <small class="text-muted">currently active</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Invested</div>
            <div class="kpi-value">${{ number_format($users->sum(function($user) { return $user->userPackages->sum('package.price') ?? 0; }), 2) }}</div>
            <small class="text-muted">total investment</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Avg Investment</div>
            <div class="kpi-value">${{ number_format($users->count() > 0 ? $users->sum(function($user) { return $user->userPackages->sum('package.price') ?? 0; }) / $users->count() : 0, 2) }}</div>
            <small class="text-muted">per user</small>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <span>Users ({{ $users->count() }} records)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Package</th>
                    <th>Total Investment</th>
                    <th>Total Earnings</th>
                    <th>Balance</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong><br>
                            <small class="text-muted">ID: {{ $user->id }}</small>
                        </td>
                        <td>
                            <small>{{ $user->email }}</small>
                        </td>
                        <td>
                            @php
                                $statusClass = match($user->status ?? 'active') {
                                    'active' => 'success',
                                    'inactive' => 'secondary',
                                    'banned' => 'danger',
                                    'suspended' => 'warning',
                                    default => 'secondary'
                                };
                                $statusIcon = match($user->status ?? 'active') {
                                    'active' => 'check-circle',
                                    'inactive' => 'minus-circle',
                                    'banned' => 'ban',
                                    'suspended' => 'pause-circle',
                                    default => 'question-circle'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">
                                <i class="fas fa-{{ $statusIcon }}"></i> {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </td>
                        <td>
                            @if($user->userPackages && $user->userPackages->count() > 0)
                                @foreach($user->userPackages as $userPackage)
                                    <span class="badge bg-primary">
                                        {{ $userPackage->package->name ?? 'Unknown' }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted">No package</span>
                            @endif
                        </td>
                        <td>
                            <strong>${{ number_format($user->userPackages->sum(function($up) { return $up->package->price ?? 0; }), 2) }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                ${{ number_format($user->earnings->sum('amount') ?? 0, 2) }}
                            </span>
                        </td>
                        <td>
                            @if($user->wallet)
                                ${{ number_format($user->wallet->balance ?? 0, 2) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            {{ $user->created_at->format('M d, Y') }}<br>
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox"></i> No users found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- User Statistics by Status -->
@if($users->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <span>User Statistics by Status</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Total Investment</th>
                    <th>Total Earnings</th>
                    <th>Average Investment</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users->groupBy(function($item) { return $item->status ?? 'active'; }) as $status => $statusUsers)
                    <tr>
                        <td>
                            @php
                                $statusClass = match($status) {
                                    'active' => 'success',
                                    'inactive' => 'secondary',
                                    'banned' => 'danger',
                                    'suspended' => 'warning',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td>{{ $statusUsers->count() }}</td>
                        <td>${{ number_format($statusUsers->sum(function($user) { return $user->userPackages->sum(function($up) { return $up->package->price ?? 0; }); }), 2) }}</td>
                        <td>${{ number_format($statusUsers->sum(function($user) { return $user->earnings->sum('amount') ?? 0; }), 2) }}</td>
                        <td>${{ number_format($statusUsers->count() > 0 ? $statusUsers->sum(function($user) { return $user->userPackages->sum(function($up) { return $up->package->price ?? 0; }); }) / $statusUsers->count() : 0, 2) }}</td>
                        <td>
                            {{ number_format(($statusUsers->count() / $users->count()) * 100, 2) }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

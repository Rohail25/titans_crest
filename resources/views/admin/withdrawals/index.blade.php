@extends('layouts.admin')

@section('title', 'Manage Withdrawals')

@section('content')
    <div class="page-title">
        <i class="fas fa-coins"></i>
        Manage Withdrawals
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-label">Pending Approvals</div>
                <div class="kpi-value">{{ $stats['pending_count'] }}</div>
                <small class="text-warning">
                    <i class="fas fa-exclamation-circle"></i>
                    ${{ number_format($stats['pending_total'], 2) }}
                </small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-label">Approved Today</div>
                <div class="kpi-value">{{ $stats['approved_today'] }}</div>
                <small class="text-success">
                    <i class="fas fa-check"></i>
                    ${{ number_format($stats['approved_today_total'], 2) }}
                </small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-label">Rejected Today</div>
                <div class="kpi-value">{{ $stats['rejected_today'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> Filter Withdrawals
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.withdrawals.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="User name or email"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending_otp" {{ request('status') === 'pending_otp' ? 'selected' : '' }}>Pending OTP
                        </option>
                        <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>
                            Pending Approval</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>
                            Date</option>
                        <option value="id" {{ request('sort') === 'id' ? 'selected' : '' }}>ID</option>
                        <option value="net_amount" {{ request('sort') === 'net_amount' ? 'selected' : '' }}>Amount</option>
                        <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Status</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Per Page</label>
                    <select name="per_page" class="form-select">
                        @foreach ([15, 25, 50, 100] as $count)
                            <option value="{{ $count }}" {{ request('per_page', 15) == $count ? 'selected' : '' }}>
                                {{ $count }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Order</label>
                    <select name="direction" class="form-select">
                        <option value="desc" {{ request('direction', 'desc') === 'desc' ? 'selected' : '' }}>Descending
                        </option>
                        <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Apply
                    </button>
                    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- All Withdrawals Table -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> All Withdrawals
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Amount (Net)</th>
                        <th>Fee (5%)</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                        <tr>
                            <td>
                                <strong>{{ $withdrawal->user->name }}</strong><br>
                                <small class="text-muted">{{ $withdrawal->user->email }}</small>
                            </td>
                            <td>${{ number_format($withdrawal->net_amount, 2) }}</td>
                            <td>${{ number_format($withdrawal->deduction_amount, 2) }}</td>
                            <td>
                                @php
                                    $status = $withdrawal->status;
                                    $statusClass = match ($status) {
                                        'pending_otp' => 'info',
                                        'pending_approval' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'cancelled' => 'secondary',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ optional($withdrawal->created_at)->format('M d, Y H:i') ?? 'N/A' }}
                                </small>
                            </td>
                            <td>
                                <a href="{{ route('admin.withdrawals.show', $withdrawal->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No withdrawals found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="card-body" style="border-top: 1px solid rgba(212, 175, 55, 0.1);">
            {{ $withdrawals->links() }}
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Daily Reports')

@section('content')
<div class="page-title">
    <i class="fas fa-calendar-day"></i>
    Daily Reports
</div>

<!-- Date Selection -->
<div class="card mb-4">
    <div class="card-header">Select Date</div>
    <div class="card-body">
        <form action="{{ route('admin.reports.daily') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ request('date', $date) }}">
            </div>

            <div class="col-md-6 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-filter"></i> Show Report
                </button>
                <a href="{{ route('admin.reports.daily', ['date' => now()->toDateString()]) }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Today
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Report Date -->
<div class="alert alert-info">
    <strong>Showing report for:</strong> {{ \Carbon\Carbon::parse($date)->format('F d, Y (l)') }}
</div>

<!-- Main Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Users Active</div>
            <div class="kpi-value">{{ $stats['active_users_count'] ?? 0 }}</div>
            <small class="text-muted">on this date</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Profits Distributed</div>
            <div class="kpi-value">${{ number_format($stats['total_profits'] ?? 0, 2) }}</div>
            <small class="text-muted">daily earnings</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Deposits</div>
            <div class="kpi-value">${{ number_format($stats['total_deposits'] ?? 0, 2) }}</div>
            <small class="text-muted">{{ $stats['deposit_count'] ?? 0 }} transactions</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Withdrawals</div>
            <div class="kpi-value">${{ number_format($stats['total_withdrawals'] ?? 0, 2) }}</div>
            <small class="text-muted">{{ $stats['withdrawal_count'] ?? 0 }} transactions</small>
        </div>
    </div>
</div>

<!-- Revenue & Operations -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Financial Summary</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-muted small">Total Inflow</div>
                        <div class="h5 mb-0">
                            ${{ number_format(($stats['total_deposits'] ?? 0), 2) }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Total Outflow</div>
                        <div class="h5 mb-0">
                            ${{ number_format(($stats['total_withdrawals'] ?? 0), 2) }}
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-6">
                        <div class="text-muted small">Net Flow</div>
                        @php
                            $netFlow = ($stats['total_deposits'] ?? 0) - ($stats['total_withdrawals'] ?? 0);
                        @endphp
                        <div class="h5 mb-0">
                            <span class="{{ $netFlow >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $netFlow >= 0 ? '+' : '' }}${{ number_format($netFlow, 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Profits Distributed</div>
                        <div class="h5 mb-0 text-info">
                            ${{ number_format($stats['total_profits'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Status Overview</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">New Users</span>
                        <span class="badge bg-primary">{{ $stats['new_users_count'] ?? 0 }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ isset($stats['active_users_count']) && $stats['active_users_count'] > 0 ? min(100, (($stats['new_users_count'] ?? 0) / $stats['active_users_count']) * 100) : 0 }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Pending Withdrawals</span>
                        <span class="badge bg-warning">{{ $stats['pending_withdrawals_count'] ?? 0 }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: {{ isset($stats['withdrawal_count']) && $stats['withdrawal_count'] > 0 ? min(100, (($stats['pending_withdrawals_count'] ?? 0) / $stats['withdrawal_count']) * 100) : 0 }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Pending Deposits</span>
                        <span class="badge bg-info">{{ $stats['pending_deposits_count'] ?? 0 }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ isset($stats['deposit_count']) && $stats['deposit_count'] > 0 ? min(100, (($stats['pending_deposits_count'] ?? 0) / $stats['deposit_count']) * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Statistics -->
<div class="card">
    <div class="card-header">Detailed Breakdown</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th class="text-end">Value</th>
                    <th class="text-end">Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <i class="fas fa-users text-primary"></i>
                        Active Users
                    </td>
                    <td class="text-end">
                        <strong>{{ $stats['active_users_count'] ?? 0 }}</strong>
                    </td>
                    <td class="text-end">-</td>
                </tr>
                <tr>
                    <td>
                        <i class="fas fa-user-plus text-success"></i>
                        New Users Registered
                    </td>
                    <td class="text-end">
                        <strong>{{ $stats['new_users_count'] ?? 0 }}</strong>
                    </td>
                    <td class="text-end">
                        {{ isset($stats['active_users_count']) && $stats['active_users_count'] > 0 ? number_format((($stats['new_users_count'] ?? 0) / $stats['active_users_count']) * 100, 2) : 0 }}%
                    </td>
                </tr>
                <tr>
                    <td>
                        <i class="fas fa-dollar-sign text-success"></i>
                        Total Deposits
                    </td>
                    <td class="text-end">
                        <strong>${{ number_format($stats['total_deposits'] ?? 0, 2) }}</strong>
                    </td>
                    <td class="text-end">
                        {{ isset($stats['total_deposits'], $stats['total_withdrawals']) ? number_format((($stats['total_deposits'] ?? 0) / (($stats['total_deposits'] ?? 0) + ($stats['total_withdrawals'] ?? 0))) * 100, 2) : 0 }}%
                    </td>
                </tr>
                <tr>
                    <td>
                        <i class="fas fa-hourglass-half text-warning"></i>
                        Pending Deposits
                    </td>
                    <td class="text-end">
                        <strong>${{ number_format($stats['pending_deposits_amount'] ?? 0, 2) }}</strong>
                    </td>
                    <td class="text-end">
                        {{ isset($stats['deposit_count']) && $stats['deposit_count'] > 0 ? number_format((($stats['pending_deposits_count'] ?? 0) / $stats['deposit_count']) * 100, 2) : 0 }}%
                    </td>
                </tr>
                <tr>
                    <td>
                        <i class="fas fa-check-circle text-success"></i>
                        Approved Deposits
                    </td>
                    <td class="text-end">
                        <strong>${{ number_format($stats['approved_deposits_amount'] ?? 0, 2) }}</strong>
                    </td>
                    <td class="text-end">
                        {{ isset($stats['deposit_count']) && $stats['deposit_count'] > 0 ? number_format(((($stats['deposit_count'] ?? 0) - ($stats['pending_deposits_count'] ?? 0)) / $stats['deposit_count']) * 100, 2) : 0 }}%
                    </td>
                </tr>
                <tr>
                    <td>
                        <i class="fas fa-money-bill-wave text-info"></i>
                        Total Profits Distributed
                    </td>
                    <td class="text-end">
                        <strong>${{ number_format($stats['total_profits'] ?? 0, 2) }}</strong>
                    </td>
                    <td class="text-end">-</td>
                </tr>
                <tr>
                    <td>
                        <i class="fas fa-cash-register text-danger"></i>
                        Total Withdrawals
                    </td>
                    <td class="text-end">
                        <strong>${{ number_format($stats['total_withdrawals'] ?? 0, 2) }}</strong>
                    </td>
                    <td class="text-end">
                        {{ isset($stats['total_deposits'], $stats['total_withdrawals']) ? number_format((($stats['total_withdrawals'] ?? 0) / (($stats['total_deposits'] ?? 0) + ($stats['total_withdrawals'] ?? 0))) * 100, 2) : 0 }}%
                    </td>
                </tr>
                <tr>
                    <td>
                        <i class="fas fa-asterisk text-warning"></i>
                        Pending Withdrawals
                    </td>
                    <td class="text-end">
                        <strong>${{ number_format($stats['pending_withdrawals_amount'] ?? 0, 2) }}</strong>
                    </td>
                    <td class="text-end">
                        {{ isset($stats['withdrawal_count']) && $stats['withdrawal_count'] > 0 ? number_format((($stats['pending_withdrawals_count'] ?? 0) / $stats['withdrawal_count']) * 100, 2) : 0 }}%
                    </td>
                </tr>
                <tr>
                    <td>
                        <i class="fas fa-check-double text-success"></i>
                        Approved Withdrawals
                    </td>
                    <td class="text-end">
                        <strong>${{ number_format($stats['approved_withdrawals_amount'] ?? 0, 2) }}</strong>
                    </td>
                    <td class="text-end">
                        {{ isset($stats['withdrawal_count']) && $stats['withdrawal_count'] > 0 ? number_format(((($stats['withdrawal_count'] ?? 0) - ($stats['pending_withdrawals_count'] ?? 0)) / $stats['withdrawal_count']) * 100, 2) : 0 }}%
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

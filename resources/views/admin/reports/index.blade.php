@extends('layouts.admin')

@section('title', 'Reports & Analytics')

@section('content')
<div class="page-title">
    <i class="fas fa-file-alt"></i>
    Reports & Analytics
</div>

<!-- System Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Users</div>
            <div class="kpi-value">{{ $stats['total_users'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Deposits</div>
            <div class="kpi-value">${{ number_format($stats['total_deposits'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Earnings</div>
            <div class="kpi-value">${{ number_format($stats['total_earnings'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Pending Withdrawals</div>
            <div class="kpi-value">{{ $stats['pending_withdrawals'] }}</div>
        </div>
    </div>
</div>

<!-- Report Links -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">User Report</div>
            <div class="card-body">
                <p class="text-muted">Generate report of all users with filters</p>
                <form method="GET" action="{{ route('admin.reports.users') }}">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="banned">Banned</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">From</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">To</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-eye"></i> View Report
                    </button>
                    <button type="submit" name="export" value="1" class="btn btn-secondary w-100">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">Deposit Report</div>
            <div class="card-body">
                <p class="text-muted">View all deposits with filtering</p>
                <form method="GET" action="{{ route('admin.reports.deposits') }}">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">From</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">To</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-eye"></i> View Report
                    </button>
                    <button type="submit" name="export" value="1" class="btn btn-secondary w-100">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">Withdrawal Report</div>
            <div class="card-body">
                <p class="text-muted">View all withdrawals</p>
                <form method="GET" action="{{ route('admin.reports.withdrawals') }}">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="pending_approval">Pending Approval</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">From</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">To</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-eye"></i> View Report
                    </button>
                    <button type="submit" name="export" value="1" class="btn btn-secondary w-100">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">Daily Report</div>
            <div class="card-body">
                <p class="text-muted">View metrics for a specific date</p>
                <form method="GET" action="{{ route('admin.reports.daily') }}">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-eye"></i> View Daily Report
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

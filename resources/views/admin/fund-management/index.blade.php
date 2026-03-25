@extends('layouts.admin')

@section('title', 'Fund Management')

@section('content')
<div class="page-title">
    <i class="fas fa-wallet"></i>
    Fund Management
    <a href="{{ route('admin.fund-management.deposits') }}" class="btn btn-info btn-sm float-end">
        <i class="fas fa-list"></i> View All Deposits
    </a>
</div>

<!-- Fund Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="kpi-label">Total Balance</div>
            <div class="kpi-value">${{ number_format($stats['total_balance'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Pending</div>
            <div class="kpi-value">${{ number_format($stats['total_pending'], 2) }}</div>
        </div>
    </div>
    {{-- <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Suspicious</div>
            <div class="kpi-value">${{ number_format($stats['total_suspicious'], 2) }}</div>
        </div>
    </div> --}}
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Earned</div>
            <div class="kpi-value">${{ number_format($stats['total_earnings'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Deposits</div>
            <div class="kpi-value">${{ number_format($stats['total_deposits'], 2) }}</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Add Funds -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header text-success">
                <i class="fas fa-plus-circle"></i> Add Funds to User
            </div>
            <div class="card-body">
                <form action="{{ route('admin.fund-management.add') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select User...</option>
                            @foreach(\App\Models\User::where('role', 'user')->orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}" class="text-black">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" step="0.01" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="2" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-plus"></i> Add Funds
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Deduct Funds -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header text-danger">
                <i class="fas fa-minus-circle"></i> Deduct Funds from User
            </div>
            <div class="card-body">
                <form action="{{ route('admin.fund-management.deduct') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select User...</option>
                            @foreach(\App\Models\User::where('role', 'user')->orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}" class="text-black">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" step="0.01" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="2" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-minus"></i> Deduct Funds
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Convert Suspicious -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header text-warning">
                <i class="fas fa-exchange-alt"></i> Convert Suspicious Funds
            </div>
            <div class="card-body">
                <form action="{{ route('admin.fund-management.convert') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select User...</option>
                            @foreach(\App\Models\User::where('role', 'user')->orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}" class="text-black">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" step="0.01" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="2" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-exchange-alt"></i> Convert
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- View Ledger -->
    {{-- <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">View User Ledger</div>
            <div class="card-body">
                <form action="{{ route('admin.fund-management.ledger', 0) }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">Select User to View Ledger...</option>
                            @foreach(\App\Models\User::where('role', 'user')->orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
</div>
@endsection

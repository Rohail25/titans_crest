@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="page-title">
    <i class="fas fa-user-circle"></i>
    User: {{ $user->name }}
</div>

<div class="row mb-4">
    <!-- User Info -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">Account Information</div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Status:</strong> <span class="badge badge-{{ $user->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($user->status) }}</span></p>
                <p><strong>Referral Code:</strong> <code>{{ $user->referral_code }}</code></p>
                <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y H:i') }}</p>
                @if($user->banned_at)
                    <p><strong>Ban Reason:</strong> {{ $user->ban_reason }}</p>
                    <p><strong>Banned At:</strong> {{ $user->banned_at->format('M d, Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Wallet Info -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">Wallet Status</div>
            <div class="card-body">
                <p><strong>Balance:</strong> <span class="text-success">${{ number_format($user->wallet->balance, 2) }}</span></p>
                <p><strong>Pending Balance:</strong> ${{ number_format($user->wallet->pending_balance, 2) }}</p>
                <p><strong>Suspicious Balance:</strong> ${{ number_format($user->wallet->suspicious_balance, 2) }}</p>
                <p><strong>Total Earned:</strong> ${{ number_format($user->wallet->total_earned, 2) }}</p>
                <p><strong>Total Deposited:</strong> ${{ number_format($user->wallet->total_deposit, 2) }}</p>
                <p><strong>3x Cap Progress:</strong> {{ number_format(($user->wallet->total_earned / max(1, $user->wallet->total_deposit * 3)) * 100, 1) }}%</p>
            </div>
        </div>
    </div>
</div>

<!-- Deposits -->
<div class="card mb-4">
    <div class="card-header">Recent Deposits</div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>TX Hash</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user->deposits()->latest()->take(5)->get() as $deposit)
                    <tr>
                        <td>${{ number_format($deposit->amount, 2) }}</td>
                        <td><span class="badge badge-{{ $deposit->status === 'confirmed' ? 'success' : 'warning' }}">{{ ucfirst($deposit->status) }}</span></td>
                        <td><small>{{ $deposit->tx_hash }}</small></td>
                        <td>{{ $deposit->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Withdrawals -->
<div class="card mb-4">
    <div class="card-header">Recent Withdrawals</div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user->withdrawals()->latest()->take(5)->get() as $withdrawal)
                    <tr>
                        <td>${{ number_format($withdrawal->net_amount, 2) }}</td>
                        <td><span class="badge badge-{{ $withdrawal->status === 'approved' ? 'success' : 'warning' }}">{{ ucfirst(str_replace('_', ' ', $withdrawal->status)) }}</span></td>
                        <td>{{ $withdrawal->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Actions -->
<div class="row mb-4">
    @if($user->status === 'active')
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header text-danger">Ban User</div>
                <div class="card-body">
                    <form action="{{ route('admin.users.ban', $user->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-ban"></i> Ban User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header text-success">Activate User</div>
                <div class="card-body">
                    <p class="text-muted">Reactivate this user account</p>
                    <form action="{{ route('admin.users.activate', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check"></i> Activate User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Add Manual Credit</div>
            <div class="card-body">
                <form action="{{ route('admin.users.add-credit', $user->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" step="0.01" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="2" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus"></i> Add Credit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Users
</a>
@endsection

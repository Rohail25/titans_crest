@extends('layouts.admin')

@section('title', 'Review Withdrawal')

@section('content')
<div class="page-title">
    <i class="fas fa-coins"></i>
    Review Withdrawal #{{ $withdrawal->id }}
</div>

<!-- User Info -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">User Information</div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $withdrawal->user->name }}</p>
                <p><strong>Email:</strong> {{ $withdrawal->user->email }}</p>
                <p><strong>Status:</strong> <span class="badge badge-{{ $withdrawal->user->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst($withdrawal->user->status) }}</span></p>
                <p><strong>Member Since:</strong> {{ $withdrawal->user->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Withdrawal Details -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Withdrawal Details</div>
            <div class="card-body">
                <p><strong>Status:</strong> <span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $withdrawal->status)) }}</span></p>
                <p><strong>Requested Amount:</strong> ${{ number_format($withdrawal->requested_amount, 2) }}</p>
                <p><strong>Fee (5%):</strong> -${{ number_format($withdrawal->deduction_amount, 2) }}</p>
                <p><strong>Net Amount:</strong> <strong class="text-success">${{ number_format($withdrawal->net_amount, 2) }}</strong></p>
                <p><strong>Wallet Address:</strong><br>
                    <code class="text-info">{{ $withdrawal->wallet_address ?? 'Not provided' }}</code>
                </p>
                @if($withdrawal->tx_hash)
                    <p><strong>Transaction Hash:</strong><br>
                        <code class="text-success">{{ $withdrawal->tx_hash }}</code>
                    </p>
                @endif
                <p><strong>Requested:</strong> {{ $withdrawal->created_at->format('M d, Y H:i') }}</p>
                @if($withdrawal->approved_at)
                    <p><strong>Approved:</strong> {{ $withdrawal->approved_at->format('M d, Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- User Wallet -->
<div class="card mb-4">
    <div class="card-header">User Wallet Status</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div>
                    <small class="text-muted">Balance</small><br>
                    <strong class="text-success">${{ number_format($withdrawal->user->wallet->balance, 2) }}</strong>
                </div>
            </div>
            <div class="col-md-3">
                <div>
                    <small class="text-muted">Total Earned</small><br>
                    <strong>${{ number_format($withdrawal->user->wallet->total_earned, 2) }}</strong>
                </div>
            </div>
            <div class="col-md-3">
                <div>
                    <small class="text-muted">Total Deposited</small><br>
                    <strong>${{ number_format($withdrawal->user->wallet->total_deposit, 2) }}</strong>
                </div>
            </div>
            <div class="col-md-3">
                <div>
                    <small class="text-muted">Cap Progress</small><br>
                    <strong>{{ number_format(($withdrawal->user->wallet->total_earned / max(1, $withdrawal->user->wallet->total_deposit * 3)) * 100, 1) }}%</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Withdrawal History -->
<div class="card mb-4">
    <div class="card-header">Recent Withdrawal History</div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Requested</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentWithdrawals as $w)
                    <tr>
                        <td>${{ number_format($w->net_amount, 2) }}</td>
                        <td><span class="badge badge-{{ $w->status === 'approved' ? 'success' : ($w->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst(str_replace('_', ' ', $w->status)) }}</span></td>
                        <td>{{ $w->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No withdrawal history</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Actions -->
{{-- <div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header text-success">
                <i class="fas fa-check-circle"></i> Approve Withdrawal
            </div>
            <div class="card-body">
                <p class="text-muted">Confirm this withdrawal and provide wallet details</p>
                <form action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">User's Wallet Address</label>
                        <input type="text" class="form-control" value="{{ $withdrawal->wallet_address }}" readonly>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Manually send BNB {{ number_format($withdrawal->net_amount, 2) }} to this address via MetaMask
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transaction Hash <span class="text-danger">*</span></label>
                        <input type="text" name="tx_hash" class="form-control" placeholder="Enter blockchain transaction hash" required>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> After sending funds via MetaMask, paste the transaction hash here
                        </small>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-check"></i> Confirm Payment & Approve
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header text-danger">
                <i class="fas fa-times-circle"></i> Reject Withdrawal
            </div>
            <div class="card-body">
                <p class="text-muted">Reject this request and refund the user</p>
                <form action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure?')">
                        <i class="fas fa-times"></i> Reject & Refund
                    </button>
                </form>
            </div>
        </div>
    </div>
</div> --}}

<a href="{{ route('admin.withdrawals.index') }}" class="btn btn-secondary mt-3">
    <i class="fas fa-arrow-left"></i> Back to List
</a>
@endsection

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

<div class="card mb-4">
    <div class="card-header">Referral Network</div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Total Levels</div>
                    <h5 class="mb-0">{{ $referralNetwork['summary']['total_levels'] }}</h5>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Total Referrals</div>
                    <h5 class="mb-0">{{ $referralNetwork['summary']['total_referrals'] }}</h5>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Network Deposit</div>
                    <h5 class="mb-0 text-success">${{ number_format($referralNetwork['summary']['total_network_deposit'], 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Network Earned</div>
                    <h5 class="mb-0 text-info">${{ number_format($referralNetwork['summary']['total_network_earned'], 2) }}</h5>
                </div>
            </div>
        </div>

        @forelse($referralNetwork['levels'] as $level => $members)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <strong>Level {{ $level }}</strong>
                    <span class="text-muted ms-2">{{ $members->count() }} referral(s)</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Join Date</th>
                                <th>Total Deposit</th>
                                <th>Total Earned</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                                <tr>
                                    <td><strong>#{{ $member->id }}</strong></td>
                                    <td>{{ $member->created_at->format('Y-m-d') }}</td>
                                    <td><strong>${{ number_format($member->total_deposit, 2) }}</strong></td>
                                    <td><strong>${{ number_format($member->total_earned, 2) }}</strong></td>
                                    <td>
                                        @if($member->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($member->status === 'banned')
                                            <span class="badge bg-danger">Banned</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($member->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">
                No referral network found for this user.
            </div>
        @endforelse
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
                @forelse($recentDeposits as $deposit)
                    <tr>
                        <td>${{ number_format($deposit->amount, 2) }}</td>
                        <td><span class="badge badge-{{ $deposit->status === 'confirmed' ? 'success' : 'warning' }}">{{ ucfirst($deposit->status) }}</span></td>
                        <td><small>{{ $deposit->tx_hash }}</small></td>
                        <td>{{ $deposit->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No deposits found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Earnings -->
<div class="card mb-4">
    <div class="card-header">Earnings Overview</div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="text-white small">Total Earnings</div>
                <h5 class="text-success">${{ number_format($earningsSummary['total_earnings'], 2) }}</h5>
            </div>
            <div class="col-md-3">
                <div class="text-white small">Profit Earnings</div>
                <h5 class="text-info">${{ number_format($earningsSummary['profit_earnings'], 2) }}</h5>
            </div>
            <div class="col-md-3">
                <div class="text-white small">Referral Commissions</div>
                <h5 class="text-warning">${{ number_format($earningsSummary['referral_earnings'], 2) }}</h5>
            </div>
            <div class="col-md-3">
                <div class="text-white small">Total Entries</div>
                <h5>{{ $earningsSummary['total_entries'] }}</h5>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Package</th>
                    <th>Metadata</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentEarnings as $earning)
                    <tr>
                        <td>
                            @if(in_array($earning->type, ['profit_share', 'daily_profit', 'roi_profit'], true))
                                <span class="badge bg-info"><i class="fas fa-calendar-day"></i> Profit</span>
                            @elseif(in_array($earning->type, ['referral', 'referral_commission'], true))
                                <span class="badge bg-warning"><i class="fas fa-users"></i> Referral</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $earning->type)) }}</span>
                            @endif
                        </td>
                        <td><strong class="text-success">${{ number_format($earning->amount, 2) }}</strong></td>
                        <td>
                            @if($earning->userPackage)
                                {{ $earning->userPackage->package->name ?? 'N/A' }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($earning->metadata)
                                <small class="text-muted">
                                    @if(isset($earning->metadata['multiplier']))
                                        Multiplier: {{ $earning->metadata['multiplier'] }}
                                    @endif
                                    @if(isset($earning->metadata['referral_level']))
                                        Level: {{ $earning->metadata['referral_level'] }}
                                    @endif
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $earning->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No earnings found</td>
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
                @forelse($recentWithdrawals as $withdrawal)
                    <tr>
                        <td>${{ number_format($withdrawal->net_amount, 2) }}</td>
                        <td><span class="badge badge-{{ $withdrawal->status === 'approved' ? 'success' : 'warning' }}">{{ ucfirst(str_replace('_', ' ', $withdrawal->status)) }}</span></td>
                        <td>{{ $withdrawal->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No withdrawals found</td>
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

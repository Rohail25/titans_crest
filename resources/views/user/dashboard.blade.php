@extends('layouts.user')

@section('page-title', 'Dashboard')

@section('content')
@if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

@if($errors->has('subscription'))
    <div class="alert alert-danger mb-4">
        {{ $errors->first('subscription') }}
    </div>
@endif

<div class="row mb-4">
    <!-- Available Balance Card (Primary) -->
    <div class="col-md-6 col-lg-3">
        <div class="stat-card primary">
            <div class="stat-card-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-card-label">Available Balance</div>
            <div class="stat-card-value">${{ number_format($wallet['balance'], 2) }}</div>
            <div class="stat-card-change">
                <i class="fas fa-arrow-up"></i> All funds available
            </div>
        </div>
    </div>

    <!-- Pending Balance -->
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-card-label">Pending Balance</div>
            <div class="stat-card-value">${{ number_format($wallet['pending_balance'], 2) }}</div>
            <div class="stat-card-change">
                {{ $deposits['pending_count'] ?? 0 }} pending confirmations
            </div>
        </div>
    </div>

    <!-- Total Deposit -->
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-piggy-bank"></i>
            </div>
            <div class="stat-card-label">Total Deposited</div>
            <div class="stat-card-value">${{ number_format($wallet['total_deposit'], 2) }}</div>
            <div class="stat-card-change">
                {{ $deposits['total_deposits'] ?? 0 }} successful deposits
            </div>
        </div>
    </div>

    <!-- Total Earned -->
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-card-label">Total Earned</div>
            <div class="stat-card-value">${{ number_format($wallet['total_earned'], 2) }}</div>
            <div class="stat-card-change positive">
                {{ number_format($profit['daily_profit'], 2) }}/day profit
            </div>
        </div>
    </div>
</div>

<!-- 3x Cap Progress and Active Package Info -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-target"></i> 3x Cap Progress</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="stat-card-label mb-2">Maximum Earnings</div>
                        <div class="stat-card-value">${{ number_format($wallet['cap_3x'], 2) }}</div>
                        <small class="text-muted">Deposit × 3</small>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card-label mb-2">Remaining to Earn</div>
                        <div class="stat-card-value {{ $wallet['cap_reached'] ? 'text-danger' : 'text-success' }}">
                            ${{ number_format($wallet['remaining_3x'], 2) }}
                        </div>
                        <small class="text-muted">
                            {{ $wallet['cap_reached'] ? 'Cap Reached! ✓' : 'Keep earning' }}
                        </small>
                    </div>
                </div>

                <div class="progress-bars mt-4">
                    <div class="progress-label">
                        <span>Progress</span>
                        <span>{{ number_format($wallet['cap_percentage'], 1) }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: {{ $wallet['cap_percentage'] }}%"></div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Earned: ${{ number_format($wallet['total_earned'], 2) }} / ${{ number_format($wallet['cap_3x'], 2) }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Package -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-box"></i> Active Package</h5>
            </div>
            <div class="card-body">
                @if(count($packages) > 0)
                    @php $package = $packages[0] @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <div class="stat-card-label mb-2">Package Name</div>
                            <h5>{{ $package['name'] }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card-label mb-2">Package Value</div>
                            <div class="h5">${{ number_format($package['price'], 2) }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="stat-card-label mb-2">Daily Profit Rate</div>
                            <h6 class="text-success">{{ ($package['daily_profit_rate'] * 100) }}%</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card-label mb-2">Daily Earnings</div>
                            <h6>${{ number_format($package['daily_profit'], 2) }}</h6>
                        </div>
                    </div>

                    <small class="text-muted">
                        Activated: {{ \Carbon\Carbon::parse($package['activated_at'])->format('M d, Y') }}
                    </small>
                @else
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-info-circle"></i> No active package. Subscribe from the plans below.
                    </div>

                    @if($availablePackages->count() > 0)
                        @foreach($availablePackages as $plan)
                            <div class="border rounded p-3 mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>{{ $plan->name }}</strong>
                                    <span class="badge badge-info">${{ number_format($plan->price, 2) }}</span>
                                </div>
                                <small class="text-muted d-block mb-2">
                                    Profit: {{ number_format($plan->daily_profit_rate * 100, 2) }}% daily
                                </small>
                                <form action="{{ route('user.package.subscribe', $plan->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm w-100" {{ $wallet['balance'] < $plan->price ? 'disabled' : '' }}>
                                        {{ $wallet['balance'] < $plan->price ? 'Insufficient Balance' : 'Subscribe Now' }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted">No active plans are available right now. Please contact support.</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Downline Count and Referral Stats -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Team Size</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <h1 class="text-primary">{{ $referrals['direct_referrals'] ?? 0 }}</h1>
                        <small class="text-muted">Direct Referrals</small>
                    </div>
                    <div class="col-md-4 text-center">
                        <h1 class="text-success">{{ $referrals['total_referrals'] ?? 0 }}</h1>
                        <small class="text-muted">Total Downline</small>
                    </div>
                    <div class="col-md-4 text-center">
                        <h1 class="text-info">${{ number_format($referrals['commission_earned'] ?? 0, 2) }}</h1>
                        <small class="text-muted">Commission Earned</small>
                    </div>
                </div>
                <a href="{{ route('user.referral') }}" class="btn btn-primary w-100 mt-3">View Referrals</a>
            </div>
        </div>
    </div>

    <!-- Withdrawal Stats -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-arrow-up"></i> Withdrawal Status</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="stat-card-label mb-2">Pending Withdrawals</div>
                        <h4 class="text-warning">{{ $withdrawals['pending_count'] ?? 0 }}</h4>
                        <small class="text-muted">${{ number_format($withdrawals['pending_amount'] ?? 0, 2) }}</small>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card-label mb-2">Total Withdrawn</div>
                        <h4 class="text-success">{{ $withdrawals['total_withdrawals'] ?? 0 }}</h4>
                        <small class="text-muted">${{ number_format($withdrawals['total_withdrawn'] ?? 0, 2) }}</small>
                    </div>
                </div>
                <a href="{{ route('user.withdrawal.index') }}" class="btn btn-outline-primary w-100 mt-3">Withdraw Funds</a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Earnings -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-history"></i> Recent Earnings</h5>
                <a href="{{ route('user.analytics') }}" class="btn btn-sm btn-outline-primary">View Analytics</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEarnings as $earning)
                                <tr>
                                    <td>{{ $earning->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $earning->type == 'deposit' ? 'info' : ($earning->type == 'profit_share' ? 'success' : 'primary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $earning->type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-success">+${{ number_format($earning->amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">Completed</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No earnings yet. <a href="{{ route('user.deposit.index') }}">Make your first deposit</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

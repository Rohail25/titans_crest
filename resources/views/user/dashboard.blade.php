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

@if($wallet['cap_reached'] && $latestCompletedPackage)
    <div class="alert alert-warning mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <div class="me-md-3 mb-3 mb-md-0">
            <strong>Your investment package has reached the maximum earning limit.</strong><br>
            Please subscribe to a new package to continue earning.
        </div>
        <a href="#available-plans" class="btn btn-primary">Subscribe Plan</a>
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

<!-- Earning Cap Progress and Active Package Info -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-target"></i> Earning Cap Progress</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="stat-card-label mb-2">Maximum Earnings</div>
                        <div class="stat-card-value">${{ number_format($wallet['cap_3x'], 2) }}</div>
                        <small class="text-muted">Based on active package cap rules</small>
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
                        Earned: ${{ number_format($wallet['earned_against_cap'] ?? 0, 2) }} / ${{ number_format($wallet['cap_3x'], 2) }}
                    </small>
                </div>

                @if($nextProfitTime && !$wallet['cap_reached'])
                    <hr>
                    <div class="stat-card-label mb-2">Next Profit Distribution In</div>
                    <h4 id="profitCountdown" class="text-primary mb-1">-- : -- : --</h4>
                    <small class="text-muted">Profit is distributed every 15 minutes after subscription activation.</small>
                    <div id="nextProfitTime" class="d-none">{{ \Carbon\Carbon::parse($nextProfitTime)->toIso8601String() }}</div>
                @endif
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
                    @foreach($packages as $package)
                        <div class="border rounded p-3 mb-3 bg-dark">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="stat-card-label mb-2">Package Name</div>
                                    <h5 class="text-white">{{ $package['name'] }}</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="stat-card-label mb-2">Package Value</div>
                                    <div class="h5 text-white">${{ number_format($package['price'], 2) }}</div>
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
                                    <h6 class="text-white">${{ number_format($package['daily_profit'], 2) }}</h6>
                                </div>
                            </div>

                            <small class="text-muted">
                                Activated: {{ \Carbon\Carbon::parse($package['activated_at'])->format('M d, Y') }}
                                @if(!empty($package['expires_at']))
                                    | Expires: {{ \Carbon\Carbon::parse($package['expires_at'])->format('M d, Y') }}
                                @endif
                            </small>
                        </div>
                    @endforeach

                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i> You currently have active package(s). You can subscribe to additional packages and earn from each.
                    </div>
                @else
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-info-circle"></i> No active package. Subscribe from the plans below.
                    </div>
                @endif

                @if($availablePackages->count() > 0)
                    <div id="available-plans"></div>
                    <h6 class="text-white mb-3"><i class="fas fa-list"></i> Available Plans</h6>
                    @foreach($availablePackages as $plan)
                        <div class="border rounded p-3 mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-white">{{ $plan->name }}</strong>
                                <span class="badge badge-info">${{ number_format($plan->price, 2) }}</span>
                            </div>
                            <small class="text-muted d-block mb-2">
                                Profit: {{ number_format($plan->daily_profit_rate , 2) }}% daily
                            </small>
                            <form action="{{ route('user.package.subscribe', $plan->id) }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm w-100"
                                    {{ $wallet['balance'] < $plan->price ? 'disabled' : '' }}
                                >
                                    @if($wallet['balance'] < $plan->price)
                                        Insufficient Balance
                                    @else
                                        Subscribe Now
                                    @endif
                                </button>
                            </form>
                        </div>
                    @endforeach
                @else
                    <div class="text-muted">No active plans are available right now. Please contact support.</div>
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

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Monthly Performance Excellence</h5>
        <div class="d-flex align-items-center gap-2">
            <small class="text-muted">Showing up to {{ $teamPerformance['summary']['levels_scanned'] ?? 100 }} levels</small>
            <a href="{{ route('user.team') }}" class="btn btn-sm btn-outline-primary">View Team Details</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="border rounded p-3 h-100">
                    <div class="stat-card-label mb-2">This Month Team Deposit</div>
                    <h4 class="text-primary mb-1">${{ number_format($teamPerformance['summary']['monthly_team_deposit'] ?? 0, 2) }}</h4>
                    <small class="text-muted">Confirmed deposits from your team this month</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="border rounded p-3 h-100">
                    <div class="stat-card-label mb-2">Total Team Deposit</div>
                    <h4 class="text-success mb-1">${{ number_format($teamPerformance['summary']['total_team_deposit'] ?? 0, 2) }}</h4>
                    <small class="text-muted">Combined confirmed deposits across levels</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="border rounded p-3 h-100">
                    <div class="stat-card-label mb-2">Direct Team Deposit</div>
                    <h4 class="text-info mb-1">${{ number_format($teamPerformance['summary']['direct_team_deposit'] ?? 0, 2) }}</h4>
                    <small class="text-muted">Level 1 confirmed deposits</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="border rounded p-3 h-100">
                    <div class="stat-card-label mb-2">Active Depositors</div>
                    <h4 class="text-warning mb-1">{{ $teamPerformance['summary']['team_with_deposit'] ?? 0 }}/{{ $teamPerformance['summary']['team_members'] ?? 0 }}</h4>
                    <small class="text-muted">Team members with confirmed deposits</small>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Level</th>
                        <th>Deposit Amount</th>
                        <th>Confirmed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teamPerformance['recent_deposits'] as $deposit)
                        <tr>
                            <td>
                                <strong>TC {{ $deposit->user_id ?? 'Unknown User' }}</strong><br>
                                {{-- <small class="text-muted">User #{{ $deposit->user_id }}</small> --}}
                            </td>
                            <td>
                                <span class="badge text-black">Level {{ $deposit->team_level ?? '-' }}</span>
                            </td>
                            <td>
                                <strong class="text-success">${{ number_format($deposit->amount, 2) }}</strong>
                            </td>
                            <td>{{ optional($deposit->confirmed_at ?? $deposit->created_at)->format('M d, Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No team deposits found yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                                        <span class="badge badge-{{ $earning->type == 'deposit' ? 'info' : ($earning->type == 'profit_share' ? 'success' : 'warning') }}">
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

@push('scripts')
<script>
(function initProfitCountdown() {
    const targetNode = document.getElementById('nextProfitTime');
    const countdownNode = document.getElementById('profitCountdown');

    if (!targetNode || !countdownNode) {
        return;
    }

    let targetDateStr = targetNode.textContent.trim();
    let targetDate = null;
    let hasTriedRefresh = false;
    let refreshAttempts = 0;
    const maxRefreshAttempts = 3;

    function pad(value) {
        return value.toString().padStart(2, '0');
    }

    // Parse the date - handle ISO8601 and other formats
    try {
        if (targetDateStr) {
            targetDate = new Date(targetDateStr);
        }
    } catch (e) {
        console.error('Failed to parse profit time:', targetDateStr, e);
    }

    // Check if date is valid and in the future
    if (!targetDate || Number.isNaN(targetDate.getTime())) {
        countdownNode.textContent = '-- : -- : --';
        return;
    }

    // If the date is in the past, calculate fallback time (15 minutes from now)
    const now = new Date();
    if (targetDate.getTime() < now.getTime()) {
        // Calculate fallback: add 15 minutes to current time
        const fallbackTime = new Date(now.getTime() + 15 * 60 * 1000);
        targetDate = fallbackTime;
        console.info('Next profit time was in the past. Using fallback time:', fallbackTime.toISOString());
    }

    function refreshProfitTime() {
        if (refreshAttempts >= maxRefreshAttempts) {
            console.warn('Max refresh attempts reached. Using calculated fallback time.');
            // Use fallback: 15 minutes from now
            targetDate = new Date(new Date().getTime() + 15 * 60 * 1000);
            tick();
            return;
        }

        refreshAttempts++;
        console.info(`Refreshing profit time (attempt ${refreshAttempts}/${maxRefreshAttempts})`);

        // Fetch updated dashboard data to get new next_profit_time
        fetch('{{ route("user.dashboard") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.text();
        })
        .then(html => {
            // Extract the nextProfitTime value from the new HTML
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            const newTargetNode = newDoc.getElementById('nextProfitTime');
            
            if (newTargetNode && newTargetNode.textContent.trim()) {
                const newTime = newTargetNode.textContent.trim();
                const newDate = new Date(newTime);
                
                // Verify new date is valid and in future
                if (!isNaN(newDate.getTime()) && newDate.getTime() > new Date().getTime()) {
                    targetNode.textContent = newTime;
                    targetDate = newDate;
                    refreshAttempts = 0; // Reset on success
                    hasTriedRefresh = false;
                    console.info('Successfully updated profit time:', newTime);
                    tick();
                } else {
                    // Invalid new time, use fallback
                    console.warn('Refreshed time is invalid or in past. Using fallback.');
                    targetDate = new Date(new Date().getTime() + 15 * 60 * 1000);
                    tick();
                }
            } else {
                // No time found in response
                console.warn('No profit time found in response. Retrying...');
                setTimeout(refreshProfitTime, 2000 * refreshAttempts); // Exponential backoff
            }
        })
        .catch(error => {
            console.error('Error fetching updated profit time:', error);
            if (refreshAttempts < maxRefreshAttempts) {
                // Retry with exponential backoff
                const delayMs = 2000 * refreshAttempts;
                console.info(`Retrying in ${delayMs}ms...`);
                setTimeout(refreshProfitTime, delayMs);
            } else {
                // Use fallback after max attempts
                targetDate = new Date(new Date().getTime() + 15 * 60 * 1000);
                tick();
            }
        });
    }

    function tick() {
        if (!targetDate) {
            countdownNode.textContent = '-- : -- : --';
            return;
        }

        const now = new Date();
        const distance = targetDate.getTime() - now.getTime();

        if (distance <= 0) {
            // Timer expired - show 00:00:00 temporarily, then refresh
            countdownNode.textContent = '00 : 00 : 00';
            
            // Try to refresh the profit time after a short delay
            if (!hasTriedRefresh) {
                hasTriedRefresh = true;
                console.info('Countdown reached zero. Refreshing profit time...');
                setTimeout(refreshProfitTime, 1000);
            }
            return;
        }

        const totalSeconds = Math.floor(distance / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        countdownNode.textContent = `${pad(hours)} : ${pad(minutes)} : ${pad(seconds)}`;
    }

    // Initial tick
    tick();
    
    // Update every second
    setInterval(tick, 1000);
})();
</script>
@endpush

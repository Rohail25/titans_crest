@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-title">
    <i class="fas fa-chart-line"></i>
    Dashboard
</div>

<!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="kpi-card">
            <div class="kpi-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-label">Total Users</div>
            <div class="kpi-value">{{ $kpis['total_users'] }}</div>
            <small class="text-success">
                <i class="fas fa-check-circle"></i>
                {{ $kpis['active_users'] }} active
            </small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="kpi-card">
            <div class="kpi-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="kpi-label">Total Balance</div>
            <div class="kpi-value">${{ number_format($kpis['total_balance'], 2) }}</div>
            <small text-muted>User wallets</small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="kpi-card">
            <div class="kpi-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="kpi-label">Total Earnings</div>
            <div class="kpi-value">${{ number_format($kpis['total_earnings'], 2) }}</div>
            <small text-muted>Generated</small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="kpi-card">
            <div class="kpi-icon">
                <i class="fas fa-check-circle" style="color: #22c55e;"></i>
            </div>
            <div class="kpi-label">Approved Withdrawals</div>
            <div class="kpi-value">{{ $kpis['approved_withdrawals'] }}</div>
            <small style="color: #22c55e;">
                <i class="fas fa-dollar-sign"></i>
                ${{ number_format($kpis['approved_withdrawals_amount'], 2) }}
            </small>
        </div>
    </div>
</div>

<!-- Critical Alerts -->
@if($kpis['pending_withdrawals'] > 0)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>{{ $kpis['pending_withdrawals'] }} pending withdrawal(s)</strong> awaiting your action.
        <a href="{{ route('admin.withdrawals.index') }}" class="alert-link">Review now</a>
    </div>
@endif

<div class="row">
    <!-- Charts -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line"></i> User Growth (30 Days)
            </div>
            <div class="card-body">
                <canvas id="userGrowthChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar"></i> Deposit Trend (30 Days)
            </div>
            <div class="card-body">
                <canvas id="depositChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-arrow-up"></i> Withdrawal Trend (30 Days)
            </div>
            <div class="card-body">
                <canvas id="withdrawalChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-heartbeat"></i> System Health
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item" style="background: transparent; border-color: rgba(212, 175, 55, 0.1);">
                        <div class="d-flex justify-content-between">
                            <span>Database Status</span>
                            <span class="badge badge-success">Healthy</span>
                        </div>
                    </div>
                    <div class="list-group-item" style="background: transparent; border-color: rgba(212, 175, 55, 0.1);">
                        <div class="d-flex justify-content-between">
                            <span>Last Profit Distribution</span>
                            <span class="text-muted">
                                @if($systemHealth['last_profit_distribution'])
                                    {{ $systemHealth['last_profit_distribution']->format('M d, Y H:i') }}
                                @else
                                    Never
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="list-group-item" style="background: transparent; border-color: rgba(212, 175, 55, 0.1);">
                        <div class="d-flex justify-content-between">
                            <span>Failed Emails</span>
                            <span class="badge badge-{{ $systemHealth['failed_emails'] > 0 ? 'warning' : 'success' }}">
                                {{ $systemHealth['failed_emails'] }}
                            </span>
                        </div>
                    </div>
                    <div class="list-group-item" style="background: transparent; border-color: rgba(212, 175, 55, 0.1);">
                        <div class="d-flex justify-content-between">
                            <span>Users Needing Attention</span>
                            <span class="badge badge-{{ $systemHealth['users_needing_attention'] > 0 ? 'warning' : 'success' }}">
                                {{ $systemHealth['users_needing_attention'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history"></i> Recent Admin Activities
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Target</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentActivities as $activity)
                    <tr>
                        <td>{{ $activity->admin->name }}</td>
                        <td>
                            <span class="badge badge-success">
                                {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                            </span>
                        </td>
                        <td>{{ $activity->target_type }} #{{ $activity->target_id }}</td>
                        <td>
                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No recent activities</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($userGrowth['labels']) !!},
            datasets: [{
                label: 'New Users',
                data: {!! json_encode($userGrowth['data']) !!},
                borderColor: '#d4af37',
                backgroundColor: 'rgba(212, 175, 55, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: { color: '#eee' }
                }
            },
            scales: {
                y: {
                    ticks: { color: '#aaa' },
                    grid: { color: 'rgba(212, 175, 55, 0.1)' }
                },
                x: {
                    ticks: { color: '#aaa' },
                    grid: { color: 'rgba(212, 175, 55, 0.1)' }
                }
            }
        }
    });

    // Deposit Chart
    const depositCtx = document.getElementById('depositChart').getContext('2d');
    new Chart(depositCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($depositTrend['labels']) !!},
            datasets: [{
                label: 'Deposits Amount',
                data: {!! json_encode($depositTrend['totals']) !!},
                backgroundColor: '#22c55e',
                borderColor: '#16a34a',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { labels: { color: '#eee' } }
            },
            scales: {
                y: {
                    ticks: { color: '#aaa' },
                    grid: { color: 'rgba(212, 175, 55, 0.1)' }
                },
                x: {
                    ticks: { color: '#aaa' },
                    grid: { color: 'rgba(212, 175, 55, 0.1)' }
                }
            }
        }
    });

    // Withdrawal Chart
    const withdrawalCtx = document.getElementById('withdrawalChart').getContext('2d');
    new Chart(withdrawalCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($withdrawalTrend['labels']) !!},
            datasets: [{
                label: 'Withdrawn Amount',
                data: {!! json_encode($withdrawalTrend['totals']) !!},
                backgroundColor: '#3b82f6',
                borderColor: '#1d4ed8',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { labels: { color: '#eee' } }
            },
            scales: {
                y: {
                    ticks: { color: '#aaa' },
                    grid: { color: 'rgba(212, 175, 55, 0.1)' }
                },
                x: {
                    ticks: { color: '#aaa' },
                    grid: { color: 'rgba(212, 175, 55, 0.1)' }
                }
            }
        }
    });
</script>
@endsection

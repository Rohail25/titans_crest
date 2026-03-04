@extends('layouts.user')

@section('page-title', 'Analytics')

@section('content')
<div class="row mb-4">
    <div class="col-lg-3">
        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-card-label">Daily Profit</div>
            <div class="stat-card-value">${{ number_format($profit['daily_profit'], 2) }}</div>
            <div class="stat-card-change">From active packages</div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-card-label">Monthly Profit</div>
            <div class="stat-card-value">${{ number_format($profit['monthly_profit'], 2) }}</div>
            <div class="stat-card-change">This month</div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-calendar-year"></i>
            </div>
            <div class="stat-card-label">Yearly Profit</div>
            <div class="stat-card-value">${{ number_format($profit['yearly_profit'], 2) }}</div>
            <div class="stat-card-change">This year</div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-percent"></i>
            </div>
            <div class="stat-card-label">ROI</div>
            <div class="stat-card-value text-success">
                {{ $wallet['total_deposit'] > 0 ? number_format(($wallet['total_earned'] / $wallet['total_deposit'] * 100), 1) : 0 }}%
            </div>
            <div class="stat-card-change">Return on investment</div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Earnings Trend (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="earningsChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Monthly Breakdown</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- 3x Cap Progress -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-target"></i> 3x Cap Progress</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card-label">Total Deposit</div>
                        <h4>${{ number_format($wallet['total_deposit'], 2) }}</h4>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card-label">Cap Limit (3x)</div>
                        <h4>${{ number_format($wallet['cap_3x'], 2) }}</h4>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card-label">Total Earned</div>
                        <h4>${{ number_format($wallet['total_earned'], 2) }}</h4>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card-label">Remaining to Earn</div>
                        <h4 class="text-{{ $wallet['cap_reached'] ? 'danger' : 'success' }}">
                            ${{ number_format($wallet['remaining_3x'], 2) }}
                        </h4>
                    </div>
                </div>

                <hr>

                <div class="progress-label">
                    <span>Cap Utilization</span>
                    <span>{{ number_format($wallet['cap_percentage'], 1) }}%</span>
                </div>
                <div class="progress" style="height: 12px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $wallet['cap_percentage'] }}%"></div>
                </div>

                @if($wallet['cap_reached'])
                    <div class="alert alert-success mt-3 mb-0">
                        <i class="fas fa-check-circle"></i> Congratulations! You've reached your 3x earning cap.
                    </div>
                @else
                    <small class="text-muted mt-3 d-block">
                        Keep earning! You can make up to ${{ number_format($wallet['remaining_3x'], 2) }} more.
                    </small>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Active Packages -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-box"></i> Active Packages</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Package Name</th>
                                <th>Price</th>
                                <th>Daily Rate</th>
                                <th>Daily Earnings</th>
                                <th>Activated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($packages as $package)
                                <tr>
                                    <td><strong>{{ $package['name'] }}</strong></td>
                                    <td>${{ number_format($package['price'], 2) }}</td>
                                    <td>{{ ($package['daily_profit_rate'] * 100) }}%</td>
                                    <td><strong>${{ number_format($package['daily_profit'], 2) }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($package['activated_at'])->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No active packages. Purchase now to start earning!
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Earnings Chart
const earningsCtx = document.getElementById('earningsChart').getContext('2d');
const earningsData = {!! json_encode($earningsData) !!};

new Chart(earningsCtx, {
    type: 'line',
    data: {
        labels: Object.keys(earningsData),
        datasets: [{
            label: 'Daily Earnings',
            data: Object.values(earningsData),
            borderColor: '#1e40af',
            backgroundColor: 'rgba(30, 64, 175, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 2,
            pointRadius: 4,
            pointBackgroundColor: '#1e40af',
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toFixed(2);
                    }
                }
            }
        }
    }
});

// Monthly Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyData = {!! json_encode($monthlyEarnings) !!};

new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: Object.keys(monthlyData),
        datasets: [{
            label: 'Monthly Earnings',
            data: Object.values(monthlyData),
            backgroundColor: [
                '#1e40af',
                '#1e40af',
                '#1e40af',
                '#fbbf24',
                '#fbbf24',
                '#fbbf24',
                '#1e40af',
                '#1e40af',
                '#1e40af',
                '#1e40af',
                '#1e40af',
                '#1e40af'
            ],
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toFixed(0);
                    }
                }
            }
        }
    }
});
</script>
@endpush

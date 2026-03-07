@extends('layouts.admin')

@section('title', 'Earnings Reports')

@section('content')
<div class="page-title">
    <i class="fas fa-chart-pie"></i>
    Earnings Reports
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">Filter Earnings</div>
    <div class="card-body">
        <form action="{{ route('admin.reports.earnings') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-4 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.reports.earnings') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Earnings</div>
            <div class="kpi-value">${{ number_format($earnings->sum('amount'), 2) }}</div>
            <small class="text-muted">{{ $earnings->count() }} distributions</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Unique Users</div>
            <div class="kpi-value">{{ $earnings->pluck('user_id')->unique()->count() }}</div>
            <small class="text-muted">earning profits</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Avg Per User</div>
            <div class="kpi-value">${{ number_format($earnings->count() > 0 ? $earnings->sum('amount') / $earnings->pluck('user_id')->unique()->count() : 0, 2) }}</div>
            <small class="text-muted">average earnings</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Avg Per Earning</div>
            <div class="kpi-value">${{ number_format($earnings->count() > 0 ? $earnings->sum('amount') / $earnings->count() : 0, 2) }}</div>
            <small class="text-muted">per distribution</small>
        </div>
    </div>
</div>

<!-- Earnings Table -->
<div class="card">
    <div class="card-header">
        <span>Earnings Distributions ({{ $earnings->count() }} records)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Package</th>
                    <th>Daily Rate (%)</th>
                    <th>Amount Earned</th>
                    <th>Distribution Date</th>
                    <th>Metadata</th>
                </tr>
            </thead>
            <tbody>
                @forelse($earnings as $earning)
                    <tr>
                        <td>
                            <strong>{{ $earning->user->name ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            <small>{{ $earning->user->email ?? 'N/A' }}</small>
                        </td>
                        <td>
                            @if(isset($earning->userPackage))
                                <span class="badge bg-primary">
                                    {{ $earning->userPackage->package->name ?? 'Unknown' }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    ${{ number_format($earning->userPackage->package->price ?? 0, 2) }}
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($earning->userPackage))
                                <strong>{{ $earning->userPackage->package->daily_profit_rate ?? 0 }}%</strong>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-success fs-6">
                                ${{ number_format($earning->amount, 2) }}
                            </span>
                        </td>
                        <td>
                            {{ $earning->created_at->format('M d, Y H:i') }}
                        </td>
                        <td>
                            @php
                                $metadata = $earning->metadata ?? [];
                                $multiplier = $metadata['multiplier'] ?? 'N/A';
                            @endphp
                            @if($metadata)
                                <small class="text-muted">
                                    Multiplier: {{ $multiplier }}
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox"></i> No earnings found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Earnings by Package -->
@if($earnings->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <span>Earnings by Package</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Package</th>
                    <th>Count</th>
                    <th>Total Earnings</th>
                    <th>Average</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($earnings->groupBy(function($item) { return $item->userPackage?->package?->name ?? 'Unknown'; }) as $packageName => $packageEarnings)
                    <tr>
                        <td>{{ $packageName }}</td>
                        <td>{{ $packageEarnings->count() }}</td>
                        <td>${{ number_format($packageEarnings->sum('amount'), 2) }}</td>
                        <td>${{ number_format($packageEarnings->sum('amount') / $packageEarnings->count(), 2) }}</td>
                        <td>
                            {{ number_format(($packageEarnings->sum('amount') / $earnings->sum('amount')) * 100, 2) }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

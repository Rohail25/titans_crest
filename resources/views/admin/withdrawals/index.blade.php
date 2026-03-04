@extends('layouts.admin')

@section('title', 'Manage Withdrawals')

@section('content')
<div class="page-title">
    <i class="fas fa-coins"></i>
    Manage Withdrawals
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Pending Approvals</div>
            <div class="kpi-value">{{ $stats['pending_count'] }}</div>
            <small class="text-warning">
                <i class="fas fa-exclamation-circle"></i>
                ${{ number_format($stats['pending_total'], 2) }}
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Approved Today</div>
            <div class="kpi-value">{{ $stats['approved_today'] }}</div>
            <small class="text-success">
                <i class="fas fa-check"></i>
                ${{ number_format($stats['approved_today_total'], 2) }}
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Rejected Today</div>
            <div class="kpi-value">{{ $stats['rejected_today'] }}</div>
        </div>
    </div>
</div>

<!-- Pending Withdrawals Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Pending Withdrawals
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Amount (Net)</th>
                    <th>Fee (5%)</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $withdrawal)
                    <tr>
                        <td>
                            <strong>{{ $withdrawal->user->name }}</strong><br>
                            <small class="text-muted">{{ $withdrawal->user->email }}</small>
                        </td>
                        <td>${{ number_format($withdrawal->net_amount, 2) }}</td>
                        <td>${{ number_format($withdrawal->deduction_amount, 2) }}</td>
                        <td>
                            <span class="badge badge-warning">
                                <i class="fas fa-hourglass-end"></i>
                                {{ ucfirst(str_replace('_', ' ', $withdrawal->status)) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $withdrawal->created_at->format('M d, Y H:i') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.withdrawals.show', $withdrawal->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-check-circle"></i> No pending withdrawals
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="card-body" style="border-top: 1px solid rgba(212, 175, 55, 0.1);">
        {{ $withdrawals->links() }}
    </div>
</div>
@endsection

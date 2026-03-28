@extends('layouts.admin')

@section('title', 'Monthly Performance')

@section('content')
<div class="page-title">
    <i class="fas fa-medal"></i>
    Monthly Performance (User Wise)
</div>

<div class="row mb-4">
    <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Total Records</div>
            <div class="kpi-value">{{ number_format($stats['total_records']) }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Paid Records</div>
            <div class="kpi-value">{{ number_format($stats['total_paid']) }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Pending Payout</div>
            <div class="kpi-value">{{ number_format($stats['total_pending']) }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Not Qualified</div>
            <div class="kpi-value">{{ number_format($stats['total_not_qualified']) }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Rejected</div>
            <div class="kpi-value">{{ number_format($stats['total_rejected']) }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="kpi-card">
            <div class="kpi-label">Total Paid Amount</div>
            <div class="kpi-value">${{ number_format($stats['total_paid_amount'], 2) }}</div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Filter Monthly Performance</div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.monthly-performance.index') }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">User (name or email)</label>
                    <input type="text" name="search" value="{{ $filters['search'] }}" class="form-control" placeholder="Search user">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="paid" {{ $filters['status'] === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending_payout" {{ $filters['status'] === 'pending_payout' ? 'selected' : '' }}>Pending Payout</option>
                        <option value="not_qualified" {{ $filters['status'] === 'not_qualified' ? 'selected' : '' }}>Not Qualified</option>
                        <option value="qualified_skipped" {{ $filters['status'] === 'qualified_skipped' ? 'selected' : '' }}>Qualified Skipped</option>
                        <option value="rejected" {{ $filters['status'] === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Month</label>
                    <input type="month" name="month" value="{{ $filters['month'] }}" class="form-control">
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Monthly Performance Records</div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Period</th>
                    <th>Total Volume</th>
                    <th>Qualified Legs</th>
                    <th>Tier Target</th>
                    <th>Reward</th>
                    <th>Status</th>
                    <th>Paid At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rewards as $reward)
                    <tr>
                        <td>
                            <strong>{{ $reward->sponsor?->name ?? 'N/A' }}</strong><br>
                            <small class="text-muted">{{ $reward->sponsor?->email ?? '-' }}</small>
                        </td>
                        <td>
                            {{ optional($reward->period_start)->format('M d, Y') }}<br>
                            <small class="text-muted">to {{ optional($reward->period_end)->format('M d, Y') }}</small>
                        </td>
                        <td>${{ number_format((float) $reward->total_volume, 2) }}</td>
                        <td>{{ number_format((int) $reward->qualified_legs) }}</td>
                        <td>
                            @if(!is_null($reward->qualifying_tier_volume))
                                ${{ number_format((float) $reward->qualifying_tier_volume, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>${{ number_format((float) $reward->qualifying_tier_reward, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $reward->status === 'paid' ? 'success' : ($reward->status === 'pending_payout' ? 'primary' : ($reward->status === 'not_qualified' ? 'warning' : ($reward->status === 'rejected' ? 'secondary' : 'danger'))) }}">
                                {{ ucfirst(str_replace('_', ' ', $reward->status)) }}
                            </span>
                        </td>
                        <td>
                            @if($reward->paid_at)
                                {{ $reward->paid_at->format('M d, Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($reward->status === 'pending_payout')
                                <div class=" gap-2">
                                    <form method="POST" action="{{ route('admin.monthly-performance.confirm', $reward->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('admin.monthly-performance.reject', $reward->id) }}" class="monthly-reject-form">
                                        @csrf
                                        <input type="hidden" name="reason" value="">
                                        <button type="button" class="btn btn-sm btn-danger js-reject-btn">Reject</button>
                                    </form>
                                </div>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No monthly performance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-3">
        {{ $rewards->links() }}
    </div>
</div>

<script>
document.querySelectorAll('.js-reject-btn').forEach(function (button) {
    button.addEventListener('click', function () {
        var form = button.closest('.monthly-reject-form');
        if (!form) {
            return;
        }

        var reason = window.prompt('Enter rejection reason (optional):', '') || '';
        var reasonInput = form.querySelector('input[name="reason"]');
        if (reasonInput) {
            reasonInput.value = reason;
        }
        form.submit();
    });
});
</script>
@endsection

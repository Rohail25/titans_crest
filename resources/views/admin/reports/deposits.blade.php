@extends('layouts.admin')

@section('title', 'Deposit Reports')

@section('content')
<div class="page-title">
    <i class="fas fa-file-invoice-dollar"></i>
    Deposit Reports
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">Filter Deposits</div>
    <div class="card-body">
        <form action="{{ route('admin.reports.deposits') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-3 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.reports.deposits') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <a href="{{ route('admin.reports.deposits', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Deposits Table -->
<div class="card">
    <div class="card-header">
        <span>Deposits ({{ $deposits->count() }} records)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Transaction Hash</th>
                    <th>Status</th>
                    <th>Deposit Date</th>
                    <th>Confirmation Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $deposit)
                    <tr>
                        <td>
                            <strong>{{ $deposit->user->name ?? 'N/A' }}</strong><br>
                            <small class="text-muted">{{ $deposit->user->email ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $deposit->currency ?? 'BNB' }} {{ number_format($deposit->amount, 2) }}</span>
                        </td>
                        <td>
                            <small>{{ substr($deposit->tx_hash, 0, 10) }}...{{ substr($deposit->tx_hash, -10) }}</small><br>
                            <a href="https://bscscan.com/tx/{{ $deposit->tx_hash }}" target="_blank" class="text-decoration-none small">
                                <i class="fas fa-external-link-alt"></i> View on BSCscan
                            </a>
                        </td>
                        <td>
                            @php
                                $statusClass = match($deposit->status) {
                                    'pending' => 'warning',
                                    'confirmed' => 'success',
                                    'rejected' => 'danger',
                                    default => 'secondary'
                                };
                                $statusIcon = match($deposit->status) {
                                    'pending' => 'hourglass-half',
                                    'confirmed' => 'check-circle',
                                    'rejected' => 'times-circle',
                                    default => 'question-circle'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">
                                <i class="fas fa-{{ $statusIcon }}"></i> {{ ucfirst($deposit->status) }}
                            </span>
                        </td>
                        <td>{{ $deposit->created_at->format('M d, Y H:i') }}</td>
                        <td>{{ $deposit->confirmed_at ? $deposit->confirmed_at->format('M d, Y H:i') : '-' }}</td>
                        <td>
                            @if($deposit->status === 'pending')
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#confirmDeposit{{ $deposit->id }}">
                                    <i class="fas fa-check"></i> Confirm
                                </button>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectDeposit{{ $deposit->id }}">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            @else
                                <span class="text-muted small">No action available</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox"></i> No deposits found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Confirm Deposit Modal -->
@foreach($deposits as $deposit)
    @if($deposit->status === 'pending')
        <div class="modal fade" id="confirmDeposit{{ $deposit->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" style="background: var(--secondary); border-color: var(--accent);">
                    <div class="modal-header" style="border-bottom-color: rgba(212, 175, 55, 0.2);">
                        <h5 class="modal-title">Confirm Deposit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to confirm this deposit?</p>
                        <div class="alert alert-info">
                            <strong>User:</strong> {{ $deposit->user->name }}<br>
                            <strong>Amount:</strong> {{ $deposit->currency ?? 'BNB' }} {{ number_format($deposit->amount, 2) }}<br>
                            <strong>Transaction:</strong> <code>{{ $deposit->tx_hash }}</code>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top-color: rgba(212, 175, 55, 0.2);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('admin.deposits.confirm', $deposit->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Confirm Deposit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="rejectDeposit{{ $deposit->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" style="background: var(--secondary); border-color: var(--accent);">
                    <div class="modal-header" style="border-bottom-color: rgba(212, 175, 55, 0.2);">
                        <h5 class="modal-title">Reject Deposit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to reject this deposit?</p>
                        <div class="alert alert-warning">
                            <strong>User:</strong> {{ $deposit->user->name }}<br>
                            <strong>Amount:</strong> {{ $deposit->currency ?? 'BNB' }} {{ number_format($deposit->amount, 2) }}<br>
                            <strong>Transaction:</strong> <code>{{ $deposit->tx_hash }}</code>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason (Optional)</label>
                            <textarea class="form-control" name="rejection_reason" rows="3" placeholder="Why is this deposit being rejected?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top-color: rgba(212, 175, 55, 0.2);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('admin.deposits.reject', $deposit->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger">Reject Deposit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

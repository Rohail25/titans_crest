@extends('layouts.admin')

@section('title', 'Withdrawal Reports')

@section('content')
<div class="page-title">
    <i class="fas fa-money-bill-wave"></i>
    Withdrawal Reports
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">Filter Withdrawals</div>
    <div class="card-body">
        <form action="{{ route('admin.reports.withdrawals') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending_otp" {{ request('status') === 'pending_otp' ? 'selected' : '' }}>Pending OTP</option>
                    <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
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
                <a href="{{ route('admin.reports.withdrawals') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <a href="{{ route('admin.reports.withdrawals', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Withdrawals</div>
            <div class="kpi-value">${{ number_format($withdrawals->sum('amount'), 2) }}</div>
            <small class="text-muted">{{ $withdrawals->count() }} transactions</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Approved</div>
            <div class="kpi-value">${{ number_format($withdrawals->where('status', 'approved')->sum('amount'), 2) }}</div>
            <small class="text-muted">{{ $withdrawals->where('status', 'approved')->count() }} transactions</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Pending</div>
            <div class="kpi-value">${{ number_format($withdrawals->whereIn('status', ['pending_otp', 'pending_approval'])->sum('amount'), 2) }}</div>
            <small class="text-muted">{{ $withdrawals->whereIn('status', ['pending_otp', 'pending_approval'])->count() }} transactions</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Rejected</div>
            <div class="kpi-value">${{ number_format($withdrawals->where('status', 'rejected')->sum('amount'), 2) }}</div>
            <small class="text-muted">{{ $withdrawals->where('status', 'rejected')->count() }} transactions</small>
        </div>
    </div>
</div>

<!-- Withdrawals Table -->
<div class="card">
    <div class="card-header">
        <span>Withdrawals ({{ $withdrawals->count() }} records)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Wallet Address</th>
                    <th>Status</th>
                    <th>Request Date</th>
                    <th>Approval Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $withdrawal)
                    <tr>
                        <td>
                            <strong>{{ $withdrawal->user->name ?? 'N/A' }}</strong><br>
                            <small class="text-muted">{{ $withdrawal->user->email ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info">BNB {{ number_format($withdrawal->net_amount, 2) }}</span>
                        </td>
                        <td>
                            <small title="{{ $withdrawal->wallet_address }}">
                                {{ substr($withdrawal->wallet_address, 0, 10) }}...{{ substr($withdrawal->wallet_address, -8) }}
                            </small>
                        </td>
                        <td>
                            @php
                                $statusClass = match($withdrawal->status) {
                                    'pending_otp' => 'warning',
                                    'pending_approval' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    default => 'secondary'
                                };
                                $statusIcon = match($withdrawal->status) {
                                    'pending_otp' => 'key',
                                    'pending_approval' => 'hourglass-half',
                                    'approved' => 'check-circle',
                                    'rejected' => 'times-circle',
                                    default => 'question-circle'
                                };
                                $statusLabel = match($withdrawal->status) {
                                    'pending_otp' => 'Pending OTP',
                                    'pending_approval' => 'Pending Approval',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    default => 'Unknown'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">
                                <i class="fas fa-{{ $statusIcon }}"></i> {{ $statusLabel }}
                            </span>
                        </td>
                        <td>{{ $withdrawal->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            @if($withdrawal->approved_at)
                                {{ $withdrawal->approved_at->format('M d, Y H:i') }}
                            @elseif($withdrawal->rejected_at)
                                {{ $withdrawal->rejected_at->format('M d, Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($withdrawal->status === 'pending_approval')
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveWithdrawal{{ $withdrawal->id }}">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectWithdrawal{{ $withdrawal->id }}">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            @else
                                <span class="text-muted small">No action</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox"></i> No withdrawals found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Approve Withdrawal Modal -->
@foreach($withdrawals as $withdrawal)
    @if($withdrawal->status === 'pending_approval')
        <div class="modal fade" id="approveWithdrawal{{ $withdrawal->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" style="background: var(--secondary); border-color: var(--accent);">
                    <div class="modal-header" style="border-bottom-color: rgba(212, 175, 55, 0.2);">
                        <h5 class="modal-title">Approve Withdrawal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-warning mb-3">
                                <strong><i class="fas fa-exclamation-triangle"></i> Action Required:</strong><br>
                                1. Send <strong>BNB {{ number_format($withdrawal->net_amount, 2) }}</strong> to user's wallet via MetaMask<br>
                                2. Copy the transaction hash from MetaMask<br>
                                3. Paste it below to confirm payment
                            </div>
                            <div class="mb-3">
                                <label class="form-label">User's Wallet Address</label>
                                <input type="text" class="form-control" value="{{ $withdrawal->wallet_address }}" readonly>
                                <small class="form-text text-muted">
                                    <i class="fas fa-user"></i> {{ $withdrawal->user->name }}
                                </small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Transaction Hash <span class="text-danger">*</span></label>
                                <input type="text" name="tx_hash" class="form-control" placeholder="Paste transaction hash from MetaMask" required>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Required to prove payment was made
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top-color: rgba(212, 175, 55, 0.2);">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Approve & Process
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

<!-- Reject Withdrawal Modal -->
@foreach($withdrawals as $withdrawal)
    @if($withdrawal->status === 'pending_approval')
        <div class="modal fade" id="rejectWithdrawal{{ $withdrawal->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" style="background: var(--secondary); border-color: var(--accent);">
                    <div class="modal-header" style="border-bottom-color: rgba(212, 175, 55, 0.2);">
                        <h5 class="modal-title">Reject Withdrawal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}" method="POST">
                        <div class="modal-body">
                            <p>Are you sure you want to reject this withdrawal?</p>
                            <div class="alert alert-warning">
                                <strong>User:</strong> {{ $withdrawal->user->name }}<br>
                                <strong>Amount:</strong> BNB {{ number_format($withdrawal->amount, 2) }}<br>
                                <strong>Wallet:</strong> <code>{{ $withdrawal->wallet_address }}</code>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rejection Reason</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Enter reason for rejection..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top-color: rgba(212, 175, 55, 0.2);">
                            @csrf
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

@extends('layouts.admin')

@section('title', 'All Deposits')

@section('content')
<div class="page-title">
    <i class="fas fa-box"></i>
    All Deposits
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.fund-management.deposits') }}" class="row g-3">
            <!-- Search -->
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Email, Name, or TX Hash" 
                    value="{{ request('search') }}">
            </div>

            <!-- Status Filter -->
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Network Filter -->
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="network" class="form-select">
                    <option value="">All Types</option>
                    <option value="ADMIN" {{ request('network') === 'ADMIN' ? 'selected' : '' }}>Admin Added</option>
                    <option value="ETH" {{ request('network') === 'ETH' ? 'selected' : '' }}>ETH</option>
                    <option value="BTC" {{ request('network') === 'BTC' ? 'selected' : '' }}>BTC</option>
                    <option value="USDC" {{ request('network') === 'USDC' ? 'selected' : '' }}>USDC</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-md-5 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.fund-management.deposits') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Additional Info -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Pending Total</div>
            <div class="kpi-value text-warning">
                ${{ number_format($deposits->total() > 0 ? $deposits->where('status', 'pending')->sum('amount') : 0, 2) }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Confirmed Total</div>
            <div class="kpi-value text-success">
                ${{ number_format($deposits->total() > 0 ? $deposits->where('status', 'confirmed')->sum('amount') : 0, 2) }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Admin Added</div>
            <div class="kpi-value text-info">
                ${{ number_format($deposits->total() > 0 ? $deposits->where('network', 'ADMIN')->sum('amount') : 0, 2) }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Rejected Total</div>
            <div class="kpi-value text-danger">
                ${{ number_format($deposits->total() > 0 ? $deposits->where('status', 'rejected')->sum('amount') : 0, 2) }}
            </div>
        </div>
    </div>
</div>
<!-- Deposits Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span>Deposits List ({{ $deposits->total() }} total)</span>
            <span class="badge bg-primary">Showing {{ $deposits->count() }} of {{ $deposits->total() }}</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>TX Hash</th>
                    <th>Confirmed Date</th>
                    <th>Created Date</th>
                    {{-- <th>Actions</th> --}}
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $deposit)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $deposit->user->name }}</div>
                                    <div class="text-muted small">{{ $deposit->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-success">${{ number_format($deposit->amount, 2) }}</span>
                        </td>
                        <td>
                            @if($deposit->network === 'ADMIN')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-user-shield"></i> Admin Added
                                </span>
                            @else
                                <span class="badge bg-info">
                                    {{ $deposit->network }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($deposit->status === 'pending')
                                <span class="badge bg-warning">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @elseif($deposit->status === 'confirmed')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Confirmed
                                </span>
                            @elseif($deposit->status === 'rejected')
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle"></i> Rejected
                                </span>
                            @else
                                <span class="badge bg-secondary">{{ $deposit->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($deposit->tx_hash)
                                <code class="text-truncate" style="max-width: 100px; display: inline-block;" 
                                    title="{{ $deposit->tx_hash }}">
                                    {{ substr($deposit->tx_hash, 0, 8) }}...
                                </code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($deposit->confirmed_at)
                                <span class="small">{{ $deposit->confirmed_at->format('M d, Y H:i') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="small text-muted">{{ $deposit->created_at->format('M d, Y H:i') }}</span>
                        </td>
                        {{-- <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.users.show', $deposit->user_id) }}" 
                                    class="btn btn-info" title="View User">
                                    <i class="fas fa-user"></i>
                                </a>
                                @if($deposit->status === 'pending')
                                    <form action="{{ route('admin.deposits.confirm', $deposit->id) }}" 
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" 
                                            title="Confirm Deposit"
                                            onclick="return confirm('Confirm this deposit?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.deposits.reject', $deposit->id) }}" 
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                            title="Reject Deposit"
                                            onclick="return confirm('Reject this deposit?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox"></i> No deposits found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($deposits->hasPages())
        <div class="card-footer">
            {{ $deposits->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>


@endsection

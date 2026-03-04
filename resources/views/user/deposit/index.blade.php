@extends('layouts.user')

@section('page-title', 'Deposits')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-arrow-down"></i> Make a Deposit</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Deposit Instructions:</strong> Send BNB to the wallet address below. Your balance will be credited after blockchain confirmation.
                </div>

                <div class="card border-light mb-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">BNB Wallet Address</h6>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $bnbAddress }}" readonly id="bnbAddress">
                            <button class="btn btn-primary" type="button" onclick="copyToClipboard('bnbAddress')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">QR Code: <span class="badge badge-light">Coming Soon</span></small>
                    </div>
                </div>

                <form action="{{ route('user.deposit.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Amount (BNB or equivalent)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                name="amount" step="0.01" min="1" placeholder="Enter amount" required>
                        </div>
                        @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transaction Hash (tx_hash)</label>
                        <input type="text" class="form-control @error('tx_hash') is-invalid @enderror" 
                            name="tx_hash" placeholder="0x..." required>
                        <small class="text-muted">From blockchain transaction receipt</small>
                        @error('tx_hash')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-arrow-down"></i> Submit Deposit
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-history"></i> Deposit Stats</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="stat-card-label">Total Confirmed</div>
                    <h4>${{ number_format($deposits['total_confirmed'] ?? 0, 2) }}</h4>
                </div>

                <div class="mb-3">
                    <div class="stat-card-label">Pending Deposits</div>
                    <h4 class="text-warning">{{ $deposits['pending_count'] ?? 0 }}</h4>
                    <small>${{ number_format($deposits['pending_amount'] ?? 0, 2) }}</small>
                </div>

                <div>
                    <div class="stat-card-label">Total Transactions</div>
                    <h4>{{ $deposits['total_deposits'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deposit History -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> Deposit History</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Tx Hash</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deposits as $deposit)
                                <tr>
                                    <td>{{ $deposit['created_at'] ? \Carbon\Carbon::parse($deposit['created_at'])->format('M d, Y h:i A') : '-' }}</td>
                                    <td><strong>${{ number_format($deposit['amount'], 2) }}</strong></td>
                                    <td><code style="font-size: 0.75rem;">{{ substr($deposit['tx_hash'], 0, 12) }}...</code></td>
                                    <td>
                                        @switch($deposit['status'])
                                            @case('pending')
                                                <span class="badge badge-warning">Pending</span>
                                                @break
                                            @case('confirmed')
                                                <span class="badge badge-success">Confirmed</span>
                                                @break
                                            @default
                                                <span class="badge badge-danger">{{ ucfirst($deposit['status']) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="https://bscscan.com/tx/{{ $deposit['tx_hash'] }}" target="_blank" 
                                            class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No deposits yet. Make your first deposit above.
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

@push('scripts')
<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    document.execCommand('copy');
    alert('Copied to clipboard!');
}
</script>
@endpush
@endsection

@extends('layouts.user')

@section('page-title', 'Withdrawals')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-arrow-up"></i> Request Withdrawal</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Withdrawal Rules:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Minimum withdrawal: ${{ $minWithdrawal }}</li>
                        <li>Processing fee: {{ $deductionPercent }}%</li>
                        <li>OTP verification required</li>
                        <li>Cannot exceed available balance</li>
                    </ul>
                </div>

                <form id="withdrawalForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Withdrawal Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="withdrawAmount" 
                                name="amount" step="0.01" min="{{ $minWithdrawal }}" placeholder="Enter amount" required>
                        </div>
                        <small class="text-muted">Available: $<span id="availableBalance">{{ $wallet['balance'] }}</span></small>
                    </div>

                    <!-- Withdrawal Calculator -->
                    <div class="card border-light mb-3" style="display: none;" id="calculatorCard">
                        <div class="card-body p-3 bg-light">
                            <h6>Withdrawal Breakdown</h6>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Requested Amount</small>
                                    <h6>$<span id="calcAmount">0.00</span></h6>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Processing Fee ({{ $deductionPercent }}%)</small>
                                    <h6>-$<span id="calcFee">0.00</span></h6>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6"></div>
                                <div class="col-6">
                                    <small class="text-muted">Net Payout</small>
                                    <h5 class="text-success">$<span id="calcNet">0.00</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                        <i class="fas fa-arrow-up"></i> Request Withdrawal
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-wallet"></i> Balance Info</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="stat-card-label">Available Balance</div>
                    <h4>${{ number_format($wallet['balance'], 2) }}</h4>
                </div>

                <div class="mb-3">
                    <div class="stat-card-label">Pending Balance</div>
                    <h4 class="text-warning">${{ number_format($wallet['pending_balance'], 2) }}</h4>
                </div>

                @if($wallet['suspicious_balance'] > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Suspicious Funds:</strong> ${{ number_format($wallet['suspicious_balance'], 2) }} cannot be withdrawn.
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-shield-alt"></i> Security</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">All withdrawals require:</p>
                <ul class="list-unstyled small">
                    <li><i class="fas fa-check text-success"></i> OTP Verification</li>
                    <li><i class="fas fa-check text-success"></i> Admin Approval</li>
                    <li><i class="fas fa-check text-success"></i> Blockchain Confirmation</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- OTP Modal -->
<div class="modal fade" id="otpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify OTP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">An OTP has been sent to your registered email/phone.</p>
                <div class="mb-3">
                    <label class="form-label">Enter OTP</label>
                    <input type="text" class="form-control form-control-lg text-center" id="otpInput" 
                        placeholder="000000" maxlength="6" autocomplete="off">
                    <small class="text-muted d-block mt-2">
                        Time remaining: <span id="otpTimer">5:00</span>
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="verifyOtpBtn">Verify</button>
            </div>
        </div>
    </div>
</div>

<!-- Withdrawal History -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-history"></i> Withdrawal History</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount Requested</th>
                                <th>Net Payout</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawals as $withdrawal)
                                <tr>
                                    <td>{{ $withdrawal['created_at'] ? \Carbon\Carbon::parse($withdrawal['created_at'])->format('M d, Y h:i A') : '-' }}</td>
                                    <td>${{ number_format($withdrawal['requested_amount'], 2) }}</td>
                                    <td><strong>${{ number_format($withdrawal['net_amount'], 2) }}</strong></td>
                                    <td>
                                        @switch($withdrawal['status'])
                                            @case('pending_otp')
                                                <span class="badge badge-info">Awaiting OTP</span>
                                                @break
                                            @case('pending_approval')
                                                <span class="badge badge-warning">Pending Approval</span>
                                                @break
                                            @case('approved')
                                                <span class="badge badge-success">Approved</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($withdrawal['status']) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($withdrawal['status'] === 'pending_otp')
                                            <button class="btn btn-sm btn-outline-primary" onclick="editWithdrawal({{ $withdrawal['id'] }})">
                                                Cancel
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No withdrawal requests yet.
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
let currentWithdrawalId = null;
let otpTimerInterval = null;

// Real-time calculator
document.getElementById('withdrawAmount').addEventListener('input', function() {
    const amount = parseFloat(this.value) || 0;
    
    if (amount > 0) {
        const fee = amount * ({{ $deductionPercent }} / 100);
        const net = amount - fee;
        
        document.getElementById('calcAmount').textContent = amount.toFixed(2);
        document.getElementById('calcFee').textContent = fee.toFixed(2);
        document.getElementById('calcNet').textContent = net.toFixed(2);
        document.getElementById('calculatorCard').style.display = 'block';
    } else {
        document.getElementById('calculatorCard').style.display = 'none';
    }
});

// Form submission
document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const amount = parseFloat(document.getElementById('withdrawAmount').value);
    
    fetch('{{ route("user.withdrawal.initiate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ amount })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            currentWithdrawalId = data.withdrawal_id;
            startOtpTimer();
            const modal = new bootstrap.Modal(document.getElementById('otpModal'));
            modal.show();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(err => alert('Error: ' + err.message));
});

// OTP verification
document.getElementById('verifyOtpBtn').addEventListener('click', function() {
    const otp = document.getElementById('otpInput').value;
    
    if (!otp || otp.length !== 6) {
        alert('Please enter a valid 6-digit OTP');
        return;
    }
    
    fetch('{{ route("user.withdrawal.verify-otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            withdrawal_id: currentWithdrawalId,
            otp: otp 
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('OTP Verified! Withdrawal is pending admin approval.');
            location.reload();
        } else {
            alert('Invalid OTP: ' + data.error);
        }
    })
    .catch(err => alert('Error: ' + err.message));
});

// OTP Timer
function startOtpTimer() {
    let seconds = 300; // 5 minutes
    
    otpTimerInterval = setInterval(function() {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        document.getElementById('otpTimer').textContent = 
            minutes + ':' + (secs < 10 ? '0' : '') + secs;
        
        seconds--;
        if (seconds < 0) clearInterval(otpTimerInterval);
    }, 1000);
}

// Cancel withdrawal
function editWithdrawal(withdrawalId) {
    if (confirm('Are you sure you want to cancel this withdrawal?')) {
        const form = document.createElement('form');
        form.method = 'DELETE';
        form.action = '/dashboard/withdrawals/' + withdrawalId + '/cancel';
        
        const token = document.querySelector('input[name="_token"]');
        if (token) {
            form.appendChild(token.cloneNode());
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection

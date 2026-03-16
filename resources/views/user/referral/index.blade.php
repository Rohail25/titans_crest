@extends('layouts.user')

@section('page-title', 'Referrals')

@section('content')
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-link"></i> Your Referral Link</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Share this code with friends to earn commissions:</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="refLink" 
                        value="{{ url('/ref/' . $referral['referral_code']) }}" readonly>
                    <button class="btn btn-primary" onclick="copyCode()">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>

                <h6>Referral Code</h6>
                <div class="badge badge-primary" style="font-size: 1.25rem; padding: 0.75rem 1.5rem;">
                    {{ $referral['referral_code'] }}
                </div>
                <small class="text-muted d-block mt-2">Share this code or link to earn commissions on referrals</small>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Referral Stats</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="stat-card-label">Direct Referrals</div>
                        <h3>{{ $referral['direct_referrals'] ?? 0 }}</h3>
                    </div>
                    <div class="col-6">
                        <div class="stat-card-label">Total Downline</div>
                        <h3>{{ $referral['total_referrals'] ?? 0 }}</h3>
                    </div>
                </div>
                <hr>
                <div class="stat-card-label">Commission Earned</div>
                <h4 class="text-success">${{ number_format($referral['commission_earned'] ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Direct Referrals -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-friends"></i> Direct Referrals</h5>
            </div>
            <div class="card-body">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                        @include('user.referral._level-table', [
                            'users' => $level1Users,
                            'emptyText' => 'No direct referrals yet. Share your referral code to get started!'
                        ])
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="stat-card h-100">
                            <div class="stat-card-label">Total Direct Referrals</div>
                            <div class="stat-card-value">{{ $directSummary['total_direct_referrals'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card h-100">
                            <div class="stat-card-label">Total Deposit From Direct Referrals</div>
                            <div class="stat-card-value">${{ number_format($directSummary['total_direct_deposit'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card h-100 primary">
                            <div class="stat-card-label">Total Referral Commission Earned</div>
                            <div class="stat-card-value">${{ number_format($directSummary['total_referral_commission_earned'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Commission Information -->
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> How It Works</h5>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li>Share your referral code with friends</li>
                    <li>When they join and purchase, you earn a commission</li>
                    <li>Commissions are credited to your wallet immediately</li>
                    <li>Build your team to increase passive income</li>
                    <li>No limit on how much you can earn!</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-gift"></i> Commission Structure</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="stat-card-label">Direct Referral</div>
                        <h5>5 Levels</h5>
                        <small class="text-muted">7%, 4%, 2%, 1%, 1%</small>
                    </div>
                    <div class="col-6">
                        <div class="stat-card-label">Daily Profit Share</div>
                        <h5>10 Levels</h5>
                        <small class="text-muted">20% down to 2%</small>
                    </div>
                </div>
                <hr>
                <small class="text-muted">
                    Example: If a direct referral buys a $100 package, level 1 earns $7 and deeper uplines earn by configured levels.
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyCode() {
    const link = document.getElementById('refLink');
    link.select();
    document.execCommand('copy');
    alert('Referral link copied to clipboard!');
}
</script>
@endpush

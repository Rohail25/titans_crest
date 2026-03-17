@extends('layouts.user')

@section('page-title', 'Referrals')

@section('content')
<div class="row mb-4 text-blue-200">
    <div class="col-lg-6">
        <div class="card bg-[#062a5f]/80 border border-[#d4af37]/20 backdrop-blur-sm shadow-[0_14px_34px_rgba(4,26,61,0.45)]">
            <div class="card-header border-[#d4af37]/20 bg-linear-to-b from-[#041a3d] to-[#062a5f]">
                <h5 class="text-white"><i class="fas fa-link text-[#d4af37]"></i> Your Referral Link</h5>
            </div>
            <div class="card-body bg-[#062a5f]/80 text-blue-200">
                <p class="text-white small mb-3">Share this code with friends to earn commissions:</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control bg-[#041a3d] border-[#d4af37]/20 text-white" id="refLink" 
                        value="{{ url('/ref/' . $referral['referral_code']) }}" readonly>
                    <button class="btn btn-primary text-[#041a3d] border-0" style="background-color: #d4af37;" onclick="copyCode()">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>

                <h6 class="text-white">Referral Code</h6>
                <div class="badge text-[#041a3d] border border-[#d4af37]/20" style="font-size: 1.25rem; padding: 0.75rem 1.5rem; background-color: #d4af37;">
                    {{ $referral['referral_code'] }}
                </div>
                <small class="text-white d-block mt-2">Share this code or link to earn commissions on referrals</small>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card bg-[#062a5f]/80 border border-[#d4af37]/20 backdrop-blur-sm shadow-[0_14px_34px_rgba(4,26,61,0.45)]">
            <div class="card-header border-[#d4af37]/20 bg-linear-to-b from-[#041a3d] to-[#062a5f]">
                <h5 class="text-white"><i class="fas fa-chart-line text-[#d4af37]"></i> Referral Stats</h5>
            </div>
            <div class="card-body bg-[#062a5f]/80 text-blue-200">
                <div class="row">
                    <div class="col-6">
                        <div class="stat-card-label text-[#d4af37]">Direct Referrals</div>
                        <h3 class="text-white">{{ $referral['direct_referrals'] ?? 0 }}</h3>
                    </div>
                    <div class="col-6">
                        <div class="stat-card-label text-[#d4af37]">Total Downline</div>
                        <h3 class="text-white">{{ $referral['total_referrals'] ?? 0 }}</h3>
                    </div>
                </div>
                <hr class="border-[#d4af37]/20 opacity-100">
                <div class="stat-card-label text-[#d4af37]">Commission Earned</div>
                <h4 class="text-white">${{ number_format($referral['commission_earned'] ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Direct Referrals -->
<div class="row">
    <div class="col-lg-12">
        <div class="card bg-[#062a5f]/80 border border-[#d4af37]/20 backdrop-blur-sm shadow-[0_14px_34px_rgba(4,26,61,0.45)] text-blue-200">
            <div class="card-header border-[#d4af37]/20 bg-linear-to-b from-[#041a3d] to-[#062a5f]">
                <h5 class="mb-0 text-white"><i class="fas fa-user-friends text-[#d4af37]"></i> Direct Referrals</h5>
            </div>
            <div class="card-body bg-[#062a5f]/80">
                <div class="card border border-[#d4af37]/20 bg-[#062a5f]/80 backdrop-blur-sm shadow-[0_14px_34px_rgba(4,26,61,0.35)] mb-4">
                    <div class="card-body p-0 bg-[#062a5f]/80 text-blue-200">
                        @include('user.referral._level-table', [
                            'users' => $level1Users,
                            'emptyText' => 'No direct referrals yet. Share your referral code to get started!'
                        ])
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="stat-card h-100 bg-[#062a5f]/80 border border-[#d4af37]/20 backdrop-blur-sm shadow-[0_14px_34px_rgba(4,26,61,0.35)]">
                            <div class="stat-card-label text-[#d4af37]">Total Direct Referrals</div>
                            <div class="stat-card-value text-white">{{ $directSummary['total_direct_referrals'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card h-100 bg-[#062a5f]/80 border border-[#d4af37]/20 backdrop-blur-sm shadow-[0_14px_34px_rgba(4,26,61,0.35)]">
                            <div class="stat-card-label text-[#d4af37]">Total Deposit From Direct Referrals</div>
                            <div class="stat-card-value text-white">${{ number_format($directSummary['total_direct_deposit'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card h-100 primary bg-[#062a5f]/80 border border-[#d4af37]/20 backdrop-blur-sm shadow-[0_14px_34px_rgba(4,26,61,0.35)]">
                            <div class="stat-card-label text-[#d4af37]">Total Referral Commission Earned</div>
                            <div class="stat-card-value text-white">${{ number_format($directSummary['total_referral_commission_earned'], 2) }}</div>
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
        <div class="card bg-[#062a5f]/80 border border-[#d4af37]/20 backdrop-blur-sm shadow-[0_14px_34px_rgba(4,26,61,0.45)] text-blue-200">
            <div class="card-header border-[#d4af37]/20 bg-linear-to-b from-[#041a3d] to-[#062a5f]">
                <h5 class="text-white"><i class="fas fa-info-circle text-[#d4af37]"></i> How It Works</h5>
            </div>
            <div class="card-body bg-[#062a5f]/80">
                <ol class="mb-0 text-white">
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
        <div class="card bg-[#062a5f]/80 border border-[#d4af37]/20 backdrop-blur-sm shadow-[0_14px_34px_rgba(4,26,61,0.45)] text-blue-200">
            <div class="card-header border-[#d4af37]/20 bg-linear-to-b from-[#041a3d] to-[#062a5f] ">
                <h5 class="text-white"><i class="fas fa-gift text-[#d4af37]"></i> Commission Structure</h5>
            </div>
            <div class="card-body bg-[#062a5f]/80">
                <div class="row">
                    <div class="col-6">
                        <div class="stat-card-label text-[#d4af37]">Direct Referral</div>
                        <h5 class="text-white">5 Levels</h5>
                        <small class="text-white">7%, 4%, 2%, 1%, 1%</small>
                    </div>
                    <div class="col-6">
                        <div class="stat-card-label text-[#d4af37]">Daily Profit Share</div>
                        <h5 class="text-white">10 Levels</h5>
                        <small class="text-white">20% down to 2%</small>
                    </div>
                </div>
                <hr class="border-[#d4af37]/20 opacity-100">
                <small class="text-white">
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

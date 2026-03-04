@extends('layouts.user')

@section('page-title', 'Wallet')

@section('content')
<div class="row mb-4">
    <div class="col-lg-4">
        <div class="stat-card primary">
            <div class="stat-card-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-card-label">Available Balance</div>
            <div class="stat-card-value">${{ number_format($wallet['balance'], 2) }}</div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-card-label">Pending Balance</div>
            <div class="stat-card-value">${{ number_format($wallet['pending_balance'], 2) }}</div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-card-label">Suspicious Balance</div>
            <div class="stat-card-value text-warning">${{ number_format($wallet['suspicious_balance'], 2) }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Wallet Summary</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <div class="stat-card-label">Total Deposited</div>
                        <h5>${{ number_format($wallet['total_deposit'], 2) }}</h5>
                    </div>
                    <div class="col-sm-6">
                        <div class="stat-card-label">Total Earned</div>
                        <h5>${{ number_format($wallet['total_earned'], 2) }}</h5>
                    </div>
                </div>

                <hr>

                <div class="mt-3">
                    <div class="stat-card-label mb-2">3x Cap Status</div>
                    <div class="progress-label">
                        <span>Progress</span>
                        <span>{{ number_format($wallet['cap_percentage'], 1) }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: {{ min($wallet['cap_percentage'], 100) }}%"></div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Maximum: ${{ number_format($wallet['cap_3x'], 2) }}
                        @if($wallet['cap_reached'])
                            <span class="badge badge-success float-end">Cap Reached</span>
                        @else
                            <span class="badge badge-primary float-end">Remaining: ${{ number_format($wallet['remaining_3x'], 2) }}</span>
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-shield-alt"></i> Wallet Security</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Your wallet is protected with:</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check-circle text-success"></i> AES-256 Encryption</li>
                    <li><i class="fas fa-check-circle text-success"></i> Two-Factor Authentication</li>
                    <li><i class="fas fa-check-circle text-success"></i> Transaction Verification</li>
                    <li><i class="fas fa-check-circle text-success"></i> Cold Storage for Large Amounts</li>
                </ul>

                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle"></i>
                    <strong>Suspicious Balance:</strong> Funds marked as suspicious until you generate 3x earnings from them.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

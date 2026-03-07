@extends('layouts.admin')

@section('title', 'Settings & Configuration')

@section('content')
<div class="page-title">
    <i class="fas fa-cog"></i>
    Settings & Configuration
</div>

<!-- System Settings -->
<div class="card mb-4">
    <div class="card-header">System Settings</div>
    <div class="card-body">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">BNB Wallet Address</label>
                    <input type="text" name="bnb_wallet_address" class="form-control" value="{{ $settings->firstWhere('key', 'bnb_wallet_address')->value ?? '' }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Referral Commission (%)</label>
                    <input type="number" name="referral_commission_percent" step="0.01" class="form-control" value="{{ $settings->firstWhere('key', 'referral_commission_percent')->value ?? '10' }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Withdrawal Fee (%)</label>
                    <input type="number" name="withdrawal_fee_percent" step="0.01" class="form-control" value="{{ $settings->firstWhere('key', 'withdrawal_fee_percent')->value ?? '5' }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">OTP Expiry (minutes)</label>
                    <input type="number" name="otp_expiry_minutes" step="1" class="form-control" value="{{ $settings->firstWhere('key', 'otp_expiry_minutes')->value ?? '5' }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Minimum Withdrawal Amount</label>
                    <input type="number" name="min_withdrawal_amount" step="0.01" class="form-control" value="{{ $settings->firstWhere('key', 'min_withdrawal_amount')->value ?? '10' }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Maximum Daily Profit Multiplier</label>
                    <div class="input-group">
                        <input type="number" name="max_daily_profit_multiplier" step="0.1" class="form-control" value="{{ $settings->firstWhere('key', 'max_daily_profit_multiplier')->value ?? '2' }}" required>
                        <span class="input-group-text" style="background: rgba(212, 175, 55, 0.1); border-color: rgba(212, 175, 55, 0.2); color: white;">x Package Value</span>
                    </div>
                    {{-- <small class="form-text text-white d-block mt-2">
                        <i class="fas fa-info-circle"></i> Maximum daily earnings = Package Value × Daily Rate × This Multiplier
                        <br>Example: $1000 package with 0.1% daily rate and 2x multiplier = Max $2 daily profit
                    </small> --}}
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </form>
    </div>
</div>

<!-- Package Management -->
<div class="card">
    <div class="card-header">Investment Packages</div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Package Name</th>
                    <th>Price</th>
                    <th>Daily Rate</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
@foreach($packages as $package)
                <tr>
                    <td><strong>{{ $package->name }}</strong></td>
                    <td>${{ number_format($package->price, 2) }}</td>
                    <td>{{ $package->daily_profit_rate }}%</td>
                    <td>{{ $package->duration_days }} days</td>
                    <td>
                        <span class="badge badge-{{ $package->is_active ? 'success' : 'danger' }}">
                            {{ $package->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editPackage{{ $package->id }}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </td>
                </tr>
@endforeach
@if($packages->isEmpty())
                <tr>
                    <td colspan="6" class="text-center text-muted">No packages available</td>
                </tr>
@endif
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Package Modals -->
@foreach($packages as $package)
<div class="modal fade" id="editPackage{{ $package->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: var(--secondary); border-color: var(--accent);">
            <div class="modal-header" style="border-bottom-color: rgba(212, 175, 55, 0.2);">
                <h5 class="modal-title">Edit {{ $package->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.settings.update-package', $package->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Package Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $package->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" step="0.01" class="form-control" value="{{ $package->price }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Daily Profit Rate (%)</label>
                        <input type="number" name="daily_profit_rate" step="0.01" class="form-control" value="{{ $package->daily_profit_rate }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (days)</label>
                        <input type="number" name="duration_days" step="1" class="form-control" value="{{ $package->duration_days }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select" required>
                            <option value="1" {{ $package->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$package->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top-color: rgba(212, 175, 55, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

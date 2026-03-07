@extends('layouts.admin')

@section('title', 'Referral Commissions')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title text-white">Referral Commission Settings</h1>
            <p class="text-white">Manage the commission percentages for each referral level (up to 5 levels deep)</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Commission Structure</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.referral-commissions.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Level</th>
                                <th style="width: 50%;">Description</th>
                                <th style="width: 20%;">Commission %</th>
                                <th style="width: 15%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($commissions as $commission)
                                <tr>
                                    <td class="align-middle">
                                        <span class="badge bg-primary">Level {{ $commission->level }}</span>
                                    </td>
                                    <td class="align-middle">
                                        @switch($commission->level)
                                            @case(1)
                                                Direct referral commission from user's direct downline
                                                @break
                                            @case(2)
                                                Second level referral commission
                                                @break
                                            @case(3)
                                                Third level referral commission
                                                @break
                                            @case(4)
                                                Fourth level referral commission
                                                @break
                                            @case(5)
                                                Fifth level referral commission
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="hidden" name="commissions[{{ $loop->index }}][id]" value="{{ $commission->id }}">
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="commissions[{{ $loop->index }}][percentage]" 
                                                   value="{{ $commission->percentage }}"
                                                   step="0.01"
                                                   min="0"
                                                   max="100"
                                                   style="color:black"
                                                   required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('commissions.' . $loop->index . '.percentage')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td class="align-middle">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="commissions[{{ $loop->index }}][is_active]"
                                                   value="1"
                                                   {{ $commission->is_active ? 'checked' : '' }}
                                                   id="status-{{ $commission->id }}">
                                            <label class="form-check-label" for="status-{{ $commission->id }}">
                                                {{ $commission->is_active ? 'Active' : 'Inactive' }}
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No commission levels found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">How It Works</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info" role="alert">
                <strong>Referral Commission Distribution:</strong>
                <ul class="mb-0 mt-2">
                    <li>When a user subscribes to a package, their referrer(s) receive commissions</li>
                    <li>Each level up to 5 receives a percentage of the package price</li>
                    <li>Commissions are only paid if the level is active</li>
                    <li>Commission amounts are credited to the referrer's wallet instantly</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .page-title {
        color: #333;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #555;
    }
</style>
@endsection

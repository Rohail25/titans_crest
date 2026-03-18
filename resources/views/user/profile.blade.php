@extends('layouts.user')

@section('page-title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-white">My Profile</h3>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="profile-section">
                        <div class="profile-header mb-4">
                            <div class="profile-avatar">
                                <i class="fas fa-user-circle fa-4x"></i>
                            </div>
                            <div class="profile-info">
                                <h2 class="text-white">{{ $user->name }}</h2>
                                <p class="text-muted">{{ $user->email }}</p>
                                <p class="text-muted">Role: <span class="badge bg-primary">{{ ucfirst($user->role) }}</span></p>
                            </div>
                        </div>

                        <hr>

                        <div class="profile-details">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="label-text">Name</label>
                                    <p class="form-control-static text-white">{{ $user->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="label-text">Email</label>
                                    <p class="form-control-static text-white">{{ $user->email }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="label-text">Referral Code</label>
                                    <p class="form-control-static text-white">{{ $user->referral_code }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="label-text">Status</label>
                                    <p class="form-control-static">
                                        <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="label-text">Member Since</label>
                                    <p class="form-control-static text-white">{{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="label-text">Last Updated</label>
                                    <p class="form-control-static text-white">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="profile-actions">
                            <a href="{{ route('user.profile.edit') }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-header {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .profile-avatar {
        color: #007bff;
    }

    .profile-info h2 {
        margin: 0;
        padding: 0;
    }

    .label-text {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control-static {
        padding: 0.5rem 0;
        color: #212529;
    }

    .profile-actions {
        display: flex;
        gap: 10px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
</style>
@endsection

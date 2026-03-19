@extends('layouts.user')

@section('page-title', 'Change Password')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <h3 class="card-title text-white mb-0">
                        <i class="fas fa-lock"></i> Change Password
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <strong>Error:</strong> Please fix the following:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-lightbulb"></i>
                        <strong>How it works:</strong> Enter your current password below for verification. We'll send a secure link to your email where you can create a new password.
                    </div>

                    <form action="{{ route('password.send-change-email') }}" method="POST" class="mt-4">
                        @csrf

                        <div class="form-group mb-4">
                            <label class="form-label text-white fw-bold">
                                <i class="fas fa-lock"></i> Current Password
                            </label>
                            <input 
                                type="password" 
                                name="current_password" 
                                class="form-control form-control-lg @error('current_password') is-invalid @enderror"
                                placeholder="Enter your current password"
                                required
                                autocomplete="current-password"
                            >
                            @error('current_password')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted d-block mt-2">
                                We need to verify your current password for security.
                            </small>
                        </div>

                        <hr>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                <i class="fas fa-envelope"></i> Send Reset Link to Email
                            </button>
                            <a href="{{ route('user.profile.show') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>

                    <div class="alert alert-warning mt-4">
                        <i class="fas fa-shield-alt"></i>
                        <strong>Security Tips:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Choose a strong password with numbers, letters, and symbols</li>
                            <li>Never share your password with anyone</li>
                            <li>Change your password periodically for better security</li>
                            <li>Reset links expire after 1 hour</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        background-color: #1a1a2e;
        border: none;
        border-radius: 10px;
    }

    .card-header {
        border-radius: 10px 10px 0 0;
        padding: 1.5rem;
    }

    .card-body {
        padding: 2rem;
    }

    .form-label {
        margin-bottom: 0.5rem;
        font-size: 14px;
    }

    .form-control {
        background-color: #2a2a3e;
        border: 2px solid #3a3a4e;
        color: #fff;
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }

    .form-control:focus {
        background-color: #2a2a3e;
        border-color: #007bff;
        color: #fff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
    }

    .form-control::placeholder {
        color: #999;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        background-color: #2a2a3e;
    }

    .form-control.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.25);
    }

    .invalid-feedback {
        color: #ff6b6b !important;
        font-size: 13px;
    }

    .text-muted {
        color: #999 !important;
    }

    .alert {
        border-radius: 8px;
        border: none;
        padding: 1rem;
    }

    .btn {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #5a6268;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    hr {
        background-color: #3a3a4e;
        opacity: 0.5;
    }
</style>
@endsection

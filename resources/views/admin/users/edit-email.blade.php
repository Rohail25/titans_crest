@extends('layouts.admin')

@section('title', 'Edit User Email')

@section('content')
<div class="page-title">
    <i class="fas fa-envelope"></i>
    Edit Email: {{ $user->name }}
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Update User Email</div>
            <div class="card-body">
                <div class="mb-4">
                    <p class="mb-2"><strong>User:</strong> {{ $user->name }}</p>
                    <p class="mb-0"><strong>Current Email:</strong> {{ $user->email }}</p>
                </div>

                <form action="{{ route('admin.users.update-email', $user->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label">New Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}"
                            placeholder="Enter new email address"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning">
                        Changing the email will clear the email verification state for this account.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Email
                        </button>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
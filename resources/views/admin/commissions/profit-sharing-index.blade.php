@extends('layouts.admin')

@section('title', 'Profit Sharing')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Profit Sharing Settings</h1>
            <p class="text-muted">Manage the daily profit sharing percentages for each level (up to 10 levels deep)</p>
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
            <h5 class="card-title mb-0">Profit Sharing Structure</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profit-sharing.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Level</th>
                                <th style="width: 50%;">Description</th>
                                <th style="width: 35%;">Daily Profit Share %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($levels as $level)
                                <tr>
                                    <td class="align-middle">
                                        <span class="badge bg-success">Level {{ $level->level }}</span>
                                    </td>
                                    <td class="align-middle">
                                        Share of user's daily profit distributed to level {{ $level->level }} upline
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="hidden" name="levels[{{ $loop->index }}][id]" value="{{ $level->id }}">
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="levels[{{ $loop->index }}][percentage]" 
                                                   value="{{ $level->percentage }}"
                                                   step="0.01"
                                                   min="0"
                                                   max="100"
                                                   style="color: black"
                                                   required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('levels.' . $loop->index . '.percentage')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No profit sharing levels found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-warning mt-3" role="alert">
                    <strong>Note:</strong> The percentages should represent the portion of daily profits distributed at each level. 
                    The sum of all levels is typically distributed from each user's daily profit amount.
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
                <strong>Daily Profit Sharing Distribution:</strong>
                <ul class="mb-0 mt-2">
                    <li>When a user generates daily profit, it's distributed to their uplines</li>
                    <li>Each level up to 10 receives a percentage of the daily profit</li>
                    <li>Profit amounts are credited to uplines' wallets instantly</li>
                    <li>All transactions are logged in the earnings ledger</li>
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

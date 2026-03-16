@extends('layouts.admin')

@section('title', 'Email Logs')

@section('content')
<div class="page-title">
    <i class="fas fa-envelope"></i>
    Email Logs
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Emails</div>
            <div class="kpi-value">{{ $stats['total_emails'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Sent</div>
            <div class="kpi-value text-success">{{ $stats['sent_emails'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Failed</div>
            <div class="kpi-value text-danger">{{ $stats['failed_emails'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Today Sent</div>
            <div class="kpi-value">{{ $stats['today_sent'] }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email, subject" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                <option value="">All Types</option>
                <option value="otp" {{ request('type') === 'otp' ? 'selected' : '' }}>OTP</option>
                <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                <option value="notification" {{ request('type') === 'notification' ? 'selected' : '' }}>Notification</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort By</label>
                <select name="sort" class="form-select">
                    <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>Date</option>
                    <option value="id" {{ request('sort') === 'id' ? 'selected' : '' }}>ID</option>
                    <option value="recipient" {{ request('sort') === 'recipient' ? 'selected' : '' }}>Recipient</option>
                    <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Status</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">Order</label>
                <select name="direction" class="form-select">
                    <option value="desc" {{ request('direction', 'desc') === 'desc' ? 'selected' : '' }}>Desc</option>
                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Asc</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="{{ route('admin.email-logs.index') }}" class="btn btn-secondary">Reset</a>
                <a href="{{ route('admin.email-logs.failed') }}" class="btn btn-warning">
                <i class="fas fa-exclamation-triangle"></i> Failed Only
            </a>
            </div>
        </form>
    </div>
</div>

<!-- Email Logs Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Email History
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Recipient</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Sent</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>
                            <strong>{{ $log->user->name }}</strong><br>
                            <small class="text-muted">{{ $log->recipient }}</small>
                        </td>
                        <td>{{ $log->subject }}</td>
                        <td><span class="badge badge-secondary">{{ ucfirst($log->type) }}</span></td>
                        <td>
                            <span class="badge badge-{{ $log->status === 'sent' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                @if($log->sent_at)
                                    {{ $log->sent_at->format('M d, Y H:i') }}
                                @else
                                    Not sent
                                @endif
                            </small>
                        </td>
                        <td>
                            <a href="{{ route('admin.email-logs.show', $log->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No email logs found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body" style="border-top: 1px solid rgba(212, 175, 55, 0.1);">
        {{ $logs->links() }}
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Email Log Details')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <i class="fas fa-envelope"></i>
        Email Log Details
    </div>
    <a href="{{ route('admin.email-logs.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Back to Logs
    </a>
</div>

<!-- Email Header Information -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-info-circle"></i> Email Information
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><strong>Recipient</strong></label>
                    <div>{{ $log->recipient }}</div>
                    @if($log->user)
                        <small class="text-muted">
                            User: <a href="{{ route('admin.users.show', $log->user->id) }}">{{ $log->user->name }}</a>
                        </small>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><strong>Type</strong></label>
                    <div>
                        <span class="badge badge-secondary badge-lg">{{ ucfirst($log->type) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><strong>Status</strong></label>
                    <div>
                        <span class="badge badge-{{ $log->status === 'sent' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }} badge-lg">
                            {{ ucfirst($log->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><strong>Sent</strong></label>
                    <div>
                        @if($log->sent_at)
                            {{ $log->sent_at->format('M d, Y H:i:s') }}
                            <br>
                            <small class="text-muted">{{ $log->sent_at->diffForHumans() }}</small>
                        @else
                            <span class="text-muted">Not sent yet</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label"><strong>Created At</strong></label>
                    <div>
                        {{ $log->created_at->format('M d, Y H:i:s') }}
                        <br>
                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Subject -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-heading"></i> Subject
    </div>
    <div class="card-body">
        <h5 class="mb-0">{{ $log->subject }}</h5>
    </div>
</div>

<!-- Email Body -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-envelope-open"></i> Email Content
    </div>
    <div class="card-body">
        <div style="border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; background-color: #f8f9fa; overflow-x: auto;">
            {!! nl2br(e($log->body)) !!}
        </div>
    </div>
</div>

<!-- Error Message (if any) -->
@if($log->status === 'failed' && $log->error_message)
<div class="card mb-4 border-danger">
    <div class="card-header bg-danger text-white">
        <i class="fas fa-exclamation-circle"></i> Error Details
    </div>
    <div class="card-body">
        <div class="alert alert-danger mb-0">
            <pre class="mb-0" style="word-wrap: break-word; white-space: pre-wrap;">{{ $log->error_message }}</pre>
        </div>
    </div>
</div>
@endif

<!-- Email Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Type</div>
            <div class="kpi-value" style="font-size: 1.2rem; text-transform: uppercase;">{{ substr($log->type, 0, 3) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Status</div>
            <div class="kpi-value" style="font-size: 1.2rem; color: {{ $log->status === 'sent' ? '#28a745' : ($log->status === 'failed' ? '#dc3545' : '#ffc107') }};">
                {{ strtoupper(substr($log->status, 0, 4)) }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Recipient Type</div>
            <div class="kpi-value">
                @if(filter_var($log->recipient, FILTER_VALIDATE_EMAIL))
                    EMAIL
                @else
                    OTHER
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Days Ago</div>
            <div class="kpi-value">{{ $log->created_at->diffInDays(now()) }}</div>
        </div>
    </div>
</div>

<!-- Related Emails -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history"></i> Related Emails for {{ $log->recipient }}
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Sent</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $relatedEmails = \App\Models\EmailLog::where('recipient', $log->recipient)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                @forelse($relatedEmails as $email)
                    <tr @if($email->id === $log->id) class="table-active" @endif>
                        <td>{{ Str::limit($email->subject, 40) }}</td>
                        <td><span class="badge badge-secondary">{{ ucfirst($email->type) }}</span></td>
                        <td>
                            <span class="badge badge-{{ $email->status === 'sent' ? 'success' : ($email->status === 'failed' ? 'danger' : 'warning') }}">
                                {{ ucfirst($email->status) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                @if($email->sent_at)
                                    {{ $email->sent_at->format('M d, Y H:i') }}
                                @else
                                    Not sent
                                @endif
                            </small>
                        </td>
                        <td>
                            @if($email->id === $log->id)
                                <span class="badge badge-primary">Current</span>
                            @else
                                <a href="{{ route('admin.email-logs.show', $email->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No related emails found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

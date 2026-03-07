@extends('layouts.admin')

@section('title', 'Audit Log Details')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <i class="fas fa-history"></i>
        Audit Log Details
    </div>
    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Back to Logs
    </a>
</div>

<!-- Main Log Details -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-info-circle"></i> Action Information
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><strong>Admin</strong></label>
                    <div>{{ $log->admin->name }}</div>
                    <small class="text-muted">{{ $log->admin->email }}</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><strong>Action</strong></label>
                    <div>
                        <span class="badge badge-success badge-lg">
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><strong>Target Type</strong></label>
                    <div>
                        <span class="badge badge-info">{{ $log->target_type }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><strong>Target ID</strong></label>
                    <div>
                        <code>#{{ $log->target_id ?? 'N/A' }}</code>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><strong>IP Address</strong></label>
                    <div>{{ $log->ip_address ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><strong>Timestamp</strong></label>
                    <div>{{ $log->created_at->format('M d, Y H:i:s') }}</div>
                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                </div>
            </div>
        </div>

        @if($log->reason)
        <div class="row">
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label"><strong>Reason</strong></label>
                    <div class="alert alert-info mb-0">
                        {{ $log->reason }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Changes Comparison -->
@if($log->old_values || $log->new_values)
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-exchange-alt"></i> Changes Made
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 25%;">Field</th>
                        <th style="width: 35%;">Old Value</th>
                        <th style="width: 35%;">New Value</th>
                    </tr>
                </thead>
                <tbody>
                    @if($log->old_values && $log->new_values)
                        @php
                            $allKeys = array_unique(array_merge(
                                array_keys((array)$log->old_values),
                                array_keys((array)$log->new_values)
                            ));
                        @endphp
                        @foreach($allKeys as $key)
                            @php
                                $oldValue = $log->old_values[$key] ?? null;
                                $newValue = $log->new_values[$key] ?? null;
                            @endphp
                            @if($oldValue !== $newValue)
                            <tr>
                                <td>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong>
                                </td>
                                <td>
                                    @if($oldValue === null)
                                        <span class="text-muted">-</span>
                                    @else
                                        <div class="bg-light p-2 rounded">
                                            <code style="word-break: break-word;">
                                                @if(is_bool($oldValue))
                                                    {{ $oldValue ? 'true' : 'false' }}
                                                @elseif(is_array($oldValue) || is_object($oldValue))
                                                    {{ json_encode($oldValue) }}
                                                @else
                                                    {{ $oldValue }}
                                                @endif
                                            </code>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($newValue === null)
                                        <span class="text-muted">-</span>
                                    @else
                                        <div class="bg-light p-2 rounded">
                                            <code style="word-break: break-word;">
                                                @if(is_bool($newValue))
                                                    {{ $newValue ? 'true' : 'false' }}
                                                @elseif(is_array($newValue) || is_object($newValue))
                                                    {{ json_encode($newValue) }}
                                                @else
                                                    {{ $newValue }}
                                                @endif
                                            </code>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    @elseif($log->new_values)
                        @foreach($log->new_values as $key => $value)
                        <tr>
                            <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                            <td>
                                <span class="text-muted">-</span>
                            </td>
                            <td>
                                <div class="bg-light p-2 rounded">
                                    <code style="word-break: break-word;">
                                        @if(is_bool($value))
                                            {{ $value ? 'true' : 'false' }}
                                        @elseif(is_array($value) || is_object($value))
                                            {{ json_encode($value) }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </code>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No changes recorded</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="alert alert-secondary" role="alert">
    <i class="fas fa-info-circle"></i> No changes were recorded for this action
</div>
@endif

<!-- Related Actions Section -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Related Actions by {{ $log->admin->name }}
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Target</th>
                    <th>Timestamp</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $relatedLogs = \App\Models\AdminLog::where('admin_id', $log->admin_id)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                @forelse($relatedLogs as $relatedLog)
                    <tr @if($relatedLog->id === $log->id) class="table-active" @endif>
                        <td>
                            <span class="badge badge-success">
                                {{ ucfirst(str_replace('_', ' ', $relatedLog->action)) }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $relatedLog->target_type }}</small><br>
                            <code style="font-size: 0.8rem;">#{{ $relatedLog->target_id ?? 'N/A' }}</code>
                        </td>
                        <td>
                            <small class="text-muted">{{ $relatedLog->created_at->format('M d, Y H:i') }}</small>
                        </td>
                        <td>
                            @if($relatedLog->id === $log->id)
                                <span class="badge badge-primary">Current</span>
                            @else
                                <a href="{{ route('admin.audit-logs.show', $relatedLog->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No related actions found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

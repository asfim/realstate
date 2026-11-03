@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Immutable System Audit Logs</h4>
        <p class="text-muted small mb-0">Complete audit trail of ERP mutations, authorization changes, and state transitions.</p>
    </div>
    <span class="badge bg-dark px-3 py-2">
        <i class="bi bi-lock-fill text-warning me-1"></i> Append-Only Trail
    </span>
</div>

<!-- Filters -->
<div class="card card-custom mb-4 p-3">
    <form method="GET" action="{{ route('erp.audit-logs.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="action" class="form-control form-control-sm" placeholder="Filter by action name (e.g. company.created, branch)..." value="{{ request('action') }}">
        </div>
        <div class="col-md-4">
            <input type="text" name="subject_type" class="form-control form-control-sm" placeholder="Filter by subject model..." value="{{ request('subject_type') }}">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm px-3 flex-grow-1"><i class="bi bi-funnel me-1"></i> Filter</button>
            <a href="{{ route('erp.audit-logs.index') }}" class="btn btn-light btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Timestamp</th>
                    <th>Actor</th>
                    <th>Action</th>
                    <th>Target Subject</th>
                    <th>Branch</th>
                    <th>IP Address</th>
                    <th class="text-end">Payload</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>
                            <small class="fw-semibold text-dark">{{ $log->created_at->format('d M Y, h:i:s A') }}</small>
                            <small class="d-block text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="fw-medium text-dark">{{ $log->actor?->name ?? 'System Worker' }}</div>
                            <small class="text-muted">{{ class_basename($log->actor_type ?? 'System') }} #{{ $log->actor_id ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary font-monospace">{{ $log->action }}</span>
                        </td>
                        <td>
                            @if($log->subject_type)
                                <span class="badge bg-light text-dark border">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</span>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $log->branch?->name ?? 'Global' }}</small>
                        </td>
                        <td>
                            <small class="font-monospace text-muted">{{ $log->ip_address ?? '127.0.0.1' }}</small>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#payloadModal{{ $log->id }}">
                                <i class="bi bi-code-slash me-1"></i> Diff
                            </button>
                        </td>
                    </tr>

                    <!-- Payload Inspection Modal -->
                    <div class="modal fade" id="payloadModal{{ $log->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Audit Payload: {{ $log->action }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-danger mb-2">State Before</h6>
                                            <pre class="bg-light p-3 rounded small border" style="max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->before_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?? 'null' }}</code></pre>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-success mb-2">State After</h6>
                                            <pre class="bg-light p-3 rounded small border" style="max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->after_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?? 'null' }}</code></pre>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-muted small">
                                        <strong>User Agent:</strong> {{ $log->user_agent ?? 'N/A' }}<br>
                                        <strong>Request ID:</strong> {{ $log->request_id ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No audit trail entries matching filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="p-3 border-top">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection


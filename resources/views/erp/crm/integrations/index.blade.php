@extends('erp.layouts.app')

@section('content')
<div class="erp-header-row mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-meta text-primary me-2"></i> Meta (Facebook & WhatsApp) Ad Integrations
        </h4>
        <p class="text-muted small mb-0">Automatic real-time synchronization for Facebook Lead Ads and WhatsApp Business leads directly into ERP CRM.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.leads.index') }}" class="btn btn-outline-dark btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Leads
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Metric Overview Cards -->
<div class="row g-2 g-sm-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card card-custom p-2 p-sm-3 border-0 bg-primary bg-opacity-10 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-primary fw-bold" style="font-size: 0.68rem;">Ad Leads Synced</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1">{{ number_format($totalLogs) }}</h3>
                </div>
                <div class="bg-primary text-white p-2 p-sm-3 rounded-circle">
                    <i class="bi bi-cloud-arrow-down fs-5 fs-sm-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-custom p-2 p-sm-3 border-0 bg-info bg-opacity-10 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-info fw-bold" style="font-size: 0.68rem;">Facebook Forms</span>
                    <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($facebookCount) }}</h3>
                </div>
                <div class="bg-info text-white p-2 p-sm-3 rounded-circle">
                    <i class="bi bi-facebook fs-5 fs-sm-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-custom p-2 p-sm-3 border-0 bg-success bg-opacity-10 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-success fw-bold" style="font-size: 0.68rem;">WhatsApp Leads</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($whatsappCount) }}</h3>
                </div>
                <div class="bg-success text-white p-2 p-sm-3 rounded-circle">
                    <i class="bi bi-whatsapp fs-5 fs-sm-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-custom p-2 p-sm-3 border-0 bg-warning bg-opacity-10 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-warning fw-bold" style="font-size: 0.68rem;">Synced Today</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($todayCount) }}</h3>
                </div>
                <div class="bg-warning text-white p-2 p-sm-3 rounded-circle">
                    <i class="bi bi-calendar2-check fs-5 fs-sm-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Webhook Endpoints & Setup Information -->
    <div class="col-lg-7">
        <div class="card card-custom h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-link-45deg text-primary me-2"></i> Meta Webhook URLs for Ad Configuration
                </h6>
                <span class="badge bg-success">Active & Ready</span>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    Copy these URLs into your <strong>Meta App Dashboard (developers.facebook.com)</strong> or <strong>WhatsApp Cloud API</strong> settings. When leads submit Facebook ad forms or send messages to your WhatsApp business number, they will instantly appear in ERP CRM.
                </p>

                <!-- Facebook Lead Ads URL -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">
                        <i class="bi bi-facebook text-primary me-1"></i> Facebook Lead Ads Webhook URL
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control font-monospace bg-light" id="fbWebhookUrl" value="{{ $facebookWebhookUrl }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('fbWebhookUrl', this)">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">Meta Event to subscribe: <code>leadgen</code></small>
                </div>

                <!-- WhatsApp Cloud API URL -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">
                        <i class="bi bi-whatsapp text-success me-1"></i> WhatsApp Business Webhook URL (Click-to-WhatsApp Ads)
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control font-monospace bg-light" id="waWebhookUrl" value="{{ $whatsappWebhookUrl }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('waWebhookUrl', this)">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">Meta Event to subscribe: <code>messages</code></small>
                </div>

                <!-- Universal Endpoint -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">
                        <i class="bi bi-globe text-info me-1"></i> Universal Meta Webhook URL (Combined FB & WA)
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control font-monospace bg-light" id="uniWebhookUrl" value="{{ $universalWebhookUrl }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('uniWebhookUrl', this)">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                </div>

                <!-- Direct API Endpoint for Zapier / Relay -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">
                        <i class="bi bi-arrow-left-right text-warning me-1"></i> Direct Lead Ingest API (Zapier / Make / Webhook Relay)
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control font-monospace bg-light" id="directApiUrl" value="{{ $directIngestUrl }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('directApiUrl', this)">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">Accepts POST JSON with <code>{ "name", "phone", "email", "source", "ad_name", "campaign_name", "notes" }</code></small>
                </div>

                <!-- Webhook Verify Token -->
                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Meta Webhook Verify Token:</span>
                            <span class="font-monospace fw-bold text-dark fs-6" id="tokenDisplay">{{ $verifyToken }}</span>
                        </div>
                        <button class="btn btn-sm btn-outline-dark" type="button" onclick="copyText('{{ $verifyToken }}', this)">
                            <i class="bi bi-key me-1"></i> Copy Token
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Integration Settings & Simulator -->
    <div class="col-lg-5">
        <!-- Settings Form -->
        <div class="card card-custom mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-sliders text-secondary me-2"></i> Meta API Configuration
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('erp.crm-integrations.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Webhook Verify Token <span class="text-danger">*</span></label>
                        <input type="text" name="meta_verify_token" class="form-control form-control-sm font-monospace" value="{{ old('meta_verify_token', $verifyToken) }}" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Enter this exact token in Meta App Dashboard during webhook verification.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Meta Page Access Token (Optional)</label>
                        <input type="password" name="meta_page_access_token" class="form-control form-control-sm font-monospace" value="{{ old('meta_page_access_token', $pageAccessToken) }}" placeholder="EAAG...">
                        <small class="text-muted" style="font-size: 0.72rem;">Used to query Graph API to retrieve complete custom form answers.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">WhatsApp Cloud API Phone ID (Optional)</label>
                        <input type="text" name="whatsapp_phone_number_id" class="form-control form-control-sm font-monospace" value="{{ old('whatsapp_phone_number_id', $whatsappPhoneId) }}" placeholder="e.g. 102938475610293">
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="meta_auto_assign" id="autoAssignCheck" value="1" {{ $autoAssign ? 'checked' : '' }}>
                        <label class="form-check-label small fw-semibold" for="autoAssignCheck">
                            Round-Robin Sales Agent Auto Assignment
                        </label>
                        <small class="text-muted d-block" style="font-size: 0.72rem;">Evenly distributes incoming Meta and WhatsApp leads among active agents.</small>
                    </div>

                    <button type="submit" class="btn btn-erp-gold btn-sm w-100">
                        <i class="bi bi-check2 me-1"></i> Save Configuration
                    </button>
                </form>
            </div>
        </div>

        <!-- Live Webhook Test Simulator -->
        <div class="card card-custom border-primary border-opacity-25 bg-primary bg-opacity-10">
            <div class="card-header bg-transparent py-3 border-bottom border-primary border-opacity-25">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-lightning-charge-fill me-2"></i> Real-Time Sync Simulator
                </h6>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    Click either button below to simulate an incoming ad lead from Facebook or WhatsApp. It will test the complete pipeline: ingestion, sequence coding, round-robin agent assignment, and audit logging.
                </p>

                <div class="d-grid gap-2">
                    <form action="{{ route('erp.crm-integrations.test') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="facebook">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-facebook me-2"></i> Simulate Facebook Lead Ad Sync
                        </button>
                    </form>

                    <form action="{{ route('erp.crm-integrations.test') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="whatsapp">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-whatsapp me-2"></i> Simulate Click-to-WhatsApp Lead Sync
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Webhook Logs Table -->
<div class="card card-custom">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-clock-history text-secondary me-2"></i> Inbound Ad Webhook Sync History
            </h6>
            <small class="text-muted">Live event stream of incoming Facebook and WhatsApp ad submissions.</small>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Timestamp</th>
                    <th>Platform</th>
                    <th>Lead Name & Phone</th>
                    <th>Campaign & Ad</th>
                    <th>Message / Details</th>
                    <th>ERP CRM Lead</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="small text-muted font-monospace" style="font-size: 0.78rem;">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}<br>
                            <span style="font-size: 0.72rem;">{{ $log->created_at->diffForHumans() }}</span>
                        </td>
                        <td>
                            @if($log->platform === 'facebook')
                                <span class="badge bg-primary px-2 py-1"><i class="bi bi-facebook me-1"></i> Facebook</span>
                            @elseif($log->platform === 'whatsapp')
                                <span class="badge bg-success px-2 py-1"><i class="bi bi-whatsapp me-1"></i> WhatsApp</span>
                            @else
                                <span class="badge bg-secondary px-2 py-1">{{ ucfirst($log->platform) }}</span>
                            @endif
                            <small class="d-block text-muted" style="font-size: 0.7rem;">{{ $log->event_type }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $log->sender_name ?? 'N/A' }}</div>
                            <div class="small text-muted">
                                <i class="bi bi-telephone text-primary me-1"></i> {{ $log->sender_phone ?? 'N/A' }}
                                @if($log->sender_email)
                                    <span class="mx-1">&bull;</span>
                                    <i class="bi bi-envelope text-secondary me-1"></i> {{ $log->sender_email }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-truncate text-dark small" style="max-width: 220px;" title="{{ $log->campaign_name }}">
                                <i class="bi bi-megaphone me-1 text-muted"></i> {{ $log->campaign_name ?? 'General Ad' }}
                            </div>
                            <small class="text-muted d-block text-truncate" style="max-width: 220px;" title="{{ $log->ad_name }}">
                                {{ $log->ad_name ?? 'Sponsored Ad' }}
                            </small>
                        </td>
                        <td>
                            <div class="small text-muted text-truncate" style="max-width: 260px;" title="{{ $log->message_body }}">
                                {{ $log->message_body ?: 'Form submitted' }}
                            </div>
                        </td>
                        <td>
                            @if($log->lead)
                                <a href="{{ route('erp.leads.show', $log->lead) }}" class="badge bg-dark text-white text-decoration-none font-monospace p-2">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> {{ $log->lead->lead_code }}
                                </a>
                                <small class="d-block text-muted mt-1">{{ ucfirst($log->lead->stage) }}</small>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                        <td>
                            @if($log->status === 'synced')
                                <span class="badge bg-success">Synced (New)</span>
                            @elseif($log->status === 'updated_existing')
                                <span class="badge bg-info">Re-engagement</span>
                            @elseif($log->status === 'failed')
                                <span class="badge bg-danger">Failed</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            No webhook events received yet. Click "Simulate Facebook Lead Ad Sync" or "Simulate WhatsApp Lead Sync" above to test.
                        </td>
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

@push('scripts')
<script>
function copyToClipboard(elementId, btn) {
    const input = document.getElementById(elementId);
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg text-success"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    });
}

function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg text-success"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    });
}
</script>
@endpush
@endsection


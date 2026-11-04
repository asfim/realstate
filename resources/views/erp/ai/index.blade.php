@extends('erp.layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #090e1a 0%, #1b2436 100%); color: #fff;">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 58px; height: 58px; background: rgba(203, 161, 53, 0.15); border: 2px solid #cba135;">
                        <i class="bi bi-robot fs-2 text-warning"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="mb-0 fw-bold text-white">AI Database Assistant</h4>
                            <span class="badge bg-warning text-dark px-2 py-1 fw-bold" style="font-size: 0.7rem;">Live DB Agent</span>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success px-2 py-1" style="font-size: 0.7rem;">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Active
                            </span>
                        </div>
                        <p class="mb-0 text-secondary small mt-1">
                            রিয়েল এস্টেট ইআরপি এবং সিএমএস ডাটাবেসের সাথে সংযুক্ত সার্বক্ষণিক বুদ্ধিমান সহকারী। বাংলা, বাংলিশ ও ইংরেজিতে ডাটাভিত্তিক যেকোনো প্রশ্ন করুন।
                        </p>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-light d-flex align-items-center gap-1" id="clearChatBtn">
                        <i class="bi bi-trash3"></i> চ্যাট রিসেট
                    </button>
                    <a href="{{ route('erp.dashboard') }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </div>
            </div>

            <!-- Fast Metric Bar -->
            <div class="row g-3 mt-3 pt-3 border-top border-secondary border-opacity-25">
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-2 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25">
                        <div class="text-uppercase text-secondary" style="font-size: 0.68rem; font-weight: 700;">আজকের সেল</div>
                        <div class="fs-6 fw-bold text-white mt-1">{{ $overview['today_sales_count'] ?? 0 }} টি</div>
                        <small class="text-warning text-truncate d-block" style="font-size: 0.72rem;">BDT {{ number_format($overview['today_sales_value'] ?? 0, 0) }}</small>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-2 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25">
                        <div class="text-uppercase text-secondary" style="font-size: 0.68rem; font-weight: 700;">আজকের নিট লাভ</div>
                        <div class="fs-6 fw-bold {{ ($overview['today_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} mt-1">
                            BDT {{ number_format($overview['today_profit'] ?? 0, 0) }}
                        </div>
                        <small class="text-secondary text-truncate d-block" style="font-size: 0.72rem;">ইনকাম - খরচ</small>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-2 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25">
                        <div class="text-uppercase text-secondary" style="font-size: 0.68rem; font-weight: 700;">মোট বকেয়া (Dues)</div>
                        <div class="fs-6 fw-bold text-white mt-1">BDT {{ number_format($overview['total_dues'] ?? 0, 0) }}</div>
                        <small class="text-danger text-truncate d-block" style="font-size: 0.72rem;">ওভারডিউ: BDT {{ number_format($overview['overdue_dues'] ?? 0, 0) }}</small>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-2 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25">
                        <div class="text-uppercase text-secondary" style="font-size: 0.68rem; font-weight: 700;">খালি ফ্ল্যাট / ইউনিট</div>
                        <div class="fs-6 fw-bold text-white mt-1">{{ $overview['available_units'] ?? 0 }} টি</div>
                        <small class="text-secondary text-truncate d-block" style="font-size: 0.72rem;">মোট ইউনিট: {{ $overview['total_units'] ?? 0 }} টি</small>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-2 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25">
                        <div class="text-uppercase text-secondary" style="font-size: 0.68rem; font-weight: 700;">সিআরএম লিডস</div>
                        <div class="fs-6 fw-bold text-white mt-1">{{ $overview['today_leads'] ?? 0 }} টি আজ</div>
                        <small class="text-secondary text-truncate d-block" style="font-size: 0.72rem;">মোট ডাটাবেস: {{ $overview['total_leads'] ?? 0 }} টি</small>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-2 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25">
                        <div class="text-uppercase text-secondary" style="font-size: 0.68rem; font-weight: 700;">ডাটাবেস কানেকশন</div>
                        <div class="fs-6 fw-bold text-success mt-1"><i class="bi bi-hdd-network-fill me-1"></i> Synchronized</div>
                        <small class="text-secondary text-truncate d-block" style="font-size: 0.72rem;">{{ $currentCompany->code ?? 'AURELIA' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Chat Workspace Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="min-height: 580px; display: flex; flex-direction: column;">
        
        <!-- Conversation Stream -->
        <div class="card-body p-4 flex-grow-1 overflow-y-auto" id="chatStream" style="max-height: 60vh; background-color: #f8fafc;">
            
            <!-- Bot Initial Message -->
            <div class="d-flex gap-3 mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: #090e1a; color: #cba135;">
                    <i class="bi bi-robot fs-5"></i>
                </div>
                <div class="flex-grow-1" style="max-width: 85%;">
                    <div class="p-3 rounded-4 shadow-sm bg-white border border-light">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-dark small">Aurelia AI Database Assistant</span>
                            <span class="text-muted" style="font-size: 0.72rem;">Just now</span>
                        </div>
                        <div class="ai-rendered-content">
                            <p class="mb-2">
                                আসসালামু আলাইকুম! আমি আপনার রিয়েল এস্টেট ইআরপি এবং সিএমএস ডাটাবেসের সাথে সার্বক্ষণিক সংযুক্ত।
                            </p>
                            <p class="mb-2">
                                আপনি নিচের যেকোনো প্রশ্ন করতে পারেন অথবা সরাসরি আপনার ভাষায় প্রশ্ন লিখুন:
                            </p>
                            <div class="d-flex flex-wrap gap-2 mt-3" id="initialSuggestions">
                                @foreach($suggestions as $s)
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill suggestion-chip" data-query="{{ $s }}">
                                        <i class="bi bi-stars text-warning me-1"></i> {{ $s }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Chat Input Footer -->
        <div class="card-footer bg-white p-3 border-top">
            
            <!-- Quick Chips Bar -->
            <div class="d-flex align-items-center gap-2 overflow-x-auto pb-2 mb-2" id="quickPillsBar" style="scrollbar-width: none;">
                <span class="text-muted small text-nowrap fw-semibold me-1"><i class="bi bi-lightning-charge-fill text-warning"></i> কুইক প্রশ্ন:</span>
                <button type="button" class="btn btn-xs btn-light border text-nowrap rounded-pill py-1 px-3 suggestion-chip" data-query="আজকের সেল কয়টা হয়েছে?">
                    🎯 আজকের সেল কয়টা?
                </button>
                <button type="button" class="btn btn-xs btn-light border text-nowrap rounded-pill py-1 px-3 suggestion-chip" data-query="আজকের লাভ কত?">
                    💰 আজকের লাভ কত?
                </button>
                <button type="button" class="btn btn-xs btn-light border text-nowrap rounded-pill py-1 px-3 suggestion-chip" data-query="কত টাকা বকেয়া আছে?">
                    ⚠️ বকেয়া (Due) কত?
                </button>
                <button type="button" class="btn btn-xs btn-light border text-nowrap rounded-pill py-1 px-3 suggestion-chip" data-query="ইউনিট কয়টা বাকি আছে?">
                    🏢 খালি ফ্ল্যাট কয়টা?
                </button>
                <button type="button" class="btn btn-xs btn-light border text-nowrap rounded-pill py-1 px-3 suggestion-chip" data-query="আজকের সাইট লেবার কয়জন?">
                    👷 সাইট লেবার হাজিরা
                </button>
                <button type="button" class="btn btn-xs btn-light border text-nowrap rounded-pill py-1 px-3 suggestion-chip" data-query="কয়টা লিড এসেছে?">
                    👥 লিড পরিস্থিতি
                </button>
                <button type="button" class="btn btn-xs btn-light border text-nowrap rounded-pill py-1 px-3 suggestion-chip" data-query="সার্বিক অবস্থা কেমন?">
                    📊 এক্সিকিউটিভ রিপোর্ট
                </button>
            </div>

            <!-- Input Form -->
            <form id="aiChatForm" class="d-flex align-items-center gap-2">
                @csrf
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-chat-dots-fill text-warning"></i>
                    </span>
                    <input type="text" id="userQueryInput" class="form-control bg-light border-start-0 py-2 shadow-none" placeholder="আপনার প্রশ্নটি লিখুন (যেমন: ajker sell koyta hoise / ajker profit koto / due ase koto)..." autocomplete="off" required>
                </div>
                <button type="submit" class="btn btn-warning px-4 py-2 fw-semibold d-flex align-items-center gap-2" id="sendBtn" style="background-color: #cba135; border-color: #cba135; color: #000;">
                    <span id="sendBtnText">জিজ্ঞেস করুন</span>
                    <i class="bi bi-send-fill" id="sendBtnIcon"></i>
                    <span class="spinner-border spinner-border-sm d-none" id="sendBtnSpinner" role="status"></span>
                </button>
            </form>

            <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                <small class="text-muted" style="font-size: 0.72rem;">
                    <i class="bi bi-shield-check text-success me-1"></i> ১০০% নির্ভুল লাইভ ডাটাবেস এনালাইসিস। বাংলা বা ইংরেজিতে প্রশ্ন করা যাবে।
                </small>
                <small class="text-secondary" style="font-size: 0.72rem;" id="engineBadge">
                    Powered by: Aurelia Intelligent Agent
                </small>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    .ai-rendered-content table {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 12px;
        border-collapse: collapse;
        font-size: 0.85rem;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
    }
    .ai-rendered-content table th {
        background-color: #090e1a;
        color: #f8fafc;
        padding: 8px 12px;
        font-weight: 600;
        border: 1px solid #1e293b;
    }
    .ai-rendered-content table td {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        color: #334155;
    }
    .ai-rendered-content table tr:nth-child(even) {
        background-color: #f8fafc;
    }
    .ai-rendered-content h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .ai-rendered-content h4 {
        font-size: 0.95rem;
        font-weight: 600;
        color: #334155;
        margin-top: 12px;
        margin-bottom: 6px;
    }
    .ai-rendered-content blockquote {
        margin: 8px 0;
        padding: 10px 14px;
        background-color: #f0fdf4;
        border-left: 4px solid #10b981;
        border-radius: 6px;
        font-weight: 600;
        color: #166534;
    }
    .ai-rendered-content p {
        margin-bottom: 6px;
        line-height: 1.5;
    }
    .ai-rendered-content ul {
        padding-left: 20px;
        margin-bottom: 8px;
    }
    .ai-rendered-content code {
        background: #f1f5f9;
        color: #0f172a;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.85rem;
    }
    .suggestion-chip {
        font-size: 0.78rem;
        transition: all 0.2s;
    }
    .suggestion-chip:hover {
        background-color: #cba135 !important;
        border-color: #cba135 !important;
        color: #000 !important;
        transform: translateY(-1px);
    }
</style>
@endpush

@push('scripts')
<!-- Marked.js for fast client-side markdown parsing -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatStream = document.getElementById('chatStream');
    const chatForm = document.getElementById('aiChatForm');
    const queryInput = document.getElementById('userQueryInput');
    const sendBtn = document.getElementById('sendBtn');
    const sendBtnText = document.getElementById('sendBtnText');
    const sendBtnIcon = document.getElementById('sendBtnIcon');
    const sendBtnSpinner = document.getElementById('sendBtnSpinner');
    const clearBtn = document.getElementById('clearChatBtn');
    const engineBadge = document.getElementById('engineBadge');

    // Scroll to bottom of chat
    function scrollToBottom() {
        chatStream.scrollTop = chatStream.scrollHeight;
    }

    // Attach click events to suggestion chips
    function attachSuggestionHandlers() {
        document.querySelectorAll('.suggestion-chip').forEach(btn => {
            btn.removeEventListener('click', handleChipClick);
            btn.addEventListener('click', handleChipClick);
        });
    }

    function handleChipClick(e) {
        const query = this.getAttribute('data-query');
        if (query) {
            queryInput.value = query;
            chatForm.dispatchEvent(new Event('submit'));
        }
    }

    attachSuggestionHandlers();

    // Render User Message
    function appendUserMessage(text) {
        const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const html = `
            <div class="d-flex gap-3 mb-4 justify-content-end">
                <div class="flex-grow-1 text-end" style="max-width: 80%;">
                    <div class="p-3 rounded-4 shadow-sm bg-primary text-white d-inline-block text-start" style="background: linear-gradient(135deg, #090e1a 0%, #1e293b 100%) !important;">
                        <div class="d-flex align-items-center justify-content-between mb-1 gap-3">
                            <span class="fw-bold small text-warning">You</span>
                            <span class="text-secondary" style="font-size: 0.7rem;">${timeStr}</span>
                        </div>
                        <div class="fw-medium">${escapeHtml(text)}</div>
                    </div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: #cba135; color: #000;">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
            </div>
        `;
        chatStream.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    // Render Bot Loading Skeleton
    function appendLoadingIndicator() {
        const id = 'loading_' + Date.now();
        const html = `
            <div class="d-flex gap-3 mb-4" id="${id}">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: #090e1a; color: #cba135;">
                    <i class="bi bi-robot fs-5"></i>
                </div>
                <div class="flex-grow-1" style="max-width: 85%;">
                    <div class="p-3 rounded-4 shadow-sm bg-white border border-light">
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <div class="spinner-grow spinner-grow-sm text-warning" role="status"></div>
                            <span class="small fw-semibold">ডাটাবেস অনুসন্ধান করা হচ্ছে...</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        chatStream.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
        return id;
    }

    // Render Bot Answer
    function appendBotMessage(data, loadingId) {
        if (loadingId) {
            const el = document.getElementById(loadingId);
            if (el) el.remove();
        }

        const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const parsedMarkdown = marked.parse(data.answer || 'কোনো ফলাফল পাওয়া যায়নি।');

        let suggestionsHtml = '';
        if (data.suggestions && data.suggestions.length > 0) {
            suggestionsHtml = `
                <div class="mt-3 pt-2 border-top">
                    <small class="text-muted d-block mb-2 fw-semibold"><i class="bi bi-lightbulb-fill text-warning me-1"></i> আরো জানতে পারেন:</small>
                    <div class="d-flex flex-wrap gap-2">
                        ${data.suggestions.map(s => `
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill suggestion-chip" data-query="${s}">
                                ${s}
                            </button>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        const html = `
            <div class="d-flex gap-3 mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: #090e1a; color: #cba135;">
                    <i class="bi bi-robot fs-5"></i>
                </div>
                <div class="flex-grow-1" style="max-width: 85%;">
                    <div class="p-3 rounded-4 shadow-sm bg-white border border-light">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark small">Aurelia AI Database Assistant</span>
                                <span class="badge bg-light text-secondary border px-1" style="font-size:0.65rem;">${data.powered_by || 'Real-time Aggregator'}</span>
                            </div>
                            <span class="text-muted" style="font-size: 0.72rem;">${timeStr}</span>
                        </div>
                        <div class="ai-rendered-content text-dark">
                            ${parsedMarkdown}
                        </div>
                        ${suggestionsHtml}
                    </div>
                </div>
            </div>
        `;

        chatStream.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
        attachSuggestionHandlers();

        if (data.powered_by) {
            engineBadge.innerText = 'Engine: ' + data.powered_by;
        }
    }

    // Submit handler
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const query = queryInput.value.trim();
        if (!query) return;

        appendUserMessage(query);
        queryInput.value = '';
        queryInput.disabled = true;
        sendBtn.disabled = true;
        sendBtnText.classList.add('d-none');
        sendBtnIcon.classList.add('d-none');
        sendBtnSpinner.classList.remove('d-none');

        const loadingId = appendLoadingIndicator();

        fetch("{{ route('erp.ai-assistant.query') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ query: query })
        })
        .then(response => {
            if (!response.ok) throw new Error('HTTP status ' + response.status);
            return response.json();
        })
        .then(res => {
            if (res.success && res.data) {
                appendBotMessage(res.data, loadingId);
            } else {
                appendBotMessage({ answer: 'দুঃখিত, কোনো উত্তর পাওয়া যায়নি।' }, loadingId);
            }
        })
        .catch(err => {
            console.error(err);
            appendBotMessage({ answer: '⚠️ সার্ভারের সাথে সংযোগে সমস্যা হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।' }, loadingId);
        })
        .finally(() => {
            queryInput.disabled = false;
            sendBtn.disabled = false;
            sendBtnText.classList.remove('d-none');
            sendBtnIcon.classList.remove('d-none');
            sendBtnSpinner.classList.add('d-none');
            queryInput.focus();
        });
    });

    // Clear Chat
    clearBtn.addEventListener('click', function () {
        if (confirm('আপনি কি সম্পূর্ণ চ্যাট হিস্ট্রি রিসেট করতে চান?')) {
            location.reload();
        }
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }
});
</script>
@endpush


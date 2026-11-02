@extends('backend.layouts.auth')

@section('styles')
<style>
    .auth-wrapper {
        max-width: 395px;
        width: 100%;
        margin: 0 auto;
        padding: 0 12px;
        position: relative;
    }

    /* Unique Card Outer Glow Frame */
    .auth-card-frame {
        position: relative;
        border-radius: 22px;
        padding: 1px;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.45) 0%, rgba(255, 255, 255, 0.05) 50%, rgba(212, 175, 55, 0.2) 100%);
        box-shadow: 0 30px 70px -15px rgba(0, 0, 0, 0.9), 0 0 40px -10px rgba(212, 175, 55, 0.18);
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
    }

    .auth-card-frame:hover {
        transform: translateY(-3px);
        box-shadow: 0 35px 80px -12px rgba(0, 0, 0, 0.95), 0 0 55px -6px rgba(212, 175, 55, 0.28);
    }

    /* Architectural Corner Crosshairs */
    .corner-crosshair {
        position: absolute;
        width: 10px;
        height: 10px;
        pointer-events: none;
        z-index: 5;
    }
    .corner-tl { top: -4px; left: -4px; border-top: 2px solid #D4AF37; border-left: 2px solid #D4AF37; }
    .corner-tr { top: -4px; right: -4px; border-top: 2px solid #D4AF37; border-right: 2px solid #D4AF37; }
    .corner-bl { bottom: -4px; left: -4px; border-bottom: 2px solid #D4AF37; border-left: 2px solid #D4AF37; }
    .corner-br { bottom: -4px; right: -4px; border-bottom: 2px solid #D4AF37; border-right: 2px solid #D4AF37; }

    /* Inner Card Surface */
    .auth-card {
        background: radial-gradient(circle at 50% 0%, rgba(26, 32, 48, 0.92) 0%, rgba(12, 16, 26, 0.96) 100%);
        backdrop-filter: blur(28px);
        -webkit-backdrop-filter: blur(28px);
        border-radius: 21px;
        padding: 28px 22px 24px;
        position: relative;
        overflow: hidden;
    }

    /* Ambient top gold ray */
    .auth-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 20%;
        right: 20%;
        height: 1px;
        background: linear-gradient(90deg, transparent, #D4AF37, #fff, #D4AF37, transparent);
        box-shadow: 0 0 18px rgba(212, 175, 55, 0.9);
    }

    /* Brand Section */
    .brand-section {
        text-align: center;
        margin-bottom: 20px;
    }

    .luxury-crest {
        width: 52px;
        height: 52px;
        margin: 0 auto 12px;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.25) 0%, rgba(15, 20, 32, 0.9) 100%);
        border: 1px solid rgba(212, 175, 55, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.45rem;
        box-shadow: 0 10px 25px -4px rgba(212, 175, 55, 0.3);
        position: relative;
        transition: transform 0.3s ease;
    }

    .luxury-crest::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 20px;
        border: 1px dashed rgba(212, 175, 55, 0.3);
        animation: spinCrest 25s linear infinite;
    }

    @keyframes spinCrest {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .brand-title-gold {
        font-family: 'Cinzel', serif;
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: 2px;
        margin-bottom: 4px;
        background: linear-gradient(135deg, #ffffff 10%, #E6CA65 60%, #AA8529 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 0 30px rgba(212, 175, 55, 0.3);
    }

    .brand-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(212, 175, 55, 0.2);
        padding: 3px 10px;
        border-radius: 30px;
        font-size: 0.68rem;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* VIP Demo Keycard - Unique standout widget */
    .vip-keycard {
        background: linear-gradient(145deg, rgba(22, 28, 44, 0.9) 0%, rgba(14, 18, 28, 0.95) 100%);
        border: 1px solid rgba(212, 175, 55, 0.3);
        border-radius: 14px;
        padding: 11px 12px;
        margin-bottom: 18px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .vip-keycard::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: linear-gradient(180deg, #D4AF37 0%, transparent 100%);
    }

    .vip-keycard:hover {
        border-color: rgba(212, 175, 55, 0.55);
        box-shadow: 0 8px 20px -4px rgba(212, 175, 55, 0.2);
    }

    .vip-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .vip-badge {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--primary);
        letter-spacing: 0.6px;
        display: flex;
        align-items: center;
        gap: 5px;
        text-transform: uppercase;
    }

    .vip-chip-icon {
        font-size: 0.85rem;
        color: #fef08a;
    }

    .vip-pill-status {
        background: rgba(212, 175, 55, 0.12);
        border: 1px solid rgba(212, 175, 55, 0.3);
        color: #fef08a;
        font-size: 0.65rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 12px;
    }

    .vip-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        margin-bottom: 8px;
    }

    .vip-box {
        background: rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        padding: 5px 7px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .vip-label {
        font-size: 0.64rem;
        color: var(--text-subtle);
        display: block;
        line-height: 1.1;
    }

    .vip-text {
        font-family: monospace;
        font-size: 0.72rem;
        color: #f1f5f9;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100px;
    }

    .vip-copy-btn {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 2px 4px;
        cursor: pointer;
        font-size: 0.72rem;
        transition: color 0.2s;
        flex-shrink: 0;
    }

    .vip-copy-btn:hover {
        color: var(--primary);
    }

    .vip-actions {
        display: flex;
        gap: 6px;
    }

    .btn-vip-fill {
        flex: 1;
        background: rgba(212, 175, 55, 0.12);
        border: 1px solid rgba(212, 175, 55, 0.35);
        color: #fef08a;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 5px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .btn-vip-fill:hover {
        background: rgba(212, 175, 55, 0.24);
        color: #fff;
    }

    .btn-vip-instant {
        flex: 1;
        background: linear-gradient(135deg, #D4AF37 0%, #B89228 100%);
        border: none;
        color: #0b0f19;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 5px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .btn-vip-instant:hover {
        background: linear-gradient(135deg, #f0cf65 0%, #caa02e 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
    }

    /* Input Fields */
    .lux-field {
        margin-bottom: 15px;
        position: relative;
    }

    .lux-field-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #cbd5e1;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .lux-input-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .lux-input-icon {
        position: absolute;
        left: 13px;
        color: var(--text-muted);
        font-size: 0.86rem;
        pointer-events: none;
        transition: color 0.3s ease;
    }

    .lux-input {
        width: 100%;
        background: rgba(9, 13, 22, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: #fff;
        padding: 10px 38px 10px 38px;
        font-size: 0.88rem;
        font-weight: 400;
        transition: all 0.25s ease;
    }

    .lux-input::placeholder {
        color: rgba(148, 163, 184, 0.4);
        font-size: 0.82rem;
    }

    .lux-input:focus {
        outline: none;
        border-color: var(--primary);
        background: rgba(14, 19, 31, 0.95);
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.16);
    }

    .lux-field:focus-within .lux-input-icon {
        color: var(--primary);
    }

    .btn-pass-toggle {
        position: absolute;
        right: 12px;
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 3px;
        font-size: 0.85rem;
        transition: color 0.2s ease;
    }

    .btn-pass-toggle:hover {
        color: var(--primary);
    }

    /* Options Row */
    .lux-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        font-size: 0.78rem;
    }

    .lux-checkbox {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        color: var(--text-muted);
        user-select: none;
    }

    .lux-checkbox input {
        display: none;
    }

    .check-box-square {
        width: 15px;
        height: 15px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(0, 0, 0, 0.25);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .check-box-square i {
        display: none;
        font-size: 0.58rem;
        color: #0b0f19;
    }

    .lux-checkbox input:checked + .check-box-square {
        background: var(--primary);
        border-color: var(--primary);
    }

    .lux-checkbox input:checked + .check-box-square i {
        display: block;
    }

    .demo-hint-link {
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.2s;
        font-size: 0.75rem;
    }

    .demo-hint-link:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    /* Main Submit Button */
    .btn-enter-console {
        width: 100%;
        padding: 12px 16px;
        background: linear-gradient(135deg, #D4AF37 0%, #B89228 50%, #997519 100%);
        border: 1px solid rgba(255, 240, 160, 0.3);
        border-radius: 10px;
        color: #0d111b;
        font-size: 0.88rem;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-enter-console::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
        transition: left 0.6s ease;
    }

    .btn-enter-console:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(212, 175, 55, 0.38);
        color: #000;
    }

    .btn-enter-console:hover::before {
        left: 100%;
    }

    .btn-enter-console:active {
        transform: translateY(0);
    }

    .btn-enter-console:disabled {
        opacity: 0.75;
        cursor: not-allowed;
    }

    /* Error Alert */
    .lux-alert-box {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 10px;
        padding: 9px 12px;
        color: #fca5a5;
        font-size: 0.78rem;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Card Footer */
    .card-footer-box {
        text-align: center;
        margin-top: 18px;
    }

    .btn-return-web {
        color: var(--text-muted);
        font-size: 0.78rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-return-web:hover {
        color: var(--primary);
        transform: translateX(-3px);
    }

    .security-note {
        margin-top: 10px;
        font-size: 0.66rem;
        color: var(--text-subtle);
        letter-spacing: 0.4px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    /* Toast Notification */
    .vip-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #0f1422;
        border: 1px solid var(--primary);
        color: #fff;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
        z-index: 9999;
        opacity: 0;
        transform: translateY(15px);
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .vip-toast.show {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endsection

@section('content')
<div class="auth-wrapper">
    <div class="auth-card-frame">
        <!-- Architectural Crosshair Accents -->
        <span class="corner-crosshair corner-tl"></span>
        <span class="corner-crosshair corner-tr"></span>
        <span class="corner-crosshair corner-bl"></span>
        <span class="corner-crosshair corner-br"></span>

        <div class="auth-card">
            
            <!-- Brand Crest & Header -->
            <div class="brand-section">
                @if(setting('site_logo'))
                    <a href="{{ url('/') }}" class="d-inline-block mb-2">
                        <img src="{{ asset_url(setting('site_logo')) }}" alt="{{ setting('site_title', 'Aurelia Estates') }}" style="max-height: 42px; object-fit: contain;">
                    </a>
                @else
                    <div class="luxury-crest">
                        <i class="fa-solid fa-city"></i>
                    </div>
                @endif

                <h1 class="brand-title-gold">{{ strtoupper(setting('site_title', 'AURELIA ESTATES')) }}</h1>
                <div class="brand-badge">
                    <i class="fa-solid fa-shield-halved text-warning"></i>
                    <span>Executive Access Enclave</span>
                </div>
            </div>

            <!-- VIP Demo Keycard Widget -->
            <div class="vip-keycard">
                <div class="vip-header">
                    <div class="vip-badge">
                        <i class="fa-solid fa-microchip vip-chip-icon"></i>
                        <span>VIP Master Key Pass</span>
                    </div>
                    <span class="vip-pill-status">Demo Ready</span>
                </div>

                <div class="vip-grid">
                    <div class="vip-box">
                        <div class="overflow-hidden me-1">
                            <span class="vip-label">Admin ID</span>
                            <span class="vip-text" id="demoEmail">admin@example.com</span>
                        </div>
                        <button type="button" class="vip-copy-btn" onclick="copyDemoVal('admin@example.com', 'Admin email copied!')" title="Copy Email">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>

                    <div class="vip-box">
                        <div class="overflow-hidden me-1">
                            <span class="vip-label">Passcode</span>
                            <span class="vip-text" id="demoPass">password</span>
                        </div>
                        <button type="button" class="vip-copy-btn" onclick="copyDemoVal('password', 'Passcode copied!')" title="Copy Password">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="vip-actions">
                    <button type="button" class="btn-vip-fill" onclick="autoFillDemoCredentials()">
                        <i class="fa-solid fa-key"></i> Auto-Fill Pass
                    </button>
                    <button type="button" class="btn-vip-instant" onclick="quickDemoLogin()">
                        <i class="fa-solid fa-bolt"></i> 1-Click Entry
                    </button>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('error'))
                <div class="lux-alert-box">
                    <i class="fa-solid fa-triangle-exclamation text-danger fs-6"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="lux-alert-box">
                    <i class="fa-solid fa-circle-exclamation text-danger fs-6"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Auth Form -->
            <form action="{{ route('admin.login.submit') }}" method="POST" id="loginForm">
                @csrf
                
                <!-- Email -->
                <div class="lux-field">
                    <label class="lux-field-label" for="loginEmail">
                        <span><i class="fa-regular fa-envelope text-warning me-1"></i> Admin Identity</span>
                    </label>
                    <div class="lux-input-box">
                        <span class="lux-input-icon"><i class="fa-solid fa-user-shield"></i></span>
                        <input 
                            type="email" 
                            name="email" 
                            id="loginEmail" 
                            class="lux-input" 
                            placeholder="admin@example.com" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="email"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="lux-field">
                    <label class="lux-field-label" for="loginPassword">
                        <span><i class="fa-solid fa-lock text-warning me-1"></i> Passcode</span>
                    </label>
                    <div class="lux-input-box">
                        <span class="lux-input-icon"><i class="fa-solid fa-shield-keyhole"></i></span>
                        <input 
                            type="password" 
                            name="password" 
                            id="loginPassword" 
                            class="lux-input" 
                            placeholder="••••••••" 
                            required 
                            autocomplete="current-password"
                        >
                        <button type="button" class="btn-pass-toggle" onclick="togglePasswordVisibility()" title="Toggle View">
                            <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Options -->
                <div class="lux-options">
                    <label class="lux-checkbox">
                        <input type="checkbox" name="remember" id="remember" value="1">
                        <span class="check-box-square">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <span>Keep Session</span>
                    </label>
                    <a href="javascript:void(0)" class="demo-hint-link" onclick="autoFillDemoCredentials()">
                        Use Demo Pass
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-enter-console" id="btnSubmit">
                    <i class="fa-solid fa-lock" id="btnSubmitIcon"></i>
                    <span id="btnSubmitText">Authenticate & Enter</span>
                </button>
            </form>

            <!-- Footer Return -->
            <div class="card-footer-box">
                <a href="{{ url('/') }}" class="btn-return-web">
                    <i class="fa-solid fa-arrow-left"></i> Return to Main Website
                </a>
                <div class="security-note">
                    <i class="fa-solid fa-fingerprint text-warning"></i>
                    <span>Hardware Encrypted &bull; ISO/IEC 27001</span>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Floating Toast -->
<div class="vip-toast" id="luxToast">
    <i class="fa-solid fa-circle-check text-warning"></i>
    <span id="toastMessage">Demo pass autofilled!</span>
</div>
@endsection

@push('scripts')
<script>
    function togglePasswordVisibility() {
        const passInput = document.getElementById('loginPassword');
        const passIcon = document.getElementById('togglePasswordIcon');
        
        if (passInput.type === 'password') {
            passInput.type = 'text';
            passIcon.classList.remove('fa-eye');
            passIcon.classList.add('fa-eye-slash');
        } else {
            passInput.type = 'password';
            passIcon.classList.remove('fa-eye-slash');
            passIcon.classList.add('fa-eye');
        }
    }

    let toastTimer;
    function showToast(message) {
        const toast = document.getElementById('luxToast');
        const toastMsg = document.getElementById('toastMessage');
        toastMsg.textContent = message;
        
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.remove('show');
        }, 2400);
    }

    function copyDemoVal(val, successMsg) {
        navigator.clipboard.writeText(val).then(() => {
            showToast(successMsg);
        }).catch(() => {
            const temp = document.createElement('input');
            temp.value = val;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            showToast(successMsg);
        });
    }

    function autoFillDemoCredentials() {
        const emailInput = document.getElementById('loginEmail');
        const passInput = document.getElementById('loginPassword');
        const lockIcon = document.getElementById('btnSubmitIcon');
        
        emailInput.value = 'admin@example.com';
        passInput.value = 'password';
        
        // Dynamically unlock the button icon
        if (lockIcon) {
            lockIcon.className = 'fa-solid fa-lock-open text-warning';
        }
        
        emailInput.style.borderColor = 'var(--primary)';
        passInput.style.borderColor = 'var(--primary)';
        setTimeout(() => {
            emailInput.style.borderColor = '';
            passInput.style.borderColor = '';
        }, 700);

        passInput.focus();
        showToast('VIP Master Key Autofilled!');
    }

    function quickDemoLogin() {
        autoFillDemoCredentials();
        
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('btnSubmit');
        const btnText = document.getElementById('btnSubmitText');
        const btnIcon = document.getElementById('btnSubmitIcon');
        
        btn.disabled = true;
        btnText.textContent = 'Unlocking Enclave...';
        btnIcon.className = 'fa-solid fa-unlock-keyhole fa-fade text-warning';
        
        setTimeout(() => {
            form.submit();
        }, 280);
    }

    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        const btnText = document.getElementById('btnSubmitText');
        const btnIcon = document.getElementById('btnSubmitIcon');
        
        btn.disabled = true;
        btnText.textContent = 'Verifying Credentials...';
        btnIcon.className = 'fa-solid fa-circle-notch fa-spin text-dark';
    });
</script>
@endpush

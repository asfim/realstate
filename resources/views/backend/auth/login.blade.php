@extends('backend.layouts.auth')

@section('styles')
<style>
    .login-container {
        max-width: 440px;
        margin: 0 auto;
        padding: 20px;
    }

    .glass-card {
        background: var(--surface);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--surface-border);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        transform: translateY(0);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.6), 0 0 40px rgba(212, 175, 55, 0.1);
    }

    .brand-logo {
        text-align: center;
        margin-bottom: 32px;
        font-family: 'Outfit', sans-serif;
    }

    .brand-logo h1 {
        font-size: 1.8rem;
        font-weight: 700;
        letter-spacing: 2px;
        margin: 0;
        color: #fff;
    }

    .brand-logo h1 span {
        color: var(--primary);
    }

    .brand-logo p {
        color: var(--text-muted);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-top: 5px;
    }

    .form-group {
        margin-bottom: 24px;
        position: relative;
    }

    .form-label {
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 8px;
        display: block;
        transition: color 0.3s;
    }

    .form-control-glass {
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
        padding: 14px 16px;
        border-radius: 12px;
        width: 100%;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control-glass:focus {
        outline: none;
        background: rgba(0, 0, 0, 0.3);
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
    }

    .form-control-glass::placeholder {
        color: rgba(255, 255, 255, 0.3);
    }

    .form-group:focus-within .form-label {
        color: var(--primary);
    }

    .btn-auth {
        background: linear-gradient(135deg, #D4AF37 0%, #AA8529 100%);
        color: #fff;
        border: none;
        padding: 16px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        width: 100%;
        letter-spacing: 1px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .btn-auth::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-auth:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
        color: #fff;
    }

    .btn-auth:hover::before {
        left: 100%;
    }

    .alert-glass {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: #ff8791;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.9rem;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

</style>
@endsection

@section('content')
<div class="container">
    <div class="login-container">
        <div class="glass-card">
            
            <div class="brand-logo">
                <h1>AURELIA<span>ESTATES</span></h1>
                <p>Admin Portal</p>
            </div>

            @if(session('error'))
                <div class="alert-glass">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control-glass" placeholder="admin@aureliaestates.com" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control-glass" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-auth mt-2">
                    Sign In <i class="fa-solid fa-arrow-right ms-2"></i>
                </button>
            </form>
            
        </div>
    </div>
</div>
@endsection

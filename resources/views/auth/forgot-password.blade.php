@extends('base.base')

@section('content')
<style>
    html, body { overflow: hidden; height: 100vh; width: 100vw; margin: 0; padding: 0; }
    body { padding-top: 0 !important; }
    nav, footer { display: none !important; }
    .app-scroll-container { padding-top: 0 !important; overflow: hidden !important; }
    .custom-scroll-track { display: none !important; }

    .login-wrapper {
        display: flex;
        height: 100vh;
        width: 100vw;
    }

    .editorial-panel {
        flex: 0 0 50%;
        background-color: #f8f9fa;
        background-image: url('https://images.unsplash.com/photo-1445205170230-053b83016050?w=1200&q=80');
        background-size: cover;
        background-position: center;
        position: relative;
        display: flex;
        align-items: flex-end;
        padding: 3rem;
    }

    .editorial-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0.2));
        z-index: 1;
    }

    .editorial-panel > div {
        position: relative;
        z-index: 2;
        color: white;
    }

    .editorial-panel h1 {
        font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
        font-size: 5rem;
        font-weight: 300;
        letter-spacing: 0.1em;
        margin-bottom: 1rem;
    }

    .editorial-panel p {
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.25em;
        color: #e0e0e0;
    }

    .form-panel {
        flex: 0 0 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
    }

    .form-minimal input[type="text"],
    .form-minimal input[type="email"] {
        border: none;
        border-bottom: 1px solid #dee2e6;
        border-radius: 0;
        padding: 0.5rem 0;
        font-size: 1rem;
        font-weight: 500;
        color: #333;
        background-color: transparent;
        box-shadow: none !important;
        width: 100%;
        display: block;
    }

    .form-minimal input[type="text"]:focus,
    .form-minimal input[type="email"]:focus {
        border-bottom: 2px solid #000;
        outline: none;
    }

    .label-caps {
        font-size: 14px;
        font-weight: 400;
        color: #333;
    }

    .btn-vesta {
        background: #000;
        color: #fff;
        border-radius: 0;
        padding: 1rem;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.3em;
        text-transform: uppercase;
        border: none;
        width: 100%;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-vesta:hover {
        background: #333;
        color: #fff;
    }

    .form-minimal input::placeholder {
        color: #adb5bd;
        font-size: 0.9rem;
        font-weight: 400;
        letter-spacing: 0.02em;
        text-transform: none;
    }

    .back-btn {
        position: absolute;
        top: 2rem;
        left: 2rem;
        z-index: 100;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #fff;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        transition: opacity 0.3s ease;
        text-shadow: 0 1px 3px rgba(0,0,0,0.5);
    }
    .back-btn:hover { opacity: 0.8; }

    .back-link {
        font-size: 11px;
        color: #868e96;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        position: relative;
        transition: color 0.3s ease;
        padding-bottom: 2px;
    }
    .back-link:hover { color: #000; }
    .back-link::after {
        content: '';
        position: absolute;
        width: 100%;
        transform: scaleX(0);
        height: 1px;
        bottom: 0;
        left: 0;
        background-color: #000;
        transform-origin: bottom right;
        transition: transform 0.25s ease-out;
    }
    .back-link:hover::after {
        transform: scaleX(1);
        transform-origin: bottom left;
    }

    .lock-icon {
        width: 48px;
        height: 48px;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 991px) {
        .editorial-panel { display: none; }
        .form-panel { flex: 0 0 100%; }
        html, body { overflow: auto; }
        .back-btn { color: #333; text-shadow: none; }
    }
</style>

<div class="login-wrapper">
    <a href="{{ route('login') }}" class="back-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Back to Login
    </a>
    <div class="editorial-panel">
        <div>
            <h1>VESTA</h1>
            <p>Recover your access. We'll help you get back in.</p>
        </div>
    </div>

    <div class="form-panel">
        <div style="width: 100%; max-width: 420px;">
            <div class="lock-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>

            <div style="margin-bottom: 2rem;">
                <h2 style="font-weight: 600; margin-bottom: 0.5rem; font-size: 1.75rem;">Forgot Password?</h2>
                <p class="label-caps">Enter the email address associated with your account. We'll send you a 6-digit OTP to reset your password.</p>
            </div>

            <form action="{{ route('password.email') }}" method="POST" class="form-minimal">
                @csrf

                @if (session('success'))
                    <div style="background-color: #e3faf2; border: 1px solid #a9e34b; color: #0ca678; padding: 12px; margin-bottom: 1.5rem; font-size: 13px; text-align: center; font-weight: 500;">
                        {{ session('success') }}
                    </div>
                @endif

                <div style="margin-bottom: 2rem;">
                    <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com" required autofocus>
                    @error('email')
                        <div style="color: #dc3545; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-vesta">
                    Send OTP Code &rarr;
                </button>
            </form>

            <p style="margin-top: 2rem; text-align: center;">
                <a href="{{ route('login') }}" class="back-link">&larr; Back to Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection

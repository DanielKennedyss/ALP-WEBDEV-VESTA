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
        overflow-y: auto;
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

    .btn-vesta:disabled {
        background: #94a3b8;
        cursor: not-allowed;
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

    /* OTP Input Styling */
    .otp-inputs {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 1.5rem 0;
    }
    .otp-inputs input {
        width: 50px;
        height: 60px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 700;
        font-family: 'Courier New', Courier, monospace;
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        background: #f8f9fa;
        transition: all 0.2s ease;
        padding: 0;
        color: #0f172a;
    }
    .otp-inputs input:focus {
        border-color: #000 !important;
        border-width: 2px !important;
        background: #fff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05) !important;
    }
    .otp-inputs input.otp-filled {
        border-color: #000 !important;
        background: #fff;
    }

    .shield-icon {
        width: 48px;
        height: 48px;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .resend-link {
        font-size: 12px;
        color: #868e96;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: color 0.3s ease;
        font-weight: 500;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
    }
    .resend-link:hover { color: #000; }

    .email-badge {
        display: inline-block;
        background: #f1f5f9;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 991px) {
        .editorial-panel { display: none; }
        .form-panel { flex: 0 0 100%; }
        html, body { overflow: auto; }
        .back-btn { color: #333; text-shadow: none; }
    }
</style>

<div class="login-wrapper">
    <a href="{{ route('profile') }}" class="back-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Cancel
    </a>
    <div class="editorial-panel">
        <div>
            <h1>VESTA</h1>
            <p>Confirm your identity to update your email.</p>
        </div>
    </div>

    <div class="form-panel">
        <div style="width: 100%; max-width: 420px;">
            <div class="shield-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <h2 style="font-weight: 600; margin-bottom: 0.5rem; font-size: 1.75rem;">Verify Email Change</h2>
                <p class="label-caps">To change your email, enter the 6-digit OTP code sent to your current email address.</p>
                
                @php
                    $oldEmail = Auth::user()->email;
                    $parts = explode('@', $oldEmail);
                    if(count($parts) === 2) {
                        $name = $parts[0];
                        $domain = $parts[1];
                        $len = strlen($name);
                        if ($len > 2) {
                            $obfuscatedName = substr($name, 0, 1) . str_repeat('*', $len - 2) . substr($name, -1);
                        } else {
                            $obfuscatedName = str_repeat('*', $len);
                        }
                        $obfuscatedEmail = $obfuscatedName . '@' . $domain;
                    } else {
                        $obfuscatedEmail = $oldEmail;
                    }
                @endphp
                
                <div class="email-badge" style="margin-top: 10px;">📧 Current: {{ $obfuscatedEmail }}</div>
                <div class="email-badge" style="margin-top: 5px; background: #e0f2fe; color: #0369a1;">➡️ Pending: {{ session('change_email_pending') }}</div>
            </div>

            @if (session('success'))
                <div style="background-color: #e3faf2; border: 1px solid #a9e34b; color: #0ca678; padding: 12px; margin-bottom: 1.5rem; font-size: 13px; text-align: center; font-weight: 500;">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div style="background-color: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px; margin-bottom: 1.5rem; font-size: 13px; text-align: center; font-weight: 500;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('profile.change-email.verify') }}" method="POST" id="verifyForm" class="form-minimal">
                @csrf
                <input type="hidden" name="otp" id="otpValueHidden" value="">

                <div>
                    <label class="label-caps" style="display: block; margin-bottom: 0.75rem;">OTP Code</label>
                    <div class="otp-inputs">
                        <input type="text" maxlength="1" class="otp-digit" data-index="0" inputmode="numeric" pattern="[0-9]" autocomplete="off" autofocus>
                        <input type="text" maxlength="1" class="otp-digit" data-index="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-digit" data-index="2" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-digit" data-index="3" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-digit" data-index="4" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-digit" data-index="5" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                    </div>
                    @error('otp')
                        <div style="color: #dc3545; font-size: 0.8rem; margin-top: 0.5rem; text-align: center;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" id="verifyBtn" class="btn btn-vesta" disabled style="margin-top: 1rem;">
                    Confirm & Update Email &rarr;
                </button>
            </form>

            <div style="display: flex; align-items: center; margin: 1.5rem 0;">
                <div style="flex: 1; height: 1px; background: #dee2e6;"></div>
                <span style="padding: 0 1rem; color: #adb5bd; font-size: 12px; text-transform: uppercase;">Or</span>
                <div style="flex: 1; height: 1px; background: #dee2e6;"></div>
            </div>

            <form action="{{ route('profile.change-email.resend') }}" method="POST" style="text-align: center;">
                @csrf
                <button type="submit" class="resend-link">Resend OTP Code</button>
            </form>

            <p style="margin-top: 1.5rem; text-align: center;">
                <a href="{{ route('profile') }}" class="back-link">&larr; Back to Profile</a>
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const digits = document.querySelectorAll('.otp-digit');
        const verifyBtn = document.getElementById('verifyBtn');
        const hiddenInput = document.getElementById('otpValueHidden');
        const verifyForm = document.getElementById('verifyForm');

        function getOtpValue() {
            let otp = '';
            digits.forEach(d => otp += d.value);
            return otp;
        }

        function updateVerifyState() {
            const otp = getOtpValue();
            verifyBtn.disabled = otp.length !== 6;
            hiddenInput.value = otp;
        }

        digits.forEach((input, index) => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value) {
                    this.classList.add('otp-filled');
                } else {
                    this.classList.remove('otp-filled');
                }
                if (this.value.length === 1 && index < digits.length - 1) {
                    digits[index + 1].focus();
                }
                updateVerifyState();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    digits[index - 1].focus();
                    digits[index - 1].value = '';
                    digits[index - 1].classList.remove('otp-filled');
                    updateVerifyState();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                if (pasteData.length >= 6) {
                    for (let i = 0; i < 6; i++) {
                        digits[i].value = pasteData[i] || '';
                        if (digits[i].value) digits[i].classList.add('otp-filled');
                    }
                    digits[5].focus();
                    updateVerifyState();
                }
            });
        });

        verifyForm.addEventListener('submit', function() {
            verifyBtn.disabled = true;
            verifyBtn.textContent = 'UPDATING EMAIL...';
        });
    });
</script>
@endsection

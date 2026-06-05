@extends('base.base')

@section('content')
<style>
    html, body { overflow: hidden; height: 100vh; width: 100vw; margin: 0; padding: 0; }
    body { padding-top: 0 !important; }
    nav, footer { display: none !important; }

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
    .form-minimal input[type="email"],
    .form-minimal input[type="password"] {
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
    .form-minimal input[type="email"]:focus,
    .form-minimal input[type="password"]:focus {
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

    /* Step transitions */
    .step-container {
        transition: opacity 0.4s ease, transform 0.4s ease;
    }
    .step-container.hidden {
        display: none;
    }
    .step-container.fade-out {
        opacity: 0;
        transform: translateY(-10px);
    }
    .step-container.fade-in {
        opacity: 0;
        transform: translateY(10px);
        animation: stepFadeIn 0.5s ease forwards;
    }
    @keyframes stepFadeIn {
        to { opacity: 1; transform: translateY(0); }
    }

    /* Error message */
    .otp-error {
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        text-align: center;
        min-height: 1.2em;
    }

    /* Success checkmark */
    .success-check {
        width: 48px;
        height: 48px;
        background: #e3faf2;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    /* Hide default browser password reveal icon */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }

    @media (max-width: 991px) {
        .editorial-panel { display: none; }
        .form-panel { flex: 0 0 100%; }
        html, body { overflow: auto; }
        .back-btn { color: #333; text-shadow: none; }
    }
</style>

<div class="login-wrapper">
    <a href="{{ route('password.request') }}" class="back-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Back
    </a>
    <div class="editorial-panel">
        <div>
            <h1>VESTA</h1>
            <p id="editorialText">Enter your verification code.</p>
        </div>
    </div>

    <div class="form-panel">
        <div style="width: 100%; max-width: 420px;">

            {{-- ========================================== --}}
            {{-- STEP 1: OTP VERIFICATION --}}
            {{-- ========================================== --}}
            <div id="stepOtp" class="step-container">
                <div class="shield-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <h2 style="font-weight: 600; margin-bottom: 0.5rem; font-size: 1.75rem;">Verify OTP</h2>
                    <p class="label-caps">Enter the 6-digit code sent to your email.</p>
                    @if(session('reset_email'))
                        <div class="email-badge">📧 {{ session('reset_email') }}</div>
                    @endif
                </div>

                @if (session('success'))
                    <div style="background-color: #e3faf2; border: 1px solid #a9e34b; color: #0ca678; padding: 12px; margin-bottom: 1.5rem; font-size: 13px; text-align: center; font-weight: 500;">
                        {{ session('success') }}
                    </div>
                @endif

                <div>
                    <label class="label-caps" style="display: block; margin-bottom: 0.75rem;">OTP Code</label>
                    <div class="otp-inputs">
                        <input type="text" maxlength="1" class="otp-digit" data-index="0" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" autofocus>
                        <input type="text" maxlength="1" class="otp-digit" data-index="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" class="otp-digit" data-index="2" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" class="otp-digit" data-index="3" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" class="otp-digit" data-index="4" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" class="otp-digit" data-index="5" inputmode="numeric" pattern="[0-9]">
                    </div>
                    <div class="otp-error" id="otpError"></div>
                </div>

                <button type="button" id="verifyOtpBtn" class="btn btn-vesta" disabled>
                    Verify Code &rarr;
                </button>

                <div style="display: flex; align-items: center; margin: 1.5rem 0;">
                    <div style="flex: 1; height: 1px; background: #dee2e6;"></div>
                    <span style="padding: 0 1rem; color: #adb5bd; font-size: 12px; text-transform: uppercase;">Or</span>
                    <div style="flex: 1; height: 1px; background: #dee2e6;"></div>
                </div>

                <form action="{{ route('password.resend.otp') }}" method="POST" style="text-align: center;">
                    @csrf
                    <button type="submit" class="resend-link">Resend OTP Code</button>
                </form>

                <p style="margin-top: 1.5rem; text-align: center;">
                    <a href="{{ route('login') }}" class="back-link">&larr; Back to Sign In</a>
                </p>
            </div>

            {{-- ========================================== --}}
            {{-- STEP 2: NEW PASSWORD (hidden initially) --}}
            {{-- ========================================== --}}
            <div id="stepPassword" class="step-container hidden">
                <div class="success-check">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0ca678" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="font-weight: 600; margin-bottom: 0.5rem; font-size: 1.75rem;">Set New Password</h2>
                    <p class="label-caps">OTP verified! Now create your new password.</p>
                </div>

                <form action="{{ route('password.reset.update') }}" method="POST" class="form-minimal">
                    @csrf

                    {{-- New Password --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">New Password</label>
                        <div style="position: relative;">
                            <input type="password" name="password" id="passwordInput" placeholder="Min. 8 characters" required style="padding-right: 2.5rem;">
                            <button type="button" class="toggle-pw-btn" data-target="passwordInput" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #adb5bd; padding: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-off-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div style="color: #dc3545; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div style="margin-bottom: 2rem;">
                        <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Confirm New Password</label>
                        <div style="position: relative;">
                            <input type="password" name="password_confirmation" id="passwordConfirm" placeholder="Re-enter your new password" required style="padding-right: 2.5rem;">
                            <button type="button" class="toggle-pw-btn" data-target="passwordConfirm" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #adb5bd; padding: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-off-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-vesta">
                        Reset Password &rarr;
                    </button>
                </form>

                <p style="margin-top: 2rem; text-align: center;">
                    <a href="{{ route('login') }}" class="back-link">&larr; Back to Sign In</a>
                </p>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const digits = document.querySelectorAll('.otp-digit');
        const verifyBtn = document.getElementById('verifyOtpBtn');
        const otpError = document.getElementById('otpError');
        const stepOtp = document.getElementById('stepOtp');
        const stepPassword = document.getElementById('stepPassword');
        const editorialText = document.getElementById('editorialText');

        // ─── OTP digit input logic ───
        function getOtpValue() {
            let otp = '';
            digits.forEach(d => otp += d.value);
            return otp;
        }

        function updateVerifyBtn() {
            const otp = getOtpValue();
            verifyBtn.disabled = otp.length !== 6;
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
                updateVerifyBtn();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    digits[index - 1].focus();
                    digits[index - 1].value = '';
                    digits[index - 1].classList.remove('otp-filled');
                    updateVerifyBtn();
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
                    updateVerifyBtn();
                }
            });
        });

        // ─── Verify OTP via AJAX ───
        verifyBtn.addEventListener('click', function() {
            const otp = getOtpValue();
            if (otp.length !== 6) return;

            verifyBtn.disabled = true;
            verifyBtn.textContent = 'VERIFYING...';
            otpError.textContent = '';

            fetch('{{ route("password.verify.otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ otp: otp }),
            })
            .then(response => response.json().then(data => ({ status: response.status, data })))
            .then(({ status, data }) => {
                if (data.success) {
                    // Transition to Step 2
                    stepOtp.classList.add('fade-out');
                    editorialText.textContent = 'Almost there. Set your new password now.';

                    setTimeout(() => {
                        stepOtp.classList.add('hidden');
                        stepPassword.classList.remove('hidden');
                        stepPassword.classList.add('fade-in');

                        // Focus the password input
                        setTimeout(() => {
                            document.getElementById('passwordInput').focus();
                        }, 300);
                    }, 400);
                } else {
                    otpError.textContent = data.message || 'Verification failed.';
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = 'VERIFY CODE &rarr;';

                    // Shake the OTP inputs
                    document.querySelector('.otp-inputs').style.animation = 'shake 0.4s ease';
                    setTimeout(() => {
                        document.querySelector('.otp-inputs').style.animation = '';
                    }, 400);
                }
            })
            .catch(() => {
                otpError.textContent = 'Something went wrong. Please try again.';
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = 'VERIFY CODE &rarr;';
            });
        });

        // ─── Password visibility toggle ───
        document.querySelectorAll('.toggle-pw-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const eyeIcon = this.querySelector('.eye-icon');
                const eyeOffIcon = this.querySelector('.eye-off-icon');

                if (input.getAttribute('type') === 'password') {
                    input.setAttribute('type', 'text');
                    eyeIcon.style.display = 'none';
                    eyeOffIcon.style.display = 'block';
                    this.style.color = '#333';
                } else {
                    input.setAttribute('type', 'password');
                    eyeIcon.style.display = 'block';
                    eyeOffIcon.style.display = 'none';
                    this.style.color = '#adb5bd';
                }
            });
        });
    });
</script>

<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20% { transform: translateX(-8px); }
        40% { transform: translateX(8px); }
        60% { transform: translateX(-6px); }
        80% { transform: translateX(6px); }
    }
</style>
@endsection

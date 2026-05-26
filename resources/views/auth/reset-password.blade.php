@extends('base.base')

@section('content')
<div style="max-width: 450px; margin: 80px auto; padding: 40px; background: #fff; border: 1px solid #eee;">
    <h2 style="font-family: ui-serif, serif; font-weight: 300; text-align: center; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 10px;">Reset Password</h2>
    <p style="color: #666; font-size: 13px; text-align: center; margin-bottom: 30px;">Silakan periksa kotak masuk email Anda, lalu masukkan kode OTP dan password baru di bawah ini.</p>

    @if (session('status'))
        <div style="background: #f1f3f5; color: #000; padding: 12px; font-size: 13px; margin-bottom: 20px; border: 1px solid #dee2e6; text-align: center;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background: #fff5f5; color: #c92a2a; padding: 12px; font-size: 13px; margin-bottom: 20px; border: 1px solid #ffc9c9; text-align: center;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- REVISI: Mengubah action ke route password.reset.update agar tidak bentrok --}}
    <form action="{{ route('password.reset.update') }}" method="POST">
        @csrf
        
        {{-- Input OTP (REVISI: Mengubah name menjadi otp_code agar sinkron dengan Controller) --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; color: #333;">Kode OTP (6 Digit)</label>
            <input type="text" name="otp_code" required maxlength="6" style="width: 100%; padding: 12px; border: 1px solid #ccc; box-sizing: border-box; font-size: 16px; font-weight: bold; text-align: center; letter-spacing: 4px;" placeholder="000000" value="{{ old('otp_code') }}">
        </div>

        {{-- Input Password Baru --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; color: #333;">Password Baru (Minimal 8 Karakter)</label>
            <div style="position: relative;">
                <input type="password" id="password" name="password" required style="width: 100%; padding: 12px; padding-right: 60px; border: 1px solid #ccc; box-sizing: border-box; font-size: 14px;">
                <button type="button" onclick="togglePasswordVisibility('password', 'toggleText1')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 11px; font-weight: bold; color: #666; cursor: pointer; letter-spacing: 0.05em; text-transform: uppercase; user-select: none;">
                    <span id="toggleText1">Show</span>
                </button>
            </div>
        </div>

        {{-- Konfirmasi Password --}}
        <div style="margin-bottom: 25px;">
            <label style="display: block; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; color: #333;">Konfirmasi Password Baru</label>
            <div style="position: relative;">
                <input type="password" id="password_confirmation" name="password_confirmation" required style="width: 100%; padding: 12px; padding-right: 60px; border: 1px solid #ccc; box-sizing: border-box; font-size: 14px;">
                <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'toggleText2')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 11px; font-weight: bold; color: #666; cursor: pointer; letter-spacing: 0.05em; text-transform: uppercase; user-select: none;">
                    <span id="toggleText2">Show</span>
                </button>
            </div>
        </div>

        <button type="submit" style="width: 100%; padding: 14px; background: #000; color: #fff; border: none; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; cursor: pointer; font-weight: bold;">
            Perbarui Password
        </button>
    </form>
</div>

{{-- JavaScript Ringan untuk Manipulasi Tipe Input Password --}}
<script>
    function togglePasswordVisibility(inputId, textId) {
        const passwordInput = document.getElementById(inputId);
        const toggleText = document.getElementById(textId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleText.textContent = 'Hide';
        } else {
            passwordInput.type = 'password';
            toggleText.textContent = 'Show';
        }
    }
</script>
@endsection
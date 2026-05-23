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

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        
        {{-- Input OTP --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; color: #333;">Kode OTP (6 Digit)</label>
            <input type="text" name="otp" required maxlength="6" style="width: 100%; padding: 12px; border: 1px solid #ccc; box-sizing: border-box; font-size: 16px; font-weight: bold; text-align: center; letter-spacing: 4px;" placeholder="000000">
        </div>

        {{-- Input Password Baru --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; color: #333;">Password Baru (Minimal 8 Karakter)</label>
            <input type="password" name="password" required style="width: 100%; padding: 12px; border: 1px solid #ccc; box-sizing: border-box; font-size: 14px;">
        </div>

        {{-- Konfirmasi Password --}}
        <div style="margin-bottom: 25px;">
            <label style="display: block; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; color: #333;">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" required style="width: 100%; padding: 12px; border: 1px solid #ccc; box-sizing: border-box; font-size: 14px;">
        </div>

        <button type="submit" style="width: 100%; padding: 14px; background: #000; color: #fff; border: none; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; cursor: pointer; font-weight: bold;">
            Perbarui Password
        </button>
    </form>
</div>
@endsection
@extends('base.base')

@section('content')
<div style="max-width: 450px; margin: 80px auto; padding: 40px; background: #fff; border: 1px solid #eee;">
    <h2 style="font-family: ui-serif, serif; font-weight: 300; text-align: center; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 10px;">Verifikasi Akun</h2>
    <p style="color: #666; font-size: 13px; text-align: center; margin-bottom: 30px;">Demi keamanan akun VESTA Anda, masukkan 6 digit kode OTP verifikasi yang telah kami kirim ke email Anda.</p>

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

    <form action="{{ route('otp.verify.submit') }}" method="POST">
        @csrf
        <div style="margin-bottom: 25px;">
            <label style="display: block; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; color: #333; text-align: center;">Masukkan 6 Digit OTP</label>
            <input type="text" name="otp" required maxlength="6" style="width: 100%; padding: 14px; border: 1px solid #ccc; box-sizing: border-box; font-size: 20px; font-weight: bold; text-align: center; letter-spacing: 6px;" placeholder="000000">
        </div>

        <button type="submit" style="width: 100%; padding: 14px; background: #000; color: #fff; border: none; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; cursor: pointer; font-weight: bold;">
            Verifikasi Kode
        </button>
    </form>

    {{-- Tombol Kirim Ulang (Resend OTP) jika tidak menerima email --}}
    <div style="text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid #f1f3f5;">
        <p style="color: #888; font-size: 12px; margin-bottom: 10px;">Tidak menerima kode OTP?</p>
        <form action="{{ route('otp.resend') }}" method="POST">
            @csrf
            <button type="submit" style="background: none; border: none; color: #000; font-size: 12px; font-weight: bold; text-decoration: underline; cursor: pointer; letter-spacing: 0.05em; text-transform: uppercase;">
                Kirim Ulang Kode
            </button>
        </form>
    </div>
</div>
@endsection
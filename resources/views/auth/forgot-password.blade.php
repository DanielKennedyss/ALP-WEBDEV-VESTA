@extends('base.base')

@section('content')
<div style="max-width: 450px; margin: 80px auto; padding: 40px; background: #fff; border: 1px solid #eee;">
    <h2 style="font-family: ui-serif, serif; font-weight: 300; text-align: center; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 10px;">Lupa Password</h2>
    <p style="color: #666; font-size: 13px; text-align: center; margin-bottom: 30px;">Masukkan email terdaftar Anda. Kami akan mengirimkan kode OTP untuk mereset password.</p>

    {{-- Alert Status Sukses --}}
    @if (session('status'))
        <div style="background: #f1f3f5; color: #000; padding: 12px; font-size: 13px; margin-bottom: 20px; border: 1px solid #dee2e6; text-align: center;">
            {{ session('status') }}
        </div>
    @endif

    {{-- Alert Status Error --}}
    @if ($errors->any())
        <div style="background: #fff5f5; color: #c92a2a; padding: 12px; font-size: 13px; margin-bottom: 20px; border: 1px solid #ffc9c9; text-align: center;">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div style="margin-bottom: 25px;">
            <label style="display: block; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; color: #333;">Alamat Email</label>
            <input type="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #ccc; box-sizing: border-box; font-size: 14px;" placeholder="nama@email.com" value="{{ old('email') }}">
        </div>

        <button type="submit" style="width: 100%; padding: 14px; background: #000; color: #fff; border: none; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; cursor: pointer; font-weight: bold;">
            Kirim Kode OTP
        </button>
    </form>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('login') }}" style="color: #666; font-size: 12px; text-decoration: none; letter-spacing: 0.05em;">← Kembali ke Login</a>
    </div>
</div>
@endsection
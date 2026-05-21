@extends('layouts.admin') {{-- Sesuaikan dengan nama layout master admin kamu --}}

@section('admin_content')
<style>
    .form-luxury-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.03);
        padding: 40px;
    }
    .form-label-luxury {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #7a7a7a;
        margin-bottom: 8px;
    }
    .form-control-luxury {
        border: 1px solid #d1d5db;
        padding: 12px 16px;
        font-size: 14px;
        border-radius: 8px;
        color: #1a1a1a;
        transition: all 0.3s ease;
    }
    .form-control-luxury:focus {
        border-color: #1a1a1a;
        box-shadow: none;
        background-color: #fafafa;
    }
    .btn-luxury-black {
        background: #1a1a1a;
        color: #ffffff;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 24px;
        border-radius: 8px;
        border: 1px solid #1a1a1a;
        transition: all 0.3s ease;
    }
    .btn-luxury-black:hover {
        background: #ffffff;
        color: #1a1a1a;
    }
    .btn-luxury-secondary {
        background: #ffffff;
        color: #7a7a7a;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 24px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: all 0.3s ease;
    }
    .btn-luxury-secondary:hover {
        background: #f4f4f4;
        color: #1a1a1a;
    }
</style>

<div class="container-fluid p-0">
    {{-- Header Section --}}
    <div class="mb-5">
        <a href="{{ route('admin.vouchers.index') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-2 mb-2 hover:text-black transition-colors">
            <i class="bi bi-arrow-left"></i> Back to Promotions List
        </a>
        <span class="text-uppercase text-muted d-block mt-2" style="font-size: 10px; font-weight: 600; letter-spacing: 0.2em;">Operations</span>
        <h1 class="h2 fw-bold mt-1 mb-0" style="letter-spacing: -0.02em;">Create Luxury Voucher</h1>
    </div>

    {{-- Error Global Alert --}}
    @if(session('error'))
        <div class="alert alert-danger border-0 mb-4 p-3 rounded-3" role="alert">
            <span class="small"><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}</span>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="form-luxury-card shadow-sm">
        <form action="{{ route('admin.vouchers.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                {{-- 1. Voucher Code --}}
                <div class="col-md-6">
                    <label for="code" class="form-label-luxury">Voucher Code</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" 
                        class="form-control form-control-luxury w-full text-uppercase @error('code') is-invalid @enderror" 
                        placeholder="E.G., VESTAWINTER26" required autocomplete="off">
                    @error('code')
                        <div class="invalid-feedback small mt-1 font-semibold">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted" style="font-size: 11px;">Alphanumeric characters only, spaces will be ignored.</div>
                </div>

                {{-- 2. Voucher Type --}}
                <div class="col-md-6">
                    <label for="type" class="form-label-luxury">Discount Type</label>
                    <select name="type" id="type" class="form-select form-control-luxury @error('type') is-invalid @enderror" required>
                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (IDR Cut)</option>
                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 3. Reward Value --}}
                <div class="col-md-6">
                    <label for="reward_value" class="form-label-luxury">Reward Value</label>
                    <input type="number" name="reward_value" id="reward_value" value="{{ old('reward_value') }}" 
                        class="form-control form-control-luxury @error('reward_value') is-invalid @enderror" 
                        placeholder="E.G., 100000 or 15" required min="1">
                    @error('reward_value')
                        <div class="invalid-feedback small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 4. Total Quota --}}
                <div class="col-md-6">
                    <label for="total_quota" class="form-label-luxury">Usage Quota</label>
                    <input type="number" name="total_quota" id="total_quota" value="{{ old('total_quota') }}" 
                        class="form-control form-control-luxury @error('total_quota') is-invalid @enderror" 
                        placeholder="E.G., 50" required min="1">
                    @error('total_quota')
                        <div class="invalid-feedback small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 5. Expiry Date --}}
                <div class="col-md-6">
                    <label for="expired_at" class="form-label-luxury">Expiry Date & Time</label>
                    <input type="datetime-local" name="expired_at" id="expired_at" value="{{ old('expired_at') }}" 
                        class="form-control form-control-luxury @error('expired_at') is-invalid @enderror" required>
                    @error('expired_at')
                        <div class="invalid-feedback small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-luxury-secondary">Cancel</a>
                <button type="submit" class="btn btn-luxury-black">Publish Voucher</button>
            </div>
        </form>
    </div>
</div>
@endsection
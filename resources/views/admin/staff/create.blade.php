@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        {{-- Ukuran col-md-8 atau col-lg-7 adalah standar ideal untuk form di Desktop --}}
        <div class="col-12 col-md-8 col-lg-7">
            
            <div class="text-center mb-5">
                <h2 class="fw-bold tracking-tighter mb-1" style="font-size: 2.5rem;">RECRUIT STAFF</h2>
                <p class="text-muted small text-uppercase tracking-widest">Register new administrative access for VESTA team</p>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('admin.staff.store') }}" method="POST" id="staffForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted mb-3">Privilege Level</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="role" id="role_staff" value="staff" checked>
                                    <label class="btn btn-outline-dark w-100 py-3 rounded-3 shadow-sm d-flex flex-column align-items-center" for="role_staff">
                                        <i class="bi bi-person-badge mb-2 fs-4"></i>
                                        <span class="fw-bold small">STORE STAFF</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="role" id="role_manager" value="manager"
                                        {{ auth()->user()->role === 'manager' ? 'disabled' : '' }}>
                                    <label class="btn btn-outline-dark w-100 py-3 rounded-3 shadow-sm d-flex flex-column align-items-center {{ auth()->user()->role === 'manager' ? 'opacity-50' : '' }}" for="role_manager">
                                        <i class="bi bi-shield-lock mb-2 fs-4"></i>
                                        <span class="fw-bold small">MANAGER</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="name" class="form-label small fw-bold text-uppercase text-muted">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control form-control-lg border-0 bg-light px-4 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Daniel Roger" required style="border-radius: 10px;">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label small fw-bold text-uppercase text-muted">Corporate Email</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg border-0 bg-light px-4 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="staff@vesta.com" required style="border-radius: 10px;">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-5 opacity-25">

                        <div class="mb-4">
                            <label for="password" class="form-label small fw-bold text-uppercase text-muted">Security Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control form-control-lg border-0 bg-light px-4 @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required style="border-radius: 10px 0 0 10px;">
                                <button class="btn btn-light border-0 px-4" type="button" onclick="togglePassword('password')" style="border-radius: 0 10px 10px 0; background-color: #f1f1f1;">
                                    <i class="bi bi-eye" id="password_icon"></i>
                                </button>
                            </div>
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-5">
                            <label for="password_confirmation" class="form-label small fw-bold text-uppercase text-muted">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-lg border-0 bg-light px-4" placeholder="Repeat security key" required style="border-radius: 10px;">
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold text-uppercase shadow-lg mb-3" style="letter-spacing: 2px;">
                                CONFIRM & CREATE ACCOUNT
                            </button>
                            <div class="text-center">
                                <a href="{{ route('admin.staff.index') }}" class="text-muted text-decoration-none small fw-bold">DISCARD CHANGES</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #fcfcfc; }
    .form-control:focus { background-color: #fff !important; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #000 !important; }
    .btn-outline-dark { transition: all 0.3s ease; border: 1px solid #eee; color: #666; }
    .btn-check:checked + .btn-outline-dark { background-color: #000; color: #fff; border-color: #000; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; }
    .tracking-tighter { letter-spacing: -1.5px; }
</style>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = document.getElementById(id + '_icon');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
@endsection
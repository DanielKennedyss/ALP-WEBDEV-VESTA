@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        {{-- Lebar yang proporsional untuk Laptop/Desktop --}}
        <div class="col-12 col-md-8 col-lg-7">
            
            <div class="text-center mb-5">
                <h2 class="fw-bold tracking-tighter mb-1" style="font-size: 2.5rem;">UPDATE STAFF</h2>
                <p class="text-muted small text-uppercase tracking-widest">Modify administrative credentials for {{ $staff->name }}</p>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" id="editStaffForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted mb-3">Privilege Level</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="role" id="role_staff" value="staff" 
                                        {{ $staff->role == 'staff' ? 'checked' : '' }}
                                        {{ auth()->user()->role !== 'owner' ? 'disabled' : '' }}>
                                    <label class="btn btn-outline-dark w-100 py-3 rounded-3 shadow-sm d-flex flex-column align-items-center" for="role_staff">
                                        <i class="bi bi-person-badge mb-2 fs-4"></i>
                                        <span class="fw-bold small">STORE STAFF</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="role" id="role_manager" value="manager" 
                                        {{ $staff->role == 'manager' ? 'checked' : '' }}
                                        {{ auth()->user()->role !== 'owner' ? 'disabled' : '' }}>
                                    <label class="btn btn-outline-dark w-100 py-3 rounded-3 shadow-sm d-flex flex-column align-items-center" for="role_manager">
                                        <i class="bi bi-shield-lock mb-2 fs-4"></i>
                                        <span class="fw-bold small">MANAGER</span>
                                    </label>
                                </div>
                            </div>
                            @if(auth()->user()->role !== 'owner')
                                <small class="text-muted mt-2 d-block">* Role changes are restricted to Owner only.</small>
                                <input type="hidden" name="role" value="{{ $staff->role }}">
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="name" class="form-label small fw-bold text-uppercase text-muted">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control form-control-lg border-0 bg-light px-4 @error('name') is-invalid @enderror" 
                                value="{{ old('name', $staff->name) }}" required style="border-radius: 10px;">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label small fw-bold text-uppercase text-muted">Corporate Email</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg border-0 bg-light px-4 @error('email') is-invalid @enderror" 
                                value="{{ old('email', $staff->email) }}" required style="border-radius: 10px;">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-5 opacity-25">

                        <div class="alert alert-light border-0 small text-muted p-3 mb-4" style="border-radius: 10px; background-color: #fcfcfc;">
                            <i class="bi bi-info-circle me-2 text-primary"></i> 
                            Leave the password fields empty if you do not wish to change the existing access key.
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label small fw-bold text-uppercase text-muted">New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" 
                                    class="form-control form-control-lg border-0 bg-light px-4 @error('password') is-invalid @enderror" 
                                    placeholder="••••••••" style="border-radius: 10px 0 0 10px;">
                                <button class="btn border-0 px-4 btn-toggle-password" type="button" onclick="togglePassword('password')" style="border-radius: 0 10px 10px 0; background-color: #e9ecef;">
                                    <i class="bi bi-eye" id="password_icon"></i>
                                </button>
                            </div>
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-5">
                            <label for="password_confirmation" class="form-label small fw-bold text-uppercase text-muted">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="form-control form-control-lg border-0 bg-light px-4" 
                                    placeholder="••••••••" style="border-radius: 10px 0 0 10px;">
                                <button class="btn border-0 px-4 btn-toggle-password" type="button" onclick="togglePassword('password_confirmation')" style="border-radius: 0 10px 10px 0; background-color: #e9ecef;">
                                    <i class="bi bi-eye" id="password_confirmation_icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold text-uppercase shadow-lg mb-3" style="letter-spacing: 2px;">
                                UPDATE STAFF ACCOUNT
                            </button>
                            <div class="text-center">
                                <a href="{{ auth()->user()->role === 'staff' ? route('admin.dashboard') : route('admin.staff.index') }}" class="text-muted text-decoration-none small fw-bold">CANCEL & BACK</a>
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
    .form-control:focus { 
        background-color: #fff !important; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        border: 1px solid #000 !important; 
        outline: none;
    }
    
    /* Fokus pada Input Group (Password) */
    .input-group:focus-within .form-control {
        border-right: none !important;
    }
    .input-group:focus-within .btn-toggle-password {
        background-color: #fff !important;
        border: 1px solid #000 !important;
        border-left: none !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    
    .btn-outline-dark { transition: all 0.3s ease; border: 1px solid #eee; color: #666; }
    .btn-check:checked + .btn-outline-dark { 
        background-color: #000; 
        color: #fff; 
        border-color: #000; 
        transform: translateY(-2px); 
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; 
    }
    .tracking-tighter { letter-spacing: -1.5px; }
    .btn-toggle-password { transition: all 0.2s; color: #6c757d; }
    .btn-toggle-password:hover { color: #000; background-color: #e2e6ea; }
</style>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = document.getElementById(id + '_icon');
        
        // Safety check
        if (!input || !icon) return;

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endsection
@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-outline-dark me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold text-uppercase m-0" style="letter-spacing: 2px;">Add New Admin</h2>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.staff.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label small fw-bold text-uppercase">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter admin name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold text-uppercase">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="admin@vesta.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label small fw-bold text-uppercase">Password</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="min. 8 characters" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label small fw-bold text-uppercase">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repeat password" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark py-2 fw-bold text-uppercase" style="letter-spacing: 1px;">
                                Register Admin
                            </button>
                            <a href="{{ route('admin.staff.index') }}" class="btn btn-link text-muted text-decoration-none small">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control {
        border-radius: 0;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
    }
    .form-control:focus {
        border-color: #333;
        box-shadow: none;
    }
    .btn-dark {
        border-radius: 0;
    }
</style>
@endsection
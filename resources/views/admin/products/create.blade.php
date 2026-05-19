@extends('layouts.admin')

@section('admin_content')
<div class="mb-5">
    <h6 class="text-muted text-uppercase small tracking-widest mb-2" style="font-size: 10px; font-weight: 700;">Inventory Management</h6>
    <h1 class="fw-normal tracking-tighter" style="font-size: 2.5rem;">New Product</h1>
</div>

{{-- 1. ERROR FEEDBACK BLOCK --}}
@if ($errors->any())
    <div class="alert alert-danger border-0 rounded-0 shadow-sm mb-4" style="background-color: #fff5f5; border-left: 4px solid #ff4d4d !important;">
        <ul class="mb-0 small fw-bold text-uppercase" style="list-style: none; letter-spacing: 0.5px; color: #ff4d4d;">
            @foreach ($errors->all() as $error)
                <li><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="admin-card" style="max-width: 900px;">
    {{-- 2. ENCTYPE HARUS ADA UNTUK UPLOAD GAMBAR --}}
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-5">
            <!-- Left Side: Basic Info -->
            <div class="col-md-7">
                <div class="mb-4">
                    <label class="stat-label d-block mb-2">Product Name</label>
                    <input type="text" name="name" class="form-control border-0 border-bottom rounded-0 px-0 mb-2 shadow-none @error('name') is-invalid @enderror" 
                           placeholder="e.g. Noir Monogram Tote" value="{{ old('name') }}" required>
                </div>
                
                <div class="mb-4">
                    <label class="stat-label d-block mb-2">Description</label>
                    <textarea name="description" class="form-control border-0 border-bottom rounded-0 px-0 mb-2 shadow-none" 
                              rows="4" placeholder="Crafted from premium leather...">{{ old('description') }}</textarea>
                </div>
            </div>
            
            <!-- Right Side: Logistics -->
            <div class="col-md-5">
                <div class="mb-4">
                    <label class="stat-label d-block mb-2">SKU (Stock Keeping Unit)</label>
                    <input type="text" name="sku" class="form-control border-0 border-bottom rounded-0 px-0 mb-2 shadow-none @error('sku') is-invalid @enderror" 
                           placeholder="VS-LTH-001" value="{{ old('sku') }}" required>
                </div>

                <div class="row">
                    <div class="col-6 mb-4">
                        <label class="stat-label d-block mb-2">Price (IDR)</label>
                        <input type="number" name="price" class="form-control border-0 border-bottom rounded-0 px-0 mb-2 shadow-none" 
                               placeholder="0" value="{{ old('price') }}" required>
                    </div>
                    <div class="col-6 mb-4">
                        <label class="stat-label d-block mb-2">Stock</label>
                        <input type="number" name="stock" class="form-control border-0 border-bottom rounded-0 px-0 mb-2 shadow-none" 
                               value="{{ old('stock', 0) }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="stat-label d-block mb-2">Category</label>
                    <select name="category" class="form-select border-0 border-bottom rounded-0 px-0 mb-2 shadow-none">
                        <option value="Outerwear" {{ old('category') == 'Outerwear' ? 'selected' : '' }}>Outerwear</option>
                        <option value="Basics" {{ old('category') == 'Basics' ? 'selected' : '' }}>Basics</option>
                        <option value="Accessories" {{ old('category') == 'Accessories' ? 'selected' : '' }}>Accessories</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="stat-label d-block mb-2">Product Image</label>
                    <input type="file" name="image" class="form-control border-0 border-bottom rounded-0 px-0 mb-2 shadow-none" accept=".jpg,.jpeg,.png">
                    <p class="text-muted" style="font-size: 9px;">Format: JPG, JPEG, PNG. Max 2MB.</p>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-4 border-top">
            <button type="submit" class="btn btn-dark rounded-pill px-5 py-2 fw-bold" style="font-size: 11px; letter-spacing: 0.1em;">CONFIRM & SAVE</button>
            <a href="{{ route('admin.inventory') }}" class="btn btn-link text-dark text-decoration-none small ms-3 fw-bold" style="font-size: 11px;">CANCEL</a>
        </div>
    </form>
</div>
@endsection
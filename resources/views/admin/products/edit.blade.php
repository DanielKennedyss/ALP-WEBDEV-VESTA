@extends('layouts.admin')

@section('admin_content')
<div class="mb-5">
    <h6 class="text-muted text-uppercase small tracking-widest mb-2" style="font-size: 10px; font-weight: 700;">Inventory Management</h6>
    <h1 class="fw-normal tracking-tighter" style="font-size: 2.5rem;">Edit Product</h1>
</div>

{{-- ERROR FEEDBACK --}}
@if ($errors->any())
    <div class="alert alert-danger border-0 rounded-0 shadow-sm mb-4" style="background-color: #fff5f5; border-left: 4px solid #ff4d4d !important;">
        <ul class="mb-0 small fw-bold text-uppercase" style="list-style: none; letter-spacing: 0.5px; color: #ff4d4d;">
            @foreach ($errors->all() as $error)
                <li><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="admin-card" style="max-width: 960px;">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row g-5">
            {{-- ============ LEFT SIDE: Basic Info ============ --}}
            <div class="col-md-7">
                {{-- Product Name --}}
                <div class="mb-4">
                    <label class="stat-label d-block mb-2">Product Name</label>
                    <input type="text" name="name" class="form-control border-0 border-bottom rounded-0 px-0 mb-2 shadow-none @error('name') is-invalid @enderror" 
                           placeholder="e.g. Noir Monogram Tote" value="{{ old('name', $product->name) }}" required>
                </div>
                
                {{-- Description --}}
                <div class="mb-4">
                    <label class="stat-label d-block mb-2">Description</label>
                    <textarea name="description" class="form-control border-0 border-bottom rounded-0 px-0 mb-2 shadow-none" 
                              rows="4" placeholder="Crafted from premium leather...">{{ old('description', $product->description) }}</textarea>
                </div>

                {{-- Category Dropdown --}}
                <div class="mb-4">
                    <label class="stat-label d-block mb-2">Category</label>
                    <select name="category_id" id="categorySelect" class="form-select border-0 border-bottom rounded-0 px-0 mb-2 shadow-none" required>
                        <option value="" disabled>-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    data-name="{{ $category->name }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Gender Radio --}}
                <div class="mb-4">
                    <label class="stat-label d-block mb-3">Gender</label>
                    <div class="d-flex gap-4">
                        @foreach(['Male', 'Female', 'Unisex'] as $g)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" id="gender{{ $g }}" value="{{ $g }}" 
                                   {{ old('gender', $product->gender) == $g ? 'checked' : '' }} required>
                            <label class="form-check-label small fw-medium" for="gender{{ $g }}">{{ $g }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Size Type Radio --}}
                <div class="mb-4">
                    <label class="stat-label d-block mb-3">Size Type</label>
                    <div class="d-flex gap-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="size_type" id="sizeTypeOneSize" value="one_size" 
                                   {{ old('size_type', $product->size_type) == 'one_size' ? 'checked' : '' }} required>
                            <label class="form-check-label small fw-medium" for="sizeTypeOneSize">One Size / No Size</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="size_type" id="sizeTypeCustom" value="custom" 
                                   {{ old('size_type', $product->size_type) == 'custom' ? 'checked' : '' }}>
                            <label class="form-check-label small fw-medium" for="sizeTypeCustom">Custom</label>
                        </div>
                    </div>
                    <p class="text-muted" style="font-size: 9px;" id="sizeHelpText"></p>
                </div>

                {{-- One Size Logic --}}
                @php 
                    $oneSizeVariant = $product->variants->where('size_label', 'One Size')->first();
                @endphp
                <div id="oneSizeFields" class="mb-4 p-3 bg-light rounded" style="display: none;">
                    <h6 class="stat-label mb-3">One Size Stock</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small text-muted" style="font-size: 10px;">Stock</label>
                            <input type="number" name="stock_one_size" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 shadow-none" 
                                   value="{{ old('stock_one_size', $oneSizeVariant->stock ?? 0) }}" min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted" style="font-size: 10px;">Min. Stock</label>
                            <input type="number" name="min_stock_one_size" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 shadow-none" 
                                   value="{{ old('min_stock_one_size', $oneSizeVariant->min_stock ?? 5) }}" min="1">
                        </div>
                    </div>
                </div>

                {{-- Custom Sizes Logic --}}
                <div id="customSizeFields" class="mb-4" style="display: none;">
                    <h6 class="stat-label mb-3">Select Sizes & Set Stock</h6>

                    @foreach(['S', 'M', 'L', 'XL'] as $size)
                    @php 
                        $variant = $product->variants->where('size_label', $size)->first();
                        $isChecked = (is_array(old('sizes')) && in_array($size, old('sizes'))) || ($variant && !old('sizes'));
                    @endphp
                    <div class="d-flex align-items-start gap-3 mb-3 p-3 bg-light rounded size-row" id="sizeRow{{ $size }}">
                        <div class="form-check pt-1" style="min-width: 60px;">
                            <input class="form-check-input size-checkbox" type="checkbox" name="sizes[]" value="{{ $size }}" 
                                   id="sizeCheck{{ $size }}" {{ $isChecked ? 'checked' : '' }} disabled>
                            <label class="form-check-label small fw-bold" for="sizeCheck{{ $size }}">{{ $size }}</label>
                        </div>
                        <div class="flex-grow-1">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label text-muted" style="font-size: 9px;">Stock</label>
                                    <input type="number" name="stock_{{ $size }}" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 shadow-none size-input" 
                                           value="{{ old("stock_{$size}", $variant->stock ?? 0) }}" min="0" disabled>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted" style="font-size: 9px;">Min. Stock</label>
                                    <input type="number" name="min_stock_{{ $size }}" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 shadow-none size-input" 
                                           value="{{ old("min_stock_{$size}", $variant->min_stock ?? 5) }}" min="1" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            {{-- ============ RIGHT SIDE: Logistics ============ --}}
            <div class="col-md-5">
                {{-- Price --}}
                <div class="mb-4">
                    <label class="stat-label d-block mb-2">Price (IDR)</label>
                    <input type="number" name="price" class="form-control border-0 border-bottom rounded-0 px-0 mb-2 shadow-none" 
                           placeholder="0" value="{{ old('price', $product->price) }}" required>
                </div>

                {{-- Product Image --}}
                <div class="mb-4">
                    <label class="stat-label d-block mb-2">Product Image</label>
                    
                    {{-- Preview Current Image --}}
                    @if($product->image_path)
                    <div class="mb-3">
                        <img src="{{ asset('product_image/' . $product->image_path) }}" 
                             class="img-thumbnail rounded-0 border-0 bg-light" style="max-height: 200px;">
                        <p class="text-muted mt-2" style="font-size: 9px;">Current Image</p>
                    </div>
                    @endif

                    <input type="file" name="image" class="form-control border-0 border-bottom rounded-0 px-0 mb-2 shadow-none" accept=".jpg,.jpeg,.png">
                    <p class="text-muted" style="font-size: 9px;">Leave empty to keep current image. Format: JPG, JPEG, PNG. Max 2MB.</p>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-4 border-top">
            <button type="submit" class="btn btn-dark rounded-pill px-5 py-2 fw-bold" style="font-size: 11px; letter-spacing: 0.1em;">UPDATE PIECE</button>
            <a href="{{ route('admin.inventory') }}" class="btn btn-link text-dark text-decoration-none small ms-3 fw-bold" style="font-size: 11px;">CANCEL</a>
        </div>
    </form>
</div>

{{-- Reuse JavaScript Logic from Create --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('categorySelect');
    const sizeTypeOneSize = document.getElementById('sizeTypeOneSize');
    const sizeTypeCustom = document.getElementById('sizeTypeCustom');
    const oneSizeFields = document.getElementById('oneSizeFields');
    const customSizeFields = document.getElementById('customSizeFields');
    const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
    const sizeHelpText = document.getElementById('sizeHelpText');

    /**
     * Toggle visibility of size fields based on size_type selection
     */
    function handleSizeTypeChange() {
        const isOneSize = sizeTypeOneSize.checked;
        const isCustom = sizeTypeCustom.checked;

        // Show/hide One Size fields
        oneSizeFields.style.display = isOneSize ? 'block' : 'none';

        // Show/hide Custom Size fields
        customSizeFields.style.display = isCustom ? 'block' : 'none';

        // Enable/disable checkboxes
        sizeCheckboxes.forEach(cb => {
            cb.disabled = !isCustom;
            if (!isCustom) {
                cb.checked = false;
                toggleSizeInputs(cb);
            }
        });
    }

    /**
     * Enable/disable stock inputs when checkbox is toggled
     */
    function toggleSizeInputs(checkbox) {
        const size = checkbox.value;
        const row = document.getElementById('sizeRow' + size);
        const inputs = row.querySelectorAll('.size-input');
        
        inputs.forEach(input => {
            input.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                input.value = input.name.startsWith('min_stock') ? '5' : '0';
            }
        });

        // Visual feedback
        row.style.opacity = checkbox.checked ? '1' : '0.5';
    }

    /**
     * Handle category change - force One Size for Accessories
     */
    function handleCategoryChange() {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const categoryName = selectedOption ? selectedOption.dataset.name : '';

        if (categoryName === 'Accessories') {
            // Force One Size, disable Custom
            sizeTypeOneSize.checked = true;
            sizeTypeCustom.disabled = true;
            sizeHelpText.textContent = 'Accessories selalu menggunakan One Size / No Size.';
            sizeHelpText.style.color = '#e67e22';
        } else {
            sizeTypeCustom.disabled = false;
            sizeHelpText.textContent = '';
        }

        handleSizeTypeChange();
    }

    // Event listeners
    sizeTypeOneSize.addEventListener('change', handleSizeTypeChange);
    sizeTypeCustom.addEventListener('change', handleSizeTypeChange);
    categorySelect.addEventListener('change', handleCategoryChange);

    sizeCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            toggleSizeInputs(this);
        });
    });

    // Initialize on page load
    handleCategoryChange();
    handleSizeTypeChange();

    // Restore checked checkboxes state on page load (for old() values)
    sizeCheckboxes.forEach(cb => {
        if (cb.checked) {
            toggleSizeInputs(cb);
        }
    });
});
</script>
@endsection
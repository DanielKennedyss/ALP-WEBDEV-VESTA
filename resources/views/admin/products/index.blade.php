@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h6 class="text-muted text-uppercase small tracking-widest mb-2" style="font-size: 10px; font-weight: 700;">Management</h6>
        <h1 class="fw-normal tracking-tighter" style="font-size: 2.5rem;">INVENTORY</h1>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-dark rounded-pill px-4 py-2" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em;">+ NEW PIECE</a>
</div>

<div class="admin-card border-0 shadow-sm p-4" style="background: #fff; border-radius: 12px;">
    <!-- Filters Row -->
    <form id="products-filter-form" action="{{ route('admin.inventory') }}" method="GET" class="mb-4">
        <div class="row g-2 align-items-center">
            <!-- Product Search -->
            <div class="col-md-3 col-12">
                <input type="text" id="filter-product" name="product" value="{{ request('product') }}" class="form-control filter-pill" placeholder="Search product or SKU...">
            </div>
            
            <!-- Category -->
            <div class="col-md-3 col-sm-4 col-12">
                <select id="filter-category" name="category_id" class="form-select filter-pill">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Gender -->
            <div class="col-md-2 col-sm-4 col-12">
                <select id="filter-gender" name="gender" class="form-select filter-pill">
                    <option value="">All Genders</option>
                    <option value="Male" {{ request('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ request('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Unisex" {{ request('gender') === 'Unisex' ? 'selected' : '' }}>Unisex</option>
                </select>
            </div>
            
            <!-- Size -->
            <div class="col-md-2 col-sm-4 col-12">
                <select id="filter-size" name="size" class="form-select filter-pill">
                    <option value="">All Sizes</option>
                    @foreach($sizes as $sz)
                        <option value="{{ $sz }}" {{ request('size') === $sz ? 'selected' : '' }}>{{ $sz }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit & Reset Button -->
            <div class="col-md-2 col-12 d-flex gap-2">
                <button type="submit" class="btn btn-dark rounded-pill px-3 fw-bold text-uppercase w-100" style="font-size: 10px; height: 38px; letter-spacing: 0.05em;">FILTER</button>
                <a href="{{ route('admin.inventory') }}" id="btn-reset-filters" class="btn btn-light rounded-pill px-3 fw-bold text-uppercase border d-flex align-items-center justify-center {{ request()->anyFilled(['product', 'category_id', 'gender', 'size']) ? '' : 'd-none' }}" style="font-size: 10px; height: 38px; min-width: 38px;" title="Reset Filters">✕</a>
            </div>
        </div>
    </form>

    <div class="table-responsive" style="overflow: visible;"> {{-- FIX 1: Override overflow agar dropdown tidak terpotong --}}
        <table class="table table-hover align-middle mb-0">
            <thead class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em;">
                <tr>
                    <th class="border-0 ps-0">Product Detail</th>
                    <th class="border-0">SKU</th>
                    <th class="border-0">Category</th>
                    <th class="border-0">Gender</th>
                    <th class="border-0" style="width: 150px;">Sizes</th>
                    <th class="border-0">Price</th>
                    <th class="border-0">Total Stock</th>
                    <th class="border-0">Status</th>
                    <th class="border-0 text-end pe-0">Action</th>
                </tr>
            </thead>
            <tbody id="products-table-body" style="font-size: 13px;">
                @include('admin.products.table_rows')
            </tbody>
        </table>
    </div>
</div>

<style>
    .table td { border-bottom-color: #f8f9fa; }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .dropdown-item:active { background-color: #1a1a1a; color: white !important; }
    .btn-link:focus { box-shadow: none; outline: none; }
    .object-fit-cover { object-fit: cover; }
    
    /* FIX 1.1: Pastikan dropdown table bisa meluap */
    .table-responsive { overflow-x: visible !important; overflow-y: visible !important; }

    /* Pill-Shape design for inputs and select */
    .filter-pill {
        border-radius: 9999px !important;
        background-color: #fff !important;
        border: 1px solid #dee2e6 !important;
        padding: 0.5rem 1.25rem !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        color: #212529 !important;
        outline: none !important;
        box-shadow: none !important;
        height: 38px !important;
        transition: all 0.2s ease !important;
    }
    .filter-pill:focus {
        border-color: #000 !important;
        background-color: #fff !important;
    }
    select.filter-pill {
        appearance: none !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 1rem center !important;
        background-size: 10px 10px !important;
        padding-right: 2.25rem !important;
    }
</style>

{{-- FIX 4: Hapus bootstrap script duplicate di sini jika master layout sudah memilikinya. 
     Jika terpaksa harus di sini, biarkan, tapi idealnya pindahkan ke master layout. --}}

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Remove from Collection?',
        text: "Piece: " + name + " will be moved to archive.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1a1a1a', 
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Archive it',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        background: '#ffffff',
        customClass: {
            title: 'fw-normal tracking-tight',
            confirmButton: 'rounded-pill px-4',
            cancelButton: 'rounded-pill px-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Archiving...',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById('delete-form-' + id).submit();
        }
    })
}

// AJAX Live Search & Filters Implementation
(function() {
    const filterProduct = document.getElementById('filter-product');
    const filterCategory = document.getElementById('filter-category');
    const filterGender = document.getElementById('filter-gender');
    const filterSize = document.getElementById('filter-size');
    const resetBtn = document.getElementById('btn-reset-filters');
    const filterForm = document.getElementById('products-filter-form');
    
    let debounceTimer;

    function fetchProducts(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('products-table-body');
            if (tbody) tbody.innerHTML = data.html;

            // Update reset button visibility
            const productVal = filterProduct.value.trim();
            const categoryVal = filterCategory.value;
            const genderVal = filterGender.value;
            const sizeVal = filterSize.value;
            
            if (productVal !== '' || categoryVal !== '' || genderVal !== '' || sizeVal !== '') {
                resetBtn.classList.remove('d-none');
            } else {
                resetBtn.classList.add('d-none');
            }
        })
        .catch(error => console.error('Error loading product data:', error));
    }

    function triggerSearch() {
        const productVal = encodeURIComponent(filterProduct.value.trim());
        const categoryVal = encodeURIComponent(filterCategory.value);
        const genderVal = encodeURIComponent(filterGender.value);
        const sizeVal = encodeURIComponent(filterSize.value);
        const baseUrl = "{{ route('admin.inventory') }}";
        const url = `${baseUrl}?product=${productVal}&category_id=${categoryVal}&gender=${genderVal}&size=${sizeVal}`;
        fetchProducts(url);
    }

    if (filterProduct) {
        filterProduct.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(triggerSearch, 400);
        });
    }

    if (filterCategory) {
        filterCategory.addEventListener('change', triggerSearch);
    }

    if (filterGender) {
        filterGender.addEventListener('change', triggerSearch);
    }

    if (filterSize) {
        filterSize.addEventListener('change', triggerSearch);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            filterProduct.value = '';
            filterCategory.value = '';
            filterGender.value = '';
            filterSize.value = '';
            triggerSearch();
        });
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            triggerSearch();
        });
    }
})();
</script>
@endsection
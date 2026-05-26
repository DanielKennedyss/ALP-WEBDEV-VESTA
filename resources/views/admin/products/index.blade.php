@extends('layouts.admin')

@section('admin_content')

{{-- Style E-commerce Premium & Luxury Inventory UI --}}
<style>
    /* Sinkronisasi Tipografi Premium */
    .luxury-title {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        font-weight: 300; /* Tipis elegan */
        letter-spacing: 0.05em;
        color: #121212;
    }

    .luxury-meta {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: #888;
    }

    /* Container data minimalis modern */
    .premium-inventory-container {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.04);
        border-radius: 12px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.01);
        padding: 24px;
    }

    /* Custom Header Tabel Luxury (CAPSLOCK & RAPI) */
    .table-luxury-head th {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #666 !important;
        padding-bottom: 16px !important;
        border-bottom: 1px solid #121212 !important; /* Garis pembatas atas tegas */
    }

    /* Row Data */
    .table-luxury-body td {
        padding: 16px 8px !important;
        font-size: 13px;
        color: #121212;
        border-bottom: 1px solid #f3f4f6 !important;
    }

    /* Mengubah badge AI kelompok lain menjadi teks polos minimalis */
    .luxury-text-tag {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #444;
    }

    /* Format Tampilan Ukuran & Stok yang Lebih Bersih */
    .size-inline-box {
        font-size: 11px;
        font-weight: 500;
        color: #121212;
        background: #f8fafc;
        padding: 3px 6px;
        border-radius: 4px;
        border: 1px solid #edf2f7;
    }
    .size-stock-count {
        color: #718096;
        font-weight: 600;
    }

    /* Status Indicator minimalis tanpa lingkaran warna startup */
    .status-luxury {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .status-healthy { color: #059669; }
    .status-low { color: #d97706; }
    .status-out { color: #dc2626; }

    /* Tombol Create New minimalis bersudut tegas */
    .btn-luxury-black {
        background-color: #121212;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        border: none;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    .btn-luxury-black:hover {
        background-color: #333;
        transform: translateY(-1px);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h6 class="luxury-meta mb-1">MANAGEMENT</h6>
        <h1 class="luxury-title" style="font-size: 2.2rem; margin-bottom: 0;">INVENTORY</h1>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-luxury-black px-4 py-2½ d-flex align-items-center gap-2">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
        NEW PIECE
    </a>
</div>

<div class="premium-inventory-container">
    <div class="table-responsive" style="overflow-x: visible !important; overflow-y: visible !important;">
        <table class="table align-middle mb-0" style="border-collapse: collapse;">
            <thead class="table-luxury-head">
                <tr>
                    <th class="ps-0">PRODUCT DETAIL</th>
                    <th>SKU</th>
                    <th>CATEGORY</th>
                    <th>GENDER</th>
                    <th style="min-width: 180px;">SIZES & STOCK</th>
                    <th>PRICE</th>
                    <th class="text-center">TOTAL</th>
                    <th>STATUS</th>
                    <th class="text-end pe-0">ACTION</th>
                </tr>
            </thead>
            <tbody class="table-luxury-body">
                @forelse($products as $product)
                <tr>
                    {{-- 1. Product Detail --}}
                    <td class="ps-0">
                        <div class="d-flex align-items-center">
                            <div class="overflow-hidden" style="width: 44px; height: 54px; border-radius: 4px; background: #f8fafc;">
                                <img src="{{ $product->image_path && Str::startsWith($product->image_path, 'http') ? $product->image_path : asset('product_image/' . $product->image_path) }}" 
                                     class="w-100 h-100 object-fit-cover" 
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=f1f5f9&color=121212&rounded=false';">
                            </div>
                            <div class="ms-3">
                                <p class="mb-0 fw-semibold text-dark" style="font-size: 13.5px; letter-spacing: -0.01em;">{{ $product->name }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- 2. SKU --}}
                    <td class="text-secondary" style="font-size: 12px; font-family: monospace; font-weight: 500;">{{ $product->sku }}</td>

                    {{-- 3. Category (Diubah dari badge bulat tebal menjadi text tag luxury) --}}
                    <td class="luxury-text-tag">{{ $product->category->name ?? '-' }}</td>

                    {{-- 4. Gender --}}
                    <td class="luxury-text-tag" style="color: #666;">{{ $product->gender }}</td>

                    {{-- 5. Sizes & Stock Inline --}}
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($product->variants as $variant)
                                <span class="size-inline-box">
                                    {{ $variant->size_label }} <span class="size-stock-count">{{ $variant->stock }}</span>
                                </span>
                            @endforeach
                        </div>
                    </td>

                    {{-- 6. Price --}}
                    <td class="fw-semibold text-dark" style="letter-spacing: -0.01em;">IDR {{ number_format($product->price, 0, ',', '.') }}</td>

                    {{-- 7. Total Stock --}}
                    <td class="text-center fw-medium" style="color: #444;">{{ $product->total_stock }}</td>

                    {{-- 8. Status (Bebas dari dot lingkaran bouncy ala AI) --}}
                    <td>
                        @if($product->isOutOfStock())
                            <span class="status-luxury status-out">OUT OF STOCK</span>
                        @elseif($product->hasLowStock())
                            <span class="status-luxury status-low">LOW STOCK</span>
                        @else
                            <span class="status-luxury status-healthy">HEALTHY</span>
                        @endif
                    </td>
                    
                    {{-- 9. Action Controls --}}
                    <td class="text-end pe-0">
                        <div class="dropdown">
                            <button class="btn btn-link text-dark p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="window">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2" style="font-size: 12px; border-radius: 8px; min-width: 160px; z-index: 1050; border: 1px solid #eee;">
                                <li>
                                    <a class="dropdown-item py-2 fw-medium" href="{{ route('admin.products.edit', $product->id) }}">
                                        Edit Piece
                                    </a>
                                </li>
                                @if(in_array(auth()->user()->role, ['owner', 'manager']))
                                    <li><hr class="dropdown-divider opacity-50"></li>
                                    <li>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                              method="POST" 
                                              id="delete-form-{{ $product->id }}" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="dropdown-item py-2 text-danger fw-medium" 
                                                    onclick="confirmDelete('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                                                Delete Piece
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted tracking-wide" style="font-size: 12px;">NO PIECES CURRENTLY IN INVENTORY.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'REMOVE FROM COLLECTION?',
        text: "Piece: " + name.toUpperCase() + " will be permanently archived.",
        icon: 'none', /* Hapus logo tanda seru kuning besar bawaan AI agar clean */
        showCancelButton: true,
        confirmButtonColor: '#121212', 
        cancelButtonColor: '#eaeaea',
        confirmButtonText: 'CONFIRM ARCHIVE',
        cancelButtonText: 'CANCEL',
        reverseButtons: true,
        background: '#ffffff',
        cornerRadius: 0,
        customClass: {
            title: 'luxury-title text-center fs-5',
            confirmButton: 'btn-luxury-black rounded-0 px-4',
            cancelButton: 'btn btn-light text-dark rounded-0 px-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    })
}
</script>
@endsection
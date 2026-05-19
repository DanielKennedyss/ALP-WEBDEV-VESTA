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
            <tbody style="font-size: 13px;">
                @forelse($products as $product)
                <tr>
                    <td class="ps-0">
                        <div class="d-flex align-items-center">
                            <img src="{{ $product->image_path && Str::startsWith($product->image_path, 'http') ? $product->image_path : asset('product_image/' . $product->image_path) }}" 
                                 class="rounded bg-light shadow-sm" 
                                 style="width: 48px; height: 48px; object-fit: cover;" 
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=f8f9fa&color=000';">
                            <div class="ms-3">
                                <p class="mb-0 fw-bold text-dark" style="font-size: 14px;">{{ $product->name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted fw-medium" style="font-size: 11px;">{{ $product->sku }}</td>
                    <td><span class="badge bg-light text-dark fw-normal rounded-pill px-3">{{ $product->category->name ?? '-' }}</span></td>
                    <td class="text-muted">{{ ucfirst($product->gender) }}</td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($product->variants as $variant)
                                <span class="badge bg-white text-dark border shadow-xs" style="font-size: 9px; padding: 4px 8px; border-color: #eee !important;">
                                    {{ $variant->size_label }} <span class="text-muted ms-1">({{ $variant->stock }})</span>
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="fw-bold text-dark">IDR {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="fw-medium">{{ $product->total_stock }}</td>
                    <td style="white-space: nowrap;">
                        @if($product->total_stock <= 0)
                            <span class="text-danger d-flex align-items-center"><i class="bi bi-dot fs-3"></i> Out of Stock</span>
                        @elseif($product->total_stock <= 10)
                            <span class="text-warning d-flex align-items-center"><i class="bi bi-dot fs-3"></i> Low Stock</span>
                        @else
                            <span class="text-success d-flex align-items-center"><i class="bi bi-dot fs-3"></i> Healthy</span>
                        @endif
                    </td>
                    
                    <td class="text-end pe-0">
                        <div class="dropdown">
                            {{-- FIX 2: Tambahkan data-bs-boundary="window" --}}
                            <button class="btn btn-link text-dark p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="window">
                                <i class="bi bi-three-dots-vertical fs-5"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2" style="font-size: 12px; border-radius: 12px; min-width: 160px; z-index: 1050;">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('admin.products.edit', $product->id) }}">
                                        <i class="bi bi-pencil-square me-2"></i> Edit Piece
                                    </a>
                                </li>

                                {{-- Pengecekan RBAC yang lebih bersih --}}
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
                                                    class="dropdown-item py-2 text-danger" 
                                                    onclick="confirmDelete('{{ $product->id }}', '{{ addslashes($product->name) }}')"> {{-- FIX 3: addslashes agar nama produk yg ada petik tidak merusak JS --}}
                                                <i class="bi bi-trash3-fill me-2"></i> Delete Piece
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
                    <td colspan="9" class="text-center py-5 text-muted">No pieces in inventory.</td>
                </tr>
                @endforelse
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
</script>
@endsection
@forelse($products as $product)
<tr>
    <td class="ps-3">
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
        @if($product->isOutOfStock())
            <span class="text-danger d-flex align-items-center"><i class="bi bi-dot fs-3"></i> Out of Stock</span>
        @elseif($product->hasLowStock())
            <span class="text-warning d-flex align-items-center"><i class="bi bi-dot fs-3"></i> Low Stock</span>
        @else
            <span class="text-success d-flex align-items-center"><i class="bi bi-dot fs-3"></i> Healthy</span>
        @endif
    </td>
    
    <td class="text-end pe-3">
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

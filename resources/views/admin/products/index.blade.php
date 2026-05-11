@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h6 class="text-muted text-uppercase small tracking-widest mb-2" style="font-size: 10px; font-weight: 700;">Management</h6>
        <h1 class="fw-normal tracking-tighter" style="font-size: 2.5rem;">INVENTORY</h1>
    </div>
    <a href="/admin/products/create" class="btn btn-dark rounded-pill px-4 py-2" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em;">+ NEW PIECE</a>
</div>

<div class="admin-card">
    <table class="table table-hover align-middle">
        <thead class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em;">
            <tr>
                <th>Product Detail</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody style="font-size: 13px;">
            @forelse($products as $product)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="{{ Str::startsWith($product->image_path, 'http') ? $product->image_path : asset('product_image/' . $product->image_path) }}" class="rounded bg-light" style="width: 45px; height: 45px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/45?text=No+Image'">
                        <div class="ms-3">
                            <p class="mb-0 fw-medium">{{ $product->name }}</p>
                        </div>
                    </div>
                </td>
                <td class="text-muted">{{ $product->sku }}</td>
                <td>{{ $product->category }}</td>
                <td>IDR {{ number_format($product->price, 0, ',', '.') }}</td>
                <td>{{ $product->stock }}</td>
                <td>
                    @if($product->stock <= 0)
                        <span class="status-badge status-out">Out of Stock</span>
                    @elseif($product->stock <= 10)
                        <span class="status-badge status-low">Low Stock</span>
                    @else
                        <span class="text-success small">● Healthy</span>
                    @endif
                </td>
                <td class="text-end">
                    <a href="#" class="text-dark me-2">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">No products in inventory.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

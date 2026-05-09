@extends('layouts.admin')

@section('admin_content')
<div class="mb-5">
    <h6 class="text-muted text-uppercase small tracking-widest mb-2" style="font-size: 10px; font-weight: 700;">Management</h6>
    <h1 class="fw-normal tracking-tighter" style="font-size: 2.5rem;">ORDER MANAGEMENT</h1>
</div>

<div class="admin-card border-0 shadow-sm p-4" style="background: #fff; border-radius: 8px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 small fw-bold text-uppercase py-3" style="font-size: 10px;">Order ID</th>
                    <th class="border-0 small fw-bold text-uppercase py-3" style="font-size: 10px;">Product</th>
                    <th class="border-0 small fw-bold text-uppercase py-3" style="font-size: 10px;">Total Price</th>
                    <th class="border-0 small fw-bold text-uppercase py-3" style="font-size: 10px;">Status</th>
                    <th class="border-0 small fw-bold text-uppercase py-3 text-end" style="font-size: 10px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td class="small fw-bold">#TRX-{{ $trx->id }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded" style="width: 35px; height: 35px; margin-right: 12px; overflow: hidden;">
    <!-- Gunakan folder public/product sesuai rencanamu -->
    <img src="{{ asset('product/' . ($trx->product->image_path ?? 'default.jpg')) }}" 
         class="w-100 h-100 object-fit-cover" 
         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($trx->product->name) }}&background=000&color=fff';">
</div>
                            <div>
                                <p class="mb-0 small fw-bold">{{ $trx->product->name }}</p>
                                <p class="text-muted mb-0" style="font-size: 10px;">{{ ucfirst($trx->product->category) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="small">IDR {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge rounded-pill {{ $trx->status == 'completed' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}" style="font-size: 9px; letter-spacing: 0.05em; padding: 5px 12px;">
                            {{ strtoupper($trx->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        @if($trx->status == 'pending')
                        <form action="{{ route('admin.transactions.updateStatus', $trx->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3" style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em;">MARK AS COMPLETED</button>
                        </form>
                        @else
                        <span class="text-muted small italic" style="font-size: 10px;">No actions available</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted small">No transactions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 
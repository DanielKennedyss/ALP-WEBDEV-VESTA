@foreach($transactions as $trx)
<div class="admin-card shadow-sm bg-white p-5 d-none transaction-details-card mb-5" id="trxDetailCard-{{ $trx->id }}">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <span class="text-uppercase text-muted tracking-widest" style="font-size: 10px; font-weight: 600; letter-spacing: 0.15em;">Transaction Details</span>
            <h3 class="h4 fw-bold mt-1 text-dark mb-0">
                Invoice {{ $trx->invoice_number ?? '#TRX-'.$trx->id }}
            </h3>
        </div>
        <button type="button" onclick="hideTransactionDetails({{ $trx->id }})" class="btn btn-outline-dark btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Back to List">
            <i class="bi bi-arrow-left"></i>
        </button>
    </div>
    
    <div class="row g-4 text-start">
        <!-- Customer info card -->
        <div class="col-md-6">
            <div class="bg-light p-4 rounded-3 border border-light h-100">
                <h6 class="text-uppercase text-muted tracking-wide mb-3" style="font-size: 11px; font-weight: 700; letter-spacing: 0.05em;">Customer Information</h6>
                <p class="mb-1 text-dark fw-bold" style="font-size: 15px;">{{ $trx->customer_name }}</p>
                <p class="mb-3 text-muted" style="font-size: 13px;">{{ $trx->customer_email }}</p>
                
                @if($trx->user)
                    <div class="d-inline-block bg-black text-white px-3 py-1 text-[10px] tracking-wider uppercase font-bold rounded-1" style="font-size: 10px; letter-spacing: 0.08em;">
                        Status: {{ $trx->user->status ?? '' }}
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Order Metadata card -->
        <div class="col-md-6">
            <div class="bg-light p-4 rounded-3 border border-light h-100">
                <h6 class="text-uppercase text-muted tracking-wide mb-3" style="font-size: 11px; font-weight: 700; letter-spacing: 0.05em;">Order Metadata</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Order Date:</span>
                    <span class="fw-semibold small text-dark">{{ $trx->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Payment URL / Token:</span>
                    <span class="font-monospace text-truncate ms-3 small text-dark" style="max-width: 250px;">{{ $trx->payment_url ?? 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Paid At:</span>
                    <span class="fw-semibold small text-dark">{{ $trx->paid_at ? $trx->paid_at->format('M d, Y H:i') : 'Unpaid' }}</span>
                </div>
            </div>
        </div>
        
        <!-- Items breakdown -->
        <div class="col-12">
            <div class="bg-white p-4 rounded-3 border border-light shadow-sm">
                <h6 class="text-uppercase text-muted tracking-wide mb-3" style="font-size: 11px; font-weight: 700; letter-spacing: 0.05em;">Items Purchased</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="border-bottom border-light">
                                <th class="text-muted small py-2 px-0">Product Details</th>
                                <th class="text-muted small py-2 text-center">Size</th>
                                <th class="text-muted small py-2 text-center" style="width: 10%;">Qty</th>
                                <th class="text-muted small py-2 text-end" style="width: 25%;">Price</th>
                                <th class="text-muted small py-2 text-end" style="width: 25%;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($trx->cart_items && is_array($trx->cart_items) && count($trx->cart_items) > 0)
                                @foreach($trx->cart_items as $item)
                                    <tr class="border-bottom border-light-subtle">
                                        <td class="py-3 px-0">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded overflow-hidden me-3" style="width: 44px; height: 56px;">
                                                    <img src="{{ $item['image_path'] ?? asset('product_image/default.jpg') }}" 
                                                         class="w-100 h-100 object-fit-cover" 
                                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($item['name'] ?? 'NA') }}&background=1a1a1a&color=fff';">
                                                </div>
                                                <span class="fw-semibold text-dark" style="font-size: 13px;">{{ $item['name'] }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center py-3">
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis text-uppercase" style="font-size: 10px;">{{ $item['size'] ?? '—' }}</span>
                                        </td>
                                        <td class="text-center py-3 fw-medium text-dark">{{ $item['quantity'] }}</td>
                                        <td class="text-end py-3 text-muted">IDR {{ number_format($item['price'], 0, ',', '.') }}</td>
                                        <td class="text-end py-3 fw-bold text-dark">IDR {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="border-bottom border-light-subtle">
                                    <td class="py-3 px-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded overflow-hidden me-3" style="width: 44px; height: 56px;">
                                                <img src="{{ ($trx->product->image_path ?? null) && Str::startsWith($trx->product->image_path, 'http') ? $trx->product->image_path : asset('product_image/' . ($trx->product->image_path ?? 'default.jpg')) }}" 
                                                     class="w-100 h-100 object-fit-cover"
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($trx->product->name ?? 'NA') }}&background=1a1a1a&color=fff';">
                                            </div>
                                            <span class="fw-semibold text-dark" style="font-size: 13px;">{{ $trx->product->name ?? 'Product Deleted' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis" style="font-size: 10px;">—</span>
                                    </td>
                                    <td class="text-center py-3 fw-medium text-dark">{{ $trx->quantity }}</td>
                                    <td class="text-end py-3 text-muted">IDR {{ number_format(($trx->product->price ?? 0), 0, ',', '.') }}</td>
                                    <td class="text-end py-3 fw-bold text-dark">IDR {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Cost summary breakdown -->
        <div class="col-12">
            <div class="bg-light p-4 rounded-3 border border-light">
                <div class="row justify-content-end">
                    <div class="col-md-6 col-lg-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Subtotal:</span>
                            <span class="text-dark small">IDR {{ number_format($trx->subtotal ?? $trx->total_price, 0, ',', '.') }}</span>
                        </div>
                        
                        @if(($trx->discount_points ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span class="small">Points Discount ({{ number_format($trx->points_redeemed ?? 0) }} PTS):</span>
                                <span class="small">-IDR {{ number_format($trx->discount_points, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if(($trx->discount_voucher ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span class="small">Voucher Discount:</span>
                                <span class="small">-IDR {{ number_format($trx->discount_voucher, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        
                        <hr class="my-2 border-light-subtle">
                        
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold text-dark" style="font-size: 15px;">Total Price:</span>
                            <span class="fw-bold text-dark" style="font-size: 15px;">IDR {{ number_format($trx->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mt-4">
        <button type="button" onclick="hideTransactionDetails({{ $trx->id }})" class="btn btn-dark text-uppercase tracking-wider font-semibold text-white px-5 py-3" style="font-size: 11px; border-radius: 8px;">
            Close Details
        </button>
    </div>
</div>
@endforeach

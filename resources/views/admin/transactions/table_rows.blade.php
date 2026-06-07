@forelse($transactions as $trx)
@php
    // Map order status to tracker category group for filtering
    $statusGroup = 'other';
    if ($trx->status == 'pending') {
        $statusGroup = 'pending';
    } elseif (in_array($trx->status, ['success', 'processing', 'settlement', 'paid'])) {
        $statusGroup = 'packed';
    } elseif ($trx->status == 'shipped') {
        $statusGroup = 'shipped';
    } elseif (in_array($trx->status, ['delivered', 'completed'])) {
        $statusGroup = 'rate';
    }
@endphp
<tr class="order-row" data-status-group="{{ $statusGroup }}">
    {{-- 1. Invoice Number --}}
    <td class="fw-bold font-mono text-dark" style="font-size: 12px;">
        {{ $trx->invoice_number ?? '#TRX-'.$trx->id }}
    </td>
    
    {{-- 2. Product Information --}}
    <td>
        <div class="d-flex align-items-center">
            @php
                $images = [];
                if ($trx->cart_items && is_array($trx->cart_items) && count($trx->cart_items) > 0) {
                    foreach ($trx->cart_items as $item) {
                        $img = $item['image_path'] ?? null;
                        if ($img) {
                            $images[] = Str::startsWith($img, 'http') ? $img : asset('product_image/' . $img);
                        } else {
                            $images[] = asset('product_image/default.jpg');
                        }
                    }
                } else {
                    $img = $trx->product->image_path ?? null;
                    $images[] = $img && Str::startsWith($img, 'http') ? $img : asset('product_image/' . ($img ?? 'default.jpg'));
                }
                $stackImages = array_slice($images, 0, 3);
                $hasMultiple = count($stackImages) > 1;
            @endphp

            @if($hasMultiple)
                <div class="position-relative" style="width: 56px; height: 67px; margin-right: 14px; flex-shrink: 0;">
                    @foreach(array_reverse($stackImages) as $index => $imgSrc)
                        @php
                            $reverseIndex = count($stackImages) - 1 - $index; // 0 for front, 1 for middle, 2 for back
                            $offsetY = $reverseIndex * 6;
                            $offsetX = $reverseIndex * 6;
                            $zIndex = 3 - $reverseIndex;
                            
                            $style = "position: absolute; width: 44px; height: 55px; left: {$offsetX}px; bottom: {$offsetY}px; z-index: {$zIndex}; transition: all 0.2s; border: 1px solid rgba(0,0,0,0.08); box-shadow: -1px 1px 3px rgba(0,0,0,0.08);";
                            if ($reverseIndex > 0) {
                                $opacity = $reverseIndex == 1 ? 0.85 : 0.65;
                                $scale = 1 - ($reverseIndex * 0.04);
                                $style .= " opacity: {$opacity}; filter: grayscale(30%) brightness(0.95); transform: scale({$scale}); transform-origin: bottom left; pointer-events: none;";
                            }
                        @endphp
                        <div class="bg-light rounded-3 overflow-hidden shadow-sm" style="{{ $style }}">
                            <img src="{{ $imgSrc }}" 
                                 class="w-100 h-100 object-fit-cover" 
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(($trx->cart_items && count($trx->cart_items) > 0) ? $trx->cart_items[0]['name'] : ($trx->product->name ?? 'NA')) }}&background=1a1a1a&color=fff';">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-light rounded-3 overflow-hidden border" style="width: 44px; height: 55px; margin-right: 14px; border-color: rgba(0,0,0,0.03); flex-shrink: 0;">
                    <img src="{{ $stackImages[0] }}" 
                         class="w-100 h-100 object-fit-cover" 
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($trx->product->name ?? 'NA') }}&background=1a1a1a&color=fff';">
                </div>
            @endif

            <div class="overflow-hidden">
                <p class="mb-0 fw-semibold text-dark text-truncate" style="font-size: 13px;">
                    {{ ($trx->cart_items && count($trx->cart_items) > 0) ? $trx->cart_items[0]['name'] : ($trx->product->name ?? 'Product Deleted') }}
                </p>
                <p class="text-muted mb-0 uppercase tracking-wider" style="font-size: 10px; font-weight: 500;">
                    @if(isset($trx->product->category) && is_object($trx->product->category))
                        {{ $trx->product->category->name ?? 'Uncategorized' }}
                    @else
                        {{ $trx->product->category ?? 'Uncategorized' }}
                    @endif
                </p>
            </div>
        </div>
    </td>
    
    {{-- 3. Total Price --}}
    <td class="fw-medium text-dark">
        IDR {{ number_format($trx->total_price, 0, ',', '.') }}
    </td>
    
    {{-- 4. Dynamic Badges Status --}}
    <td>
        @php
            $statusColor = match(strtolower($trx->status)) {
                'pending'    => 'bg-warning-subtle text-warning border border-warning-subtle',
                'processing', 'success', 'settlement', 'paid' => 'bg-info-subtle text-info border border-info-subtle',
                'shipped'    => 'bg-primary-subtle text-primary border border-primary-subtle',
                'delivered', 'completed'  => 'bg-success-subtle text-success border border-success-subtle',
                'cancelled', 'failed', 'expired' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'refunded'   => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                default      => 'bg-secondary-subtle text-secondary'
            };
            $statusLabel = match(strtolower($trx->status)) {
                'pending'    => 'Pending Payment',
                'processing', 'success', 'settlement', 'paid' => 'Processing',
                'shipped'    => 'Shipped',
                'delivered', 'completed'  => 'Delivered',
                'cancelled', 'failed', 'expired' => 'Cancelled',
                'refunded'   => 'Refunded',
                default      => $trx->status
            };
        @endphp
        <span class="badge rounded-pill {{ $statusColor }} uppercase font-bold tracking-widest" style="font-size: 9px; padding: 6px 12px;">
            {{ $statusLabel }}
        </span>
    </td>
    
    {{-- 5. Details button --}}
    <td class="text-center">
        <button type="button" onclick="showTransactionDetails({{ $trx->id }})" class="btn btn-outline-dark btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; transition: all 0.2s;" title="View Details">
            <i class="bi bi-eye"></i>
        </button>
    </td>

    {{-- 6. Hybrid Action Controls --}}
    <td class="text-end">
        @if(in_array(strtolower($trx->status), ['cancelled', 'expired', 'failed', 'refunded']))
            <span class="text-muted small fst-italic" style="font-size: 11px;">No actions available</span>
        @elseif(strtolower($trx->status) === 'delivered')
            {{-- Tombol Refund Terpisah --}}
            <form id="form-refund-{{ $trx->id }}" action="{{ route('admin.transactions.updateStatus', $trx->id) }}" method="POST" class="m-0">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="refunded">
                <button type="button" onclick="triggerRefundAlert('{{ $trx->id }}', '{{ $trx->invoice_number }}')" class="btn btn-outline-danger btn-sm shadow-sm" style="font-size: 11px; padding: 6px 12px; border-radius: 8px; font-weight: 600;" title="Refund Order">
                    Refund
                </button>
            </form>
        @else
            <div class="d-inline-flex align-items-center gap-2">
                
                {{-- Dropdown Logistik Maju (Interseptasi via JS SweetAlert) --}}
                <form id="form-logistics-{{ $trx->id }}" action="{{ route('admin.transactions.updateStatus', $trx->id) }}" method="POST" class="m-0">
                    @csrf
                    @method('PATCH')
                    
                    <select name="status" data-old-value="{{ strtolower($trx->status) }}" onchange="triggerLogisticsAlert(this, '{{ $trx->invoice_number }}')" class="form-select form-select-sm select-luxury-sm shadow-sm">
                        
                        {{-- SAFEGUARD OPTION: Mencegah kotak blank jika data DB berisi status di luar opsi logistik utama --}}
                        @if(!in_array(strtolower($trx->status), ['pending', 'processing', 'shipped', 'delivered', 'success', 'settlement', 'paid']))
                            <option value="{{ strtolower($trx->status) }}" selected disabled>{{ ucfirst($trx->status) }}</option>
                        @endif

                        <option value="pending" {{ $trx->status === 'pending' ? 'selected' : '' }} disabled>Pending Payment</option>
                        <option value="processing" {{ in_array(strtolower($trx->status), ['processing', 'success', 'settlement', 'paid']) ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $trx->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $trx->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    </select>
                </form>

                {{-- Tombol Cancel Terpisah (Interseptasi via JS SweetAlert) --}}
                <form id="form-cancel-{{ $trx->id }}" action="{{ route('admin.transactions.updateStatus', $trx->id) }}" method="POST" class="m-0">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="button" onclick="triggerCancelAlert('{{ $trx->id }}', '{{ $trx->invoice_number }}')" class="btn btn-cancel-luxury shadow-sm" title="Cancel Order">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </form>

            </div>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center py-5 text-muted">
        <i class="bi bi-receipt d-block display-6 mb-3 text-secondary"></i>
        <span class="text-uppercase tracking-widest font-medium" style="font-size: 11px;">No transactions recorded in system.</span>
    </td>
</tr>
@endforelse

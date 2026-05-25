@extends('base.base')

@section('content')
@php
    $status = $order->status;
    
    // Progress stage mapping
    $currentLevel = 1; // Default: Dikemas (Paid)
    
    if ($status === 'processing') {
        $currentLevel = 2; // Disortir
    } elseif ($status === 'shipped') {
        $currentLevel = 3; // Dikirim
    } elseif (in_array($status, ['delivered', 'completed'])) {
        $currentLevel = 4; // Sudah Sampai
    }

    $stages = [
        1 => [
            'title' => 'Dikemas',
            'desc' => 'Pesanan Anda sedang dikemas dan disiapkan oleh pihak VESTA.',
            'time' => $order->paid_at ? $order->paid_at->format('d M Y, H:i') : $order->created_at->format('d M Y, H:i')
        ],
        2 => [
            'title' => 'Disortir',
            'desc' => 'Pesanan telah disortir di pusat logistik kami.',
            'time' => $status !== 'pending' && $order->updated_at ? $order->updated_at->format('d M Y, H:i') : 'Menunggu antrean'
        ],
        3 => [
            'title' => 'Dikirim',
            'desc' => 'Pesanan sedang dalam perjalanan ke alamat tujuan (' . strtoupper($order->shipping_courier ?? 'Kurir') . ').',
            'time' => $currentLevel >= 3 ? $order->updated_at->format('d M Y, H:i') : 'Menunggu pengiriman'
        ],
        4 => [
            'title' => 'Sudah Sampai',
            'desc' => 'Pesanan telah diterima oleh penerima.',
            'time' => $currentLevel >= 4 ? $order->updated_at->format('d M Y, H:i') : 'Dalam perjalanan'
        ]
    ];
@endphp

<div class="pt-32 pb-24 px-6 lg:px-8 bg-stone-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumbs -->
        <div class="mb-8 flex items-center gap-2 text-[10px] tracking-[0.2em] uppercase text-stone-400 font-medium">
            <a href="{{ route('home') }}" class="hover:text-stone-900 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('profile') }}" class="hover:text-stone-900 transition-colors">Profile</a>
            <span>/</span>
            <span class="text-stone-900 font-bold">Track Order</span>
        </div>

        <!-- Page Header -->
        <div class="border border-stone-200 bg-white p-8 mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <span class="text-[9px] tracking-[0.25em] text-stone-400 uppercase font-bold">Logistics & Delivery Tracking</span>
                <h1 class="text-2xl font-serif tracking-wider text-stone-900 mt-2 uppercase">Track Order</h1>
                <p class="text-xs text-stone-500 mt-1">Invoice: <span class="font-mono font-bold text-stone-900">{{ $order->invoice_number }}</span></p>
            </div>
            <div class="flex flex-col items-start md:items-end gap-1.5">
                <span class="text-[9px] tracking-[0.2em] text-stone-400 uppercase font-bold">Order Date</span>
                <span class="text-xs text-stone-900 font-medium">{{ $order->created_at->format('M d, Y') }}</span>
                <span class="inline-block border border-emerald-200 bg-emerald-50 text-emerald-700 px-3 py-0.5 text-[9px] tracking-[0.1em] uppercase font-bold mt-1">PAID</span>
            </div>
        </div>

        <!-- Progress Tracker Section -->
        <div class="border border-stone-200 bg-white p-8 md:p-12 mb-8">
            <h3 class="text-xs tracking-[0.2em] uppercase font-bold text-stone-950 mb-10 pb-4 border-b border-stone-100">Pesanan Saya</h3>

            <!-- Desktop Horizontal Stepper (hidden on mobile) -->
            <div class="hidden md:flex justify-between items-start relative mb-12">
                <!-- Progress Line Background -->
                <div class="absolute top-5 left-8 right-8 h-0.5 bg-stone-100 -z-0"></div>
                <!-- Active Progress Line -->
                <div class="absolute top-5 left-8 h-0.5 bg-stone-900 -z-0 transition-all duration-500 ease-in-out" 
                     style="width: {{ (($currentLevel - 1) / 3) * 100 }}%;"></div>

                @foreach($stages as $num => $stage)
                    <div class="flex flex-col items-center text-center w-1/4 relative z-10 px-2">
                        <!-- Step Indicator Node -->
                        <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300
                            {{ $currentLevel >= $num 
                                ? 'bg-stone-900 text-white shadow-md' 
                                : 'bg-white border-2 border-stone-200 text-stone-400' }}">
                            @if($currentLevel > $num)
                                <!-- Check Icon for Completed -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            @elseif($currentLevel == $num)
                                <!-- Active Pulsing Node -->
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                                </span>
                            @else
                                <span class="text-xs font-bold">{{ $num }}</span>
                            @endif
                        </div>

                        <!-- Step Labels -->
                        <span class="text-[10px] tracking-widest uppercase font-bold mt-4 {{ $currentLevel >= $num ? 'text-stone-950' : 'text-stone-400' }}">
                            {{ $stage['title'] }}
                        </span>
                        <span class="text-[9px] text-stone-400 font-mono mt-1 block">
                            {{ $stage['time'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Mobile Vertical Stepper (hidden on desktop) -->
            <div class="flex md:hidden flex-col space-y-8 relative pl-6 before:absolute before:top-2 before:bottom-2 before:left-[11px] before:w-[2px] before:bg-stone-100">
                <!-- Mobile Active Progress Line -->
                <div class="absolute left-[11px] top-2 w-[2px] bg-stone-900 transition-all duration-500 ease-in-out" 
                     style="height: {{ (($currentLevel - 1) / 3) * 90 }}%;"></div>

                @foreach($stages as $num => $stage)
                    <div class="flex items-start gap-4 relative z-10">
                        <!-- Step Indicator Node -->
                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 transition-all duration-300
                            {{ $currentLevel >= $num 
                                ? 'bg-stone-900 text-white shadow' 
                                : 'bg-white border-2 border-stone-200 text-stone-400' }}">
                            @if($currentLevel > $num)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <span class="text-[9px] font-bold">{{ $num }}</span>
                            @endif
                        </div>

                        <!-- Step details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline gap-2">
                                <h4 class="text-xs tracking-wider uppercase font-bold {{ $currentLevel >= $num ? 'text-stone-950' : 'text-stone-400' }}">
                                    {{ $stage['title'] }}
                                </h4>
                                <span class="text-[9px] text-stone-400 font-mono">
                                    {{ $stage['time'] }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-500 mt-1 leading-relaxed">
                                {{ $stage['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Active Stage Descriptive Callout -->
            <div class="mt-8 border-t border-stone-100 pt-8 bg-stone-50/50 -mx-8 -mb-8 p-8 md:-mx-12 md:-mb-12 md:p-12">
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-stone-900/5 text-stone-950 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-[9px] tracking-wider uppercase text-stone-400 font-bold block mb-1">Status Terkini</span>
                        <h4 class="text-xs uppercase font-bold text-stone-900 tracking-wider">
                            {{ $stages[$currentLevel]['title'] }}
                        </h4>
                        <p class="text-xs text-stone-500 leading-relaxed mt-1">
                            {{ $stages[$currentLevel]['desc'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Summary Section -->
        <div class="border border-stone-200 bg-white p-8 mb-8">
            <h3 class="text-xs tracking-[0.2em] uppercase font-bold text-stone-950 mb-6 pb-4 border-b border-stone-100">Product Summary</h3>
            <div class="divide-y divide-stone-100">
                @if($order->cart_items && is_array($order->cart_items) && count($order->cart_items) > 0)
                    @foreach($order->cart_items as $item)
                        <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-16 bg-stone-100 shrink-0 overflow-hidden border border-stone-100">
                                    <img src="{{ $item['image_path'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($item['name']) . '&background=1a1a1a&color=fff' }}" 
                                         class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-stone-900">{{ $item['name'] }}</h4>
                                    @if(!empty($item['size']))
                                        <p class="text-[10px] text-stone-400 mt-1 uppercase">Size: {{ $item['size'] }}</p>
                                    @endif
                                    <p class="text-xs text-stone-400 mt-0.5">Quantity: {{ $item['quantity'] }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-stone-950 font-mono">IDR {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-16 bg-stone-100 shrink-0 overflow-hidden border border-stone-100">
                                <img src="{{ $order->product && $order->product->image_path ? $order->product->image_path : 'https://ui-avatars.com/api/?name=' . urlencode($order->product ? $order->product->name : 'Product') . '&background=1a1a1a&color=fff' }}" 
                                     class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 class="text-xs font-semibold text-stone-900">{{ $order->product ? $order->product->name : 'Product' }}</h4>
                                <p class="text-xs text-stone-400 mt-1">Quantity: {{ $order->quantity }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-stone-950 font-mono">IDR {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
            
            <div class="mt-6 border-t border-stone-100 pt-6 flex justify-between items-center">
                <span class="text-xs tracking-wider text-stone-500">Shipping ({{ strtoupper($order->shipping_courier ?? 'REG') }} - {{ $order->shipping_service ?? 'Standard' }})</span>
                <span class="text-xs font-semibold text-stone-950 font-mono">IDR {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</span>
            </div>
            
            <div class="mt-4 border-t border-stone-100 pt-4 flex justify-between items-center">
                <span class="text-xs font-bold text-stone-900 uppercase tracking-widest">Total Paid</span>
                <span class="text-sm font-bold text-stone-900 font-mono">IDR {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Refund Policy Warning Card -->
        <div class="border border-stone-200 bg-white p-6 flex items-start gap-4">
            <div class="shrink-0 text-stone-900 mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h5 class="text-[10px] uppercase tracking-[0.2em] font-bold text-stone-900 mb-1">Kebijakan Pengembalian Dana & Refund</h5>
                <p class="text-xs text-stone-500 leading-relaxed">
                    Jika Anda ingin melakukan refund atau pengembalian dana setelah pembayaran berhasil, silakan hubungi kami melalui email resmi di <a href="mailto:evanvarian39@gmail.com" class="text-stone-900 underline underline-offset-2 hover:text-stone-700 transition-colors">evanvarian39@gmail.com</a>.
                </p>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('profile') }}" class="inline-block border border-stone-950 text-stone-950 px-8 py-3 text-[10px] tracking-[0.2em] uppercase font-bold hover:bg-stone-950 hover:text-white transition-all duration-300">
                Kembali ke Profile
            </a>
        </div>
    </div>
</div>
@endsection

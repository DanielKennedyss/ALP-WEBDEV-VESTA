@extends('base.base')

@section('content')
<style>
    /* Hover effect for (!) info icon */
    .info-btn-trigger {
        transition: all 0.3s ease !important;
    }
    .info-btn-trigger:hover {
        transform: scale(1.15) !important;
        background-color: #fbbf24 !important; /* bg-amber-400 */
        color: #000000 !important;
        border-color: #fbbf24 !important;
        box-shadow: 0 0 14px rgba(251, 191, 36, 0.7) !important;
    }
</style>
<div class="pt-32 pb-24 px-6 lg:px-8 bg-white min-h-screen">
    <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16">
            <div>
                <span class="text-[10px] tracking-[0.3em] text-gray-400 uppercase font-medium">Welcome Back</span>
                <h1 class="text-4xl md:text-5xl font-serif tracking-[0.1em] mt-2 uppercase">{{ Auth::user()->name }}</h1>
            </div>
            <div class="mt-6 md:mt-0">
                <div class="relative border border-black px-6 py-3 inline-block bg-white text-black">
                    <span class="text-xs tracking-[0.2em] uppercase font-medium">STATUS: {{ auth()->check() ? auth()->user()->status : '' }}</span>
                    
                    <!-- Clickable Info Icon (!) -->
                    <button type="button" onclick="openMembershipModal()" class="info-btn-trigger absolute -top-3 -right-3 bg-black text-white border border-black w-7 h-7 rounded-full flex items-center justify-center cursor-pointer shadow-sm focus:outline-none" title="Membership Info Guide">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            <!-- Loyalty Points -->
            <div class="border border-gray-200 bg-stone-50 p-8 flex flex-col justify-between hover:border-black transition-colors duration-300">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Loyalty Points</span>
                <div class="flex items-baseline gap-2">
                    <h2 class="text-4xl font-light">{{ Auth::user()->loyalty_points ?? 0 }}</h2>
                    <span class="text-xs tracking-widest text-gray-400">PTS</span>
                </div>
            </div>

            <!-- Total Spending -->
            <div class="border border-gray-200 p-8 flex flex-col justify-between hover:border-black transition-colors duration-300">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Total Spending</span>
                <h2 class="text-3xl font-light">IDR {{ number_format(Auth::user()->total_spending ?? 0, 0, ',', '.') }}</h2>
            </div>

            <!-- Active Orders -->
            <div class="border border-gray-200 p-8 flex flex-col justify-between hover:border-black transition-colors duration-300">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Active Orders</span>
                <h2 class="text-4xl font-light">{{ $transactions->where('status', 'pending')->count() }}</h2>
            </div>

            <!-- Quick Actions -->
            <div class="border border-gray-200 p-8 flex flex-col justify-between hover:border-black transition-colors duration-300">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Account Action</span>
                <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                    @csrf
                    <button type="submit" class="text-xs tracking-[0.2em] uppercase font-medium hover:text-gray-500 transition-colors flex items-center gap-2">
                        Logout 
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Order History Section -->
        <div class="mb-16">
            <h4 class="text-sm tracking-[0.2em] uppercase font-medium mb-8">My Order History</h4>
            
            @if($transactions->isEmpty())
                <div class="border border-gray-200 p-12 text-center">
                    <p class="text-sm tracking-widest text-gray-400 uppercase">You haven't made any orders yet.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-black">
                                <th class="pb-4 text-[10px] tracking-[0.2em] uppercase text-gray-400 font-medium whitespace-nowrap pr-6">Order ID</th>
                                <th class="pb-4 text-[10px] tracking-[0.2em] uppercase text-gray-400 font-medium whitespace-nowrap pr-6">Date</th>
                                <th class="pb-4 text-[10px] tracking-[0.2em] uppercase text-gray-400 font-medium whitespace-nowrap pr-6">Product</th>
                                <th class="pb-4 text-[10px] tracking-[0.2em] uppercase text-gray-400 font-medium whitespace-nowrap pr-6">Total</th>
                                <th class="pb-4 text-[10px] tracking-[0.2em] uppercase text-gray-400 font-medium whitespace-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $order)
                            <tr class="border-b border-gray-100 hover:bg-stone-50 transition-colors">
                                <td class="py-6 pr-6 text-sm font-medium">{{ $order->invoice_number }}</td>
                                <td class="py-6 pr-6 text-sm text-gray-600">{{ $order->created_at->format('M d, Y H:i') }}</td>
                                <td class="py-6 pr-6 text-sm">{{ $order->product ? $order->product->name : 'Unknown Product' }} <span class="text-gray-400 ml-1">(x{{ $order->quantity }})</span></td>
                                <td class="py-6 pr-6 text-sm">IDR {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="py-6">
                                    @if($order->status == 'completed' || $order->status == 'success')
                                        <span class="inline-block border border-green-200 bg-green-50 text-green-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase">Success</span>
                                    @elseif($order->status == 'pending')
                                        <span class="inline-block border border-yellow-200 bg-yellow-50 text-yellow-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase">Pending</span>
                                    @else
                                        <span class="inline-block border border-red-200 bg-red-50 text-red-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase">Failed</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Banner / Information -->
        <div class="bg-black text-white p-12 text-center">
            <h3 class="text-2xl md:text-3xl font-serif tracking-[0.1em] mb-4">VESTA Winter Collection '26 is coming.</h3>
            <p class="text-[10px] text-gray-400 uppercase tracking-[0.3em]">Exclusively for {{ auth()->check() ? auth()->user()->status : '' }} members.</p>
        </div>
    </div>
</div>
@endsection
@extends('base.base')

@section('content')
{{-- Animasi agar profil terlihat lebih premium --}}
<style>
    .profile-card {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
    }
    .profile-card:hover {
        transform: translateY(-5px);
        border-color: #000 !important;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
</style>

<div class="pt-32 pb-24 px-6 lg:px-8 bg-white min-h-screen">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 animate-fade-in">
            <div>
                <span class="text-[10px] tracking-[0.3em] text-gray-400 uppercase font-medium">Welcome Back</span>
                <h1 class="text-4xl md:text-5xl font-serif tracking-[0.1em] mt-2 uppercase">{{ Auth::user()->name }}</h1>
            </div>
            <div class="mt-6 md:mt-0">
                <div class="border border-black px-6 py-3 inline-block">
                    <span class="text-xs tracking-[0.2em] uppercase font-medium">Tier: {{ Auth::user()->membership_level ?? 'Bronze' }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            <div class="profile-card delay-1 border border-gray-200 bg-stone-50 p-8 flex flex-col justify-between">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Loyalty Points</span>
                <div class="flex items-baseline gap-2">
                    {{-- Otomatis terupdate via Model Observer --}}
                    <h2 class="text-4xl font-light">{{ number_format(Auth::user()->loyalty_points ?? 0, 0) }}</h2>
                    <span class="text-xs tracking-widest text-gray-400">PTS</span>
                </div>
            </div>

            <div class="profile-card delay-2 border border-gray-200 p-8 flex flex-col justify-between">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Total Spending</span>
                {{-- Otomatis terupdate via Model Observer --}}
                <h2 class="text-3xl font-light">IDR {{ number_format(Auth::user()->total_spending ?? 0, 0, ',', '.') }}</h2>
            </div>

            <div class="profile-card delay-3 border border-gray-200 p-8 flex flex-col justify-between">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Active Orders</span>
                <h2 class="text-4xl font-light">{{ $transactions->where('status', 'pending')->count() }}</h2>
            </div>

            <div class="profile-card delay-4 border border-gray-200 p-8 flex flex-col justify-between">
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

        <div class="mb-16 profile-card delay-4">
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
                                <th class="pb-4 text-[10px] tracking-[0.2em] uppercase text-gray-400 font-medium whitespace-nowrap pr-6">Status</th>
                                <th class="pb-4 text-[10px] tracking-[0.2em] uppercase text-gray-400 font-medium whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $order)
                            <tr class="border-b border-gray-100 hover:bg-stone-50 transition-colors">
                                <td class="py-6 pr-6 text-sm font-medium">{{ $order->invoice_number }}</td>
                                <td class="py-6 pr-6 text-sm text-gray-600">{{ $order->created_at->format('M d, Y H:i') }}</td>
                                <td class="py-6 pr-6 text-sm">
                                    {{ $order->product ? $order->product->name : 'Unknown Product' }} 
                                    <span class="text-gray-400 ml-1">(x{{ $order->quantity }})</span>
                                </td>
                                <td class="py-6 pr-6 text-sm">IDR {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="py-6 pr-6">
                                    {{-- Sinkronisasi Status: success & completed --}}
                                    @if($order->status == 'completed' || $order->status == 'success')
                                        <span class="inline-block border border-green-200 bg-green-50 text-green-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase">Success</span>
                                    @elseif($order->status == 'pending')
                                        <span class="inline-block border border-yellow-200 bg-yellow-50 text-yellow-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase">Pending</span>
                                    @else
                                        <span class="inline-block border border-red-200 bg-red-50 text-red-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td class="py-6">
                                    @if($order->status == 'pending' && $order->payment_url)
                                        <a href="{{ route('payment.retry', $order->id) }}" class="inline-block bg-black text-white text-[9px] tracking-[0.15em] uppercase px-4 py-2 hover:bg-gray-800 transition-colors">
                                            Pay Now
                                        </a>
                                    @else
                                        <span class="text-[10px] tracking-wider text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-black text-white p-12 text-center profile-card delay-4">
            <h3 class="text-2xl md:text-3xl font-serif tracking-[0.1em] mb-4">VESTA Winter Collection '26 is coming.</h3>
            <p class="text-[10px] text-gray-400 uppercase tracking-[0.3em]">Exclusively for {{ Auth::user()->membership_level ?? 'Bronze' }} members.</p>
        </div>
    </div>
</div>
@endsection
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

<div class="pt-32 pb-24 px-6 lg:px-8 bg-white min-h-screen" x-data="{ activeTab: '{{ ($errors->any() || session('open-profile-tab')) ? 'my-profile' : 'order-history' }}' }">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 animate-fade-in">
            <div>
                <span class="text-[10px] tracking-[0.3em] text-gray-400 uppercase font-medium">Welcome Back</span>
                <h1 class="text-4xl md:text-5xl font-serif tracking-[0.1em] mt-2 uppercase">{{ Auth::user()->name }}</h1>
            </div>
            <div class="mt-6 md:mt-0">
                <div class="border border-black px-6 py-3 inline-block bg-black text-white">
                    {{-- MEMANGGIL NAMA TIER LUXURY SECARA OTOMATIS --}}
                    <span class="text-xs tracking-[0.2em] uppercase font-bold">STATUS: {{ Auth::user()->membership_tier_badge }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
            <div class="profile-card delay-1 border border-gray-200 bg-stone-50 p-8 flex flex-col justify-between">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Privilege Points</span>
                <div class="flex items-baseline gap-2">
                    {{-- MENAMPILKAN POIN YANG SUDAH DIKALIBRASI (Rp 1.000 / Poin) --}}
                    <h2 class="text-4xl font-light">{{ number_format(Auth::user()->loyalty_points ?? 0, 0, ',', '.') }}</h2>
                    <span class="text-xs tracking-widest text-gray-400">PTS</span>
                </div>
            </div>

            <div class="profile-card delay-2 border border-gray-200 p-8 flex flex-col justify-between">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Lifetime Spending</span>
                <h2 class="text-3xl font-light">IDR {{ number_format(Auth::user()->total_spending ?? 0, 0, ',', '.') }}</h2>
            </div>

            <div class="profile-card delay-3 border border-gray-200 p-8 flex flex-col justify-between">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Active Orders</span>
                <h2 class="text-4xl font-light">{{ $transactions->where('status', 'pending')->count() }}</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 items-start mb-16">
            <!-- Navigation Sidebar -->
            <div class="lg:col-span-1 space-y-3 profile-card delay-4">
                <button type="button" @click="activeTab = 'order-history'" :class="activeTab === 'order-history' ? 'bg-stone-900 text-white border-black' : 'border-stone-200 hover:border-black text-stone-900 bg-white'" class="w-full text-left py-4 px-6 border font-semibold text-[11px] tracking-[0.2em] uppercase transition-all duration-300 flex justify-between items-center group">
                    <span>My Order History</span>
                    <svg class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                <button type="button" @click="activeTab = 'my-profile'" :class="activeTab === 'my-profile' ? 'bg-stone-900 text-white border-black' : 'border-stone-200 hover:border-black text-stone-900 bg-white'" class="w-full text-left py-4 px-6 border font-semibold text-[11px] tracking-[0.2em] uppercase transition-all duration-300 flex justify-between items-center group">
                    <span>My Profile</span>
                    <svg class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                <div class="pt-6 !mt-6 border-t border-stone-100">
                    <form action="{{ route('logout') }}" method="POST" class="w-full m-0">
                        @csrf
                        <button type="submit" class="w-full text-left py-4 px-6 border border-stone-200 hover:border-red-600 font-semibold text-[11px] tracking-[0.2em] uppercase transition-all duration-300 text-stone-500 hover:text-red-600 flex justify-between items-center group bg-white">
                            <span>Sign Out</span>
                            <svg class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content Area -->
            <div class="lg:col-span-3 profile-card delay-4">
                
                <!-- Content Area: Order History -->
                <div x-show="activeTab === 'order-history'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="border border-stone-200 p-8 bg-white">
                    <h4 class="text-xs tracking-[0.2em] uppercase font-bold mb-8 text-stone-900 border-b border-stone-100 pb-4">My Order History</h4>

                    @if($transactions->isEmpty())
                        <div class="border border-stone-100 p-12 text-center bg-stone-50/50">
                            <p class="text-xs tracking-widest text-stone-400 uppercase">You haven't made any orders yet.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-stone-200">
                                        <th class="pb-4 text-[9px] tracking-[0.2em] uppercase text-stone-400 font-bold whitespace-nowrap pr-6">Order ID</th>
                                        <th class="pb-4 text-[9px] tracking-[0.2em] uppercase text-stone-400 font-bold whitespace-nowrap pr-6">Date</th>
                                        <th class="pb-4 text-[9px] tracking-[0.2em] uppercase text-stone-400 font-bold whitespace-nowrap pr-6">Product(s)</th>
                                        <th class="pb-4 text-[9px] tracking-[0.2em] uppercase text-stone-400 font-bold whitespace-nowrap pr-6">Total</th>
                                        <th class="pb-4 text-[9px] tracking-[0.2em] uppercase text-stone-400 font-bold whitespace-nowrap pr-6">Status</th>
                                        <th class="pb-4 text-[9px] tracking-[0.2em] uppercase text-stone-400 font-bold whitespace-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $order)
                                    <tr class="border-b border-stone-100 hover:bg-stone-50/50 transition-colors">
                                        <td class="py-6 pr-6 text-xs font-semibold text-stone-900">{{ $order->invoice_number }}</td>
                                        <td class="py-6 pr-6 text-xs text-stone-500">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="py-6 pr-6 text-xs text-stone-800">
                                            @if($order->cart_items && is_array($order->cart_items) && count($order->cart_items) > 0)
                                                <span class="font-medium text-stone-900">{{ $order->cart_items[0]['name'] }}</span>
                                                <span class="text-stone-400 ml-1">(x{{ $order->cart_items[0]['quantity'] }})</span>
                                                
                                                @if(count($order->cart_items) > 1)
                                                    <br>
                                                    <span class="text-[10px] text-stone-400 italic mt-1 inline-block">
                                                        + {{ count($order->cart_items) - 1 }} other item(s)
                                                    </span>
                                                @endif
                                            @else
                                                <span class="font-medium text-stone-900">{{ $order->product ? $order->product->name : 'Unknown Product' }}</span>
                                                <span class="text-stone-400 ml-1">(x{{ $order->quantity }})</span>
                                            @endif
                                        </td>
                                        <td class="py-6 pr-6 text-xs font-medium text-stone-900">IDR {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td class="py-6 pr-6">
                                             @if($order->status == 'pending')
                                                 <span class="inline-block border border-amber-200 bg-amber-50 text-amber-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase font-bold">PENDING</span>
                                             @elseif(in_array($order->status, ['success', 'processing', 'settlement', 'paid']))
                                                 <span class="inline-block border border-emerald-200 bg-emerald-50 text-emerald-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase font-bold">PAID</span>
                                             @elseif($order->status == 'shipped')
                                                 <span class="inline-block border border-indigo-200 bg-indigo-50 text-indigo-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase font-bold">Dikirim</span>
                                             @elseif($order->status == 'delivered')
                                                 <span class="inline-block border border-emerald-200 bg-emerald-50 text-emerald-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase font-bold">Diterima</span>
                                             @elseif($order->status == 'completed')
                                                 <span class="inline-block border border-green-200 bg-green-50 text-green-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase font-bold">Selesai</span>
                                             @else
                                                 <span class="inline-block border border-stone-200 bg-stone-50 text-stone-700 px-3 py-1 text-[9px] tracking-[0.1em] uppercase font-bold">{{ $order->status }}</span>
                                             @endif
                                        </td>
                                        <td class="py-6">
                                            <div class="flex items-center gap-3">
                                                @if($order->status == 'pending' && $order->payment_url)
                                                    <a href="{{ route('payment.retry', $order->id) }}" class="inline-block bg-black text-white text-[9px] tracking-[0.15em] uppercase px-4 py-2 hover:bg-stone-800 transition-colors">
                                                        Pay Now
                                                    </a>
                                                    
                                                    <form action="{{ route('profile.orders.cancel', $order->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                                        @csrf
                                                        <button type="submit" class="relative group flex items-center justify-center w-8 h-8 rounded-full border border-stone-200 bg-stone-50 text-stone-500 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all duration-300 shadow-sm" title="Batalkan Pesanan">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            <span class="absolute bottom-full mb-2 hidden group-hover:block bg-stone-900 text-white text-[8px] tracking-wider uppercase px-2 py-1 whitespace-nowrap rounded shadow-md z-10">
                                                                Batalkan Pesanan
                                                            </span>
                                                        </button>
                                                    </form>
                                                @elseif(in_array($order->status, ['success', 'processing', 'settlement', 'paid', 'shipped', 'delivered', 'completed']))
                                                    <a href="{{ route('profile.orders.track', $order->id) }}" class="inline-block bg-black text-white text-[9px] tracking-[0.15em] uppercase px-4 py-2 hover:bg-stone-850 transition-colors">
                                                        Track
                                                    </a>

                                                    @if($order->status == 'shipped')
                                                        <form action="{{ route('profile.orders.receive', $order->id) }}" method="POST" class="inline-block m-0 ml-2">
                                                            @csrf
                                                            <button type="submit" class="inline-block border border-black bg-white text-black text-[9px] tracking-[0.15em] uppercase px-4 py-2 hover:bg-stone-100 transition-colors">
                                                                Diterima
                                                            </button>
                                                        </form>
                                                    @elseif(in_array($order->status, ['delivered', 'completed']))
                                                        @php
                                                            $isReviewed = $order->reviews->isNotEmpty();
                                                            $reviewItems = [];
                                                            if ($order->cart_items && is_array($order->cart_items) && count($order->cart_items) > 0) {
                                                                foreach ($order->cart_items as $item) {
                                                                    $reviewItems[] = [
                                                                        'product_id' => $item['product_id'],
                                                                        'name' => $item['name'],
                                                                        'size' => $item['size'] ?? null,
                                                                        'image_path' => $item['image_path'] ?? null
                                                                    ];
                                                                }
                                                            } else {
                                                                $reviewItems[] = [
                                                                    'product_id' => $order->product_id,
                                                                    'name' => $order->product ? $order->product->name : 'Product',
                                                                    'size' => null,
                                                                    'image_path' => $order->product ? $order->product->image_path : null
                                                                ];
                                                            }
                                                            $encodedItems = json_encode($reviewItems);
                                                        @endphp
                                                        
                                                        @if($isReviewed)
                                                            <button disabled class="inline-block border border-stone-200 text-stone-400 text-[9px] tracking-[0.15em] uppercase px-4 py-2 cursor-not-allowed ml-2">
                                                                Sudah Dinilai
                                                            </button>
                                                        @else
                                                            <button type="button" onclick="openReviewModal({{ $order->id }}, '{{ $order->invoice_number }}', {{ $encodedItems }})" class="inline-block bg-black text-white text-[9px] tracking-[0.15em] uppercase px-4 py-2 hover:bg-stone-800 transition-colors ml-2">
                                                                Beri Penilaian
                                                            </button>
                                                        @endif
                                                    @endif
                                                @else
                                                    <span class="text-[10px] tracking-wider text-stone-400">—</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Premium Refund Policy Notice Box -->
                        <div class="mt-8 border border-stone-200 bg-stone-50/50 p-6 flex items-start gap-4">
                            <div class="shrink-0 text-stone-900 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.854l-.041.02a.75.75 0 01-1.063-.854zm0 3h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-[10px] uppercase tracking-[0.2em] font-bold text-stone-900 mb-1">Informasi Pengembalian Dana (Refund Policy)</h5>
                                <p class="text-xs text-stone-500 leading-relaxed">
                                    Jika Anda ingin melakukan refund atau pengembalian dana setelah pembayaran berhasil, silakan hubungi kami melalui email resmi di <a href="mailto:evanvarian39@gmail.com" class="text-stone-900 underline underline-offset-2 hover:text-stone-700 transition-colors">evanvarian39@gmail.com</a>.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Content Area: Profile & Account Settings -->
                <div x-show="activeTab === 'my-profile'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-12">
                    
                    <!-- Section 1: Personal Details -->
                    <div class="border border-stone-200 p-8 bg-white">
                        <h4 class="text-xs tracking-[0.2em] uppercase font-bold mb-8 text-stone-900 border-b border-stone-100 pb-4">Personal Details</h4>
                        
                        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PATCH')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                                    @error('name')
                                        <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Email Address</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                                    @error('email')
                                        <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="phone_number" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Phone Number</label>
                                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', Auth::user()->phone_number) }}" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" placeholder="e.g. +62812345678">
                                @error('phone_number')
                                    <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="pt-4 text-right">
                                <button type="submit" class="bg-black text-white text-[10px] tracking-[0.2em] uppercase px-8 py-4 hover:bg-stone-800 transition-colors">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Section 2: Change Password -->
                    <div class="border border-stone-200 p-8 bg-white">
                        <h4 class="text-xs tracking-[0.2em] uppercase font-bold mb-8 text-stone-900 border-b border-stone-100 pb-4">Security & Password</h4>

                        <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <label for="current_password" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Current Password</label>
                                <input type="password" name="current_password" id="current_password" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                                @if($errors->updatePassword->has('current_password'))
                                    <span class="text-[10px] text-red-600 mt-1 block">{{ $errors->updatePassword->first('current_password') }}</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="password" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">New Password</label>
                                    <input type="password" name="password" id="password" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                                    @if($errors->updatePassword->has('password'))
                                        <span class="text-[10px] text-red-600 mt-1 block">{{ $errors->updatePassword->first('password') }}</span>
                                    @endif
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                                </div>
                            </div>

                            <div class="pt-4 text-right">
                                <button type="submit" class="bg-black text-white text-[10px] tracking-[0.2em] uppercase px-8 py-4 hover:bg-stone-800 transition-colors">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Section 3: Delete Account -->
                    <div class="border border-red-200 bg-red-50/30 p-8">
                        <h4 class="text-xs tracking-[0.2em] uppercase font-bold mb-4 text-red-900">Danger Zone</h4>
                        <p class="text-xs text-stone-500 mb-6 leading-relaxed">Once you delete your account, there is no going back. All of your privilege points, loyalty histories, and transaction records will be permanently erased.</p>
                        
                        @if($errors->userDeletion->any())
                            <div class="mb-6 p-4 bg-red-100/50 border border-red-200 text-red-700 text-xs">
                                {{ $errors->userDeletion->first() }}
                            </div>
                        @endif

                        <div>
                            <button type="button" onclick="openDeleteModal()" class="border border-red-600 text-red-600 hover:bg-red-600 hover:text-white text-[10px] tracking-[0.2em] uppercase px-6 py-3.5 transition-colors font-bold">
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-black text-white p-12 text-center profile-card delay-4">
            <h3 class="text-2xl md:text-3xl font-serif tracking-[0.1em] mb-4">VESTA Winter Collection '26 is coming.</h3>
            {{-- FOOTER EKSKLUSIF BERDASARKAN TIER --}}
            <p class="text-[10px] text-gray-400 uppercase tracking-[0.3em]">Exclusive preview for {{ Auth::user()->membership_tier_badge }} members.</p>
        </div>
    </div>
</div>

<!-- Elegant Premium Review Modal -->
<div id="reviewModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center" aria-hidden="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeReviewModal()"></div>
    <div class="relative bg-white max-w-xl w-full p-8 overflow-y-auto max-h-[85vh] shadow-2xl border border-stone-100 flex flex-col z-[101] animate-fade-in">
        <button type="button" onclick="closeReviewModal()" class="absolute top-6 right-6 hover:text-stone-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h3 class="text-xl font-serif tracking-wider mb-2 text-stone-900">Leave Your Review</h3>
        <p class="text-xs text-stone-400 uppercase tracking-widest mb-6">Order ID: <span id="modal-invoice-number"></span></p>
        
        <form id="reviewForm" method="POST" action="">
            @csrf
            <div id="modal-review-items" class="space-y-8 divide-y divide-stone-100 max-h-[50vh] overflow-y-auto pr-2">
                <!-- Dynamically populated via JS -->
            </div>
            
            <div class="mt-8 border-t border-stone-100 pt-6">
                <button type="submit" class="w-full bg-black text-white text-xs tracking-[0.2em] py-4 hover:bg-stone-800 transition-all uppercase duration-200">
                    Submit Reviews
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Premium Star Rating & Review Modal
    function openReviewModal(orderId, invoiceNumber, items) {
        const modal = document.getElementById('reviewModal');
        const invoiceSpan = document.getElementById('modal-invoice-number');
        const itemsDiv = document.getElementById('modal-review-items');
        const form = document.getElementById('reviewForm');
        
        // Set action route dynamic
        form.action = `/profile/orders/${orderId}/review`;
        invoiceSpan.textContent = invoiceNumber;
        
        // Populate HTML
        itemsDiv.innerHTML = '';
        
        items.forEach((item, index) => {
            // Clean up image path if empty or relative
            let imgUrl = item.image_path;
            if (!imgUrl) {
                imgUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=1a1a1a&color=fff`;
            }
            
            const itemHtml = `
                <div class="pt-6 first:pt-0">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-16 bg-stone-100 shrink-0 overflow-hidden">
                            <img src="${imgUrl}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-stone-900">${item.name}</p>
                            ${item.size ? `<p class="text-[10px] text-stone-400 mt-1 uppercase">Size: ${item.size}</p>` : ''}
                            <input type="hidden" name="reviews[${index}][product_id]" value="${item.product_id}">
                        </div>
                    </div>
                    
                    <!-- Star Rating Selector -->
                    <div class="mb-4">
                        <span class="text-[9px] tracking-[0.2em] uppercase text-stone-400 block mb-2 font-medium">Rating</span>
                        <div class="flex gap-2 star-rating-container" data-item-index="${index}">
                            <input type="hidden" name="reviews[${index}][rating]" id="rating-input-${index}" value="5">
                            ${[1, 2, 3, 4, 5].map(star => `
                                <button type="button" onclick="setStarRating(${index}, ${star})" class="star-btn text-stone-300 hover:scale-110 active:scale-95 transition-all duration-200" data-star="${star}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            `).join('')}
                        </div>
                    </div>
                    
                    <!-- Comment Textarea -->
                    <div>
                        <span class="text-[9px] tracking-[0.2em] uppercase text-stone-400 block mb-2 font-medium">Review Comment</span>
                        <textarea name="reviews[${index}][comment]" rows="2" placeholder="Tell us what you love about this item..." class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 resize-none transition-all duration-300"></textarea>
                    </div>
                </div>
            `;
            itemsDiv.insertAdjacentHTML('beforeend', itemHtml);
        });
        
        // Initialize ratings to 5 stars
        items.forEach((item, index) => {
            setStarRating(index, 5);
        });
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function setStarRating(itemIndex, rating) {
        const container = document.querySelector(`.star-rating-container[data-item-index="${itemIndex}"]`);
        const input = document.getElementById(`rating-input-${itemIndex}`);
        input.value = rating;
        
        const starBtns = container.querySelectorAll('.star-btn');
        starBtns.forEach(btn => {
            const starVal = parseInt(btn.dataset.star);
            if (starVal <= rating) {
                btn.classList.remove('text-stone-300');
                btn.classList.add('text-black');
            } else {
                btn.classList.remove('text-black');
                btn.classList.add('text-stone-300');
            }
        });
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
</script>

<!-- Elegant Delete Account Modal -->
<div id="deleteModal" class="fixed inset-0 z-[100] {{ $errors->userDeletion->any() ? '' : 'hidden' }} flex items-center justify-center" aria-hidden="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white max-w-md w-full p-8 shadow-2xl border border-stone-100 flex flex-col z-[101] animate-fade-in text-center">
        <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <h3 class="text-lg font-serif tracking-wider mb-2 text-stone-900">Are you absolutely sure?</h3>
        <p class="text-xs text-stone-500 mb-6 leading-relaxed">This action cannot be undone. Please type your password to confirm and permanently delete your account.</p>
        
        <form action="{{ route('profile.destroy') }}" method="POST" class="space-y-4">
            @csrf
            @method('DELETE')
            
            <div class="text-left">
                <label for="delete_password" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Your Password</label>
                <input type="password" name="password" id="delete_password" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                @if($errors->userDeletion->has('password'))
                    <span class="text-[10px] text-red-600 mt-1 block text-left">{{ $errors->userDeletion->first('password') }}</span>
                @endif
            </div>

            <div class="space-y-2 pt-2">
                <button type="submit" class="w-full bg-red-600 text-white text-xs tracking-[0.2em] py-4 hover:bg-red-700 transition-colors uppercase font-bold">
                    Yes, Delete My Account
                </button>
                <button type="button" onclick="closeDeleteModal()" class="w-full border border-stone-200 text-stone-600 text-xs tracking-[0.2em] py-4 hover:bg-stone-50 transition-colors uppercase font-bold bg-white">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 
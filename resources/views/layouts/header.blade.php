<nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand -->
            <a href="/" class="text-xl font-serif tracking-[0.3em]">VESTA</a>

            <!-- Navigation Links (desktop) -->
            <div class="hidden md:flex items-center gap-10">
                <a href="/" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">HOME</a>
                <a href="{{ route('collection') }}" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">COLLECTION</a>
                <a href="/#about" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">ABOUT</a>
                <a href="/#contact" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">CONTACT</a>
            </div>

            <!-- Right Icons -->
            <div class="flex items-center gap-6">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">
                        ACCOUNT
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline m-0">
                        @csrf
                        <button type="submit" class="text-xs tracking-[0.2em] text-red-600 hover:text-red-700 transition-colors uppercase">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors border border-current px-4 py-2">
                        LOGIN
                    </a>
                @endauth

                <a href="{{ route('cart.view') }}" class="hover:text-gray-600 transition-colors relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    @php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
                    @if($cartCount > 0)
                    <span class="absolute -top-2 -right-2 bg-black text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</nav>
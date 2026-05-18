<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VESTRA - Luxury Fashion Redefined</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-black antialiased">

    <!-- STICKY NAVBAR -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="/" class="text-xl font-serif tracking-[0.3em]">VESTRA</a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-10">
                    <a href="/" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">HOME</a>
                    <a href="#collection" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">COLLECTION</a>
                    <a href="#about" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">ABOUT</a>
                    <a href="#contact" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">CONTACT</a>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('login') }}" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors border border-current px-4 py-2">
                        LOGIN
                    </a>
                    <button class="hover:text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </button>
                    <button class="md:hidden hover:text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="relative w-full h-screen overflow-hidden">
        <!-- Video Background -->
        <video
            class="absolute inset-0 w-full h-full object-cover"
            autoplay
            muted
            loop
            playsinline
            poster="https://images.unsplash.com/photo-1558171813-4c088753af8f?w=1920&q=80"
        >
            <source src="/assets/video/hero.mp4" type="video/mp4">
        </video>

        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-black/60"></div>

        <!-- Hero Content -->
        <div class="absolute inset-0 flex flex-col items-center justify-center z-10">
            <h1 class="text-white text-5xl md:text-7xl lg:text-8xl font-serif tracking-[0.2em] mb-4">VESTRA</h1>
            <div class="w-20 h-px bg-white/50 my-6"></div>
            <p class="text-white/80 text-xs md:text-sm tracking-[0.4em] uppercase">Luxury Fashion Redefined</p>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 animate-bounce">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </section>

<<<<<<< Updated upstream
    <!-- BRAND INTRO -->
    <section id="about" class="w-full py-32 px-6 lg:px-8 bg-white">
        <div class="max-w-3xl mx-auto text-center">
            <span class="text-xs tracking-[0.3em] text-gray-400 uppercase">The House of Vestra</span>
            <h2 class="text-3xl md:text-4xl font-serif tracking-[0.1em] mt-6 mb-8">Crafting Excellence</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                Discover our curated collection of luxury fashion pieces, meticulously crafted with the finest materials and unwavering attention to detail.
            </p>
        </div>
    </section>
=======
>>>>>>> Stashed changes

    <!-- PRODUCT COLLECTION -->
    <section id="collection" class="w-full py-24 px-6 lg:px-8 bg-stone-50">
        <div class="max-w-7xl mx-auto">
            <!-- Section Title -->
            <div class="text-center mb-20">
                <span class="text-xs tracking-[0.3em] text-gray-400 uppercase">Our Selection</span>
                <h2 class="text-4xl md:text-5xl font-serif tracking-[0.15em] mt-4">THE COLLECTION</h2>
                <div class="w-16 h-px bg-black mx-auto mt-8"></div>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10" id="productGrid">
                @foreach($products as $product)
                <article class="group flex flex-col cursor-pointer" data-product-id="{{ $product->id }}">
                    <!-- Product Image -->
                    <div class="relative overflow-hidden bg-gray-200 aspect-[3/4] mb-6">
                        <img
                            src="{{ $product->image_path }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            loading="lazy"
                        >

                        <!-- Sold Out Overlay -->
                        @if($product->stock == 0)
                        <div class="absolute inset-0 bg-black/70 flex items-center justify-center">
                            <span class="text-white text-xs tracking-[0.3em] border border-white px-6 py-3">SOLD OUT</span>
                        </div>
                        @endif

                        <!-- Quick View Button -->
                        <div class="absolute inset-x-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                            <button
                                onclick="openQuickView({{ $product->id }})"
                                class="w-full bg-white/95 backdrop-blur-sm text-black text-xs tracking-[0.2em] py-4 hover:bg-black hover:text-white transition-colors duration-300"
                            >
                                QUICK VIEW
                            </button>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="flex flex-col items-center text-center">
                        <span class="text-xs tracking-[0.2em] text-gray-400 mb-3 uppercase">{{ $product->category }}</span>
                        <h3 class="text-sm tracking-wide mb-2 group-hover:underline underline-offset-4">{{ $product->name }}</h3>
                        <p class="text-sm font-light text-gray-800">
                            IDR {{ number_format($product->price, 0, ',', '.') }}
                        </p>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="w-full py-24 px-6 lg:px-8 bg-black text-white">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-16">
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-6 flex items-center justify-center border border-white/30 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xs tracking-[0.25em] mb-4">AUTHENTIC MATERIALS</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Only the finest fabrics and leathers sourced globally.</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-6 flex items-center justify-center border border-white/30 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xs tracking-[0.25em] mb-4">TIMELESS DESIGN</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Classic aesthetics that transcend seasons.</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-6 flex items-center justify-center border border-white/30 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h3 class="text-xs tracking-[0.25em] mb-4">WORLDWIDE DELIVERY</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Luxury packaging with global shipping.</p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer id="contact" class="w-full py-20 px-6 lg:px-8 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div class="md:col-span-2">
                    <a href="/" class="text-2xl font-serif tracking-[0.3em]">VESTRA</a>
                    <p class="text-gray-500 text-sm mt-4 leading-relaxed max-w-sm">
                        Luxury fashion for the discerning individual. Crafted with passion, worn with pride.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs tracking-[0.25em] mb-6">NAVIGATE</h4>
                    <ul class="space-y-3">
                        <li><a href="/" class="text-gray-400 text-sm hover:text-black transition-colors">Home</a></li>
                        <li><a href="#collection" class="text-gray-400 text-sm hover:text-black transition-colors">Collection</a></li>
                        <li><a href="#about" class="text-gray-400 text-sm hover:text-black transition-colors">About</a></li>
                        <li><a href="#contact" class="text-gray-400 text-sm hover:text-black transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs tracking-[0.25em] mb-6">POLICY</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 text-sm hover:text-black transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 text-sm hover:text-black transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 text-sm hover:text-black transition-colors">Shipping Info</a></li>
                        <li><a href="#" class="text-gray-400 text-sm hover:text-black transition-colors">Returns</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-xs tracking-wide text-gray-400">&copy; {{ date('Y') }} VESTRA. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="text-gray-400 hover:text-black transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.226 1.523 4.077 3.415 4.099-1.107 1.05-2.655 1.64-4.237 1.64-.26 0-.515-.015-.766-.04.56 1.745 2.193 3.02 4.098 3.06-1.503 1.18-3.396 1.883-5.454 1.883-.355 0-.704-.02-1.048-.05 1.954.98 4.298 1.62 6.803 1.62 8.162 0 12.628-6.41 12.628-11.98 0-.182 0-.363-.01-.543.868-.628 1.624-1.408 2.223-2.303"/></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-black transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- QUICK VIEW MODAL -->
    <div id="quickViewModal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeQuickView()"></div>

        <!-- Modal Content -->
        <div class="absolute inset-4 md:inset-10 lg:inset-20 bg-white overflow-hidden flex items-center justify-center">
            <button onclick="closeQuickView()" class="absolute top-6 right-6 z-10 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="w-full h-full grid grid-cols-1 md:grid-cols-2">
                <!-- Product Image -->
                <div class="bg-gray-100 overflow-hidden">
                    <img id="modalImage" src="" alt="" class="w-full h-full object-cover">
                </div>

                <!-- Product Info -->
                <div class="p-8 md:p-12 lg:p-16 flex flex-col justify-center overflow-y-auto">
                    <span id="modalCategory" class="text-xs tracking-[0.3em] text-gray-400 uppercase mb-4"></span>
                    <h2 id="modalName" class="text-3xl md:text-4xl font-serif tracking-[0.1em] mb-6"></h2>
                    <p id="modalPrice" class="text-2xl font-light mb-8"></p>

                    <div class="w-16 h-px bg-gray-200 mb-8"></div>

                    <p id="modalDescription" class="text-gray-600 text-sm leading-relaxed mb-8"></p>

                    <div id="modalStock" class="mb-8"></div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button class="flex-1 bg-black text-white text-xs tracking-[0.2em] py-4 hover:bg-gray-800 transition-colors">
                            ADD TO CART
                        </button>
                        <button class="flex-1 border border-black text-xs tracking-[0.2em] py-4 hover:bg-black hover:text-white transition-colors">
                            BUY NOW
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Data (Hidden) -->
    <script type="application/json" id="productsData">
        {!! json_encode($products) !!}
    </script>

    <script>
        const products = JSON.parse(document.getElementById('productsData').textContent);

        function openQuickView(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;

            document.getElementById('modalImage').src = product.image_path;
            document.getElementById('modalImage').alt = product.name;
            document.getElementById('modalCategory').textContent = product.category || '';
            document.getElementById('modalName').textContent = product.name;
            document.getElementById('modalPrice').textContent = 'IDR ' + new Intl.NumberFormat('id-ID').format(product.price);
            document.getElementById('modalDescription').textContent = product.description || 'No description available for this product.';

            const stockDiv = document.getElementById('modalStock');
            if (product.stock > 0) {
                stockDiv.innerHTML = '<span class="text-xs tracking-[0.2em] text-green-600">IN STOCK (' + product.stock + ' available)</span>';
            } else {
                stockDiv.innerHTML = '<span class="text-xs tracking-[0.2em] text-red-500">SOLD OUT</span>';
            }

            document.getElementById('quickViewModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeQuickView() {
            document.getElementById('quickViewModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeQuickView();
        });
    </script>
</body>
</html>
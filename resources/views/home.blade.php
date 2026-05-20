<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VESTA - Luxury Fashion Redefined</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-black antialiased">

    <!-- STICKY NAVBAR -->
    @include('layouts.header')

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
            <h1 class="text-white text-5xl md:text-7xl lg:text-8xl font-serif tracking-[0.2em] mb-4">VESTA</h1>
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

    <section id="about" class="w-full py-32 px-6 lg:px-8 bg-white">
        <div class="max-w-3xl mx-auto text-center">
            <span class="text-xs tracking-[0.3em] text-gray-400 uppercase">The House of Vesta</span>
            <h2 class="text-3xl md:text-4xl font-serif tracking-[0.1em] mt-6 mb-8">Crafting Excellence</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                Discover our curated collection of luxury fashion pieces, meticulously crafted with the finest materials and unwavering attention to detail.
            </p>
        </div>
    </section>

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
                        @if($product->total_stock == 0)
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
                        <span class="text-xs tracking-[0.2em] text-gray-400 mb-3 uppercase">{{ $product->category->name ?? '' }}</span>
                        <h3 class="text-sm tracking-wide mb-2 group-hover:underline underline-offset-4">
                            {{ $product->name }}
                        </h3>
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
    @include('layouts.footer')

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

                    <div id="modalStock" class="mb-4"></div>

                    <div class="flex items-center gap-4 mb-8">
                        <span class="text-xs tracking-[0.2em]">QTY</span>
                        <input type="number" id="modalQuantity" value="1" min="1" class="w-20 border border-gray-200 px-3 py-2 text-center">
                    </div>

                    <!-- Actions -->
                    <form id="modalAddToCartForm" action="" method="POST">
                        @csrf
                        <input type="hidden" name="quantity" id="modalQuantityInput" value="1">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit" class="flex-1 bg-black text-white text-xs tracking-[0.2em] py-4 hover:bg-gray-800 transition-colors">
                                ADD TO CART
                            </button>
                            <button type="button" onclick="buyNow()" class="flex-1 border border-black text-xs tracking-[0.2em] py-4 hover:bg-black hover:text-white transition-colors">
                                BUY NOW
                            </button>
                        </div>
                    </form>
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

        let currentModalProductId = null;

        function openQuickView(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;

            currentModalProductId = productId;

            document.getElementById('modalAddToCartForm').action = '/cart/add/' + productId;

            document.getElementById('modalImage').src = product.image_path;
            document.getElementById('modalImage').alt = product.name;
            document.getElementById('modalCategory').textContent = (product.category ? product.category.name : '') || '';
            
            document.getElementById('modalName').textContent = product.name;
            
            document.getElementById('modalPrice').textContent = 'IDR ' + new Intl.NumberFormat('id-ID').format(product.price);
            document.getElementById('modalDescription').textContent = product.description || 'No description available for this product.';
            document.getElementById('modalQuantity').value = '1';
            var totalStock = (product.variants || []).reduce(function(sum, v) { return sum + v.stock; }, 0);
            document.getElementById('modalQuantity').max = totalStock;

            const stockDiv = document.getElementById('modalStock');
            if (totalStock > 0) {
                stockDiv.innerHTML = '<span class="text-xs tracking-[0.2em] text-green-600">IN STOCK (' + totalStock + ' available)</span>';
            } else {
                stockDiv.innerHTML = '<span class="text-xs tracking-[0.2em] text-red-500">SOLD OUT</span>';
            }

            document.getElementById('quickViewModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function buyNow() {
            if (!currentModalProductId) return;
            let qty = document.getElementById('modalQuantity').value;
            let form = document.getElementById('modalAddToCartForm');
            form.action = '/direct-checkout/' + currentModalProductId;
            document.getElementById('modalQuantityInput').value = qty;
            form.submit();
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
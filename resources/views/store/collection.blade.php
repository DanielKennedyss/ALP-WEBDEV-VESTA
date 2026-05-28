@extends('base.base')

@section('content')
    <style>
        .filter-chip {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            border: 1px solid #d1d5db;
            transition: all 0.3s;
            cursor: pointer;
            user-select: none;
            background: #fff;
            color: #4b5563;
        }

        .filter-chip:hover {
            border-color: #000;
            color: #000;
        }

        .filter-chip.active {
            background: #000;
            color: #fff;
            border-color: #000;
        }

        .size-btn {
            width: 3rem;
            height: 3rem;
            border: 1px solid #d1d5db;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            cursor: pointer;
            background: #fff;
        }

        .size-btn:hover:not(.disabled) {
            border-color: #000;
        }

        .size-btn.active {
            background: #000;
            color: #fff;
            border-color: #000;
        }

        .size-btn.disabled {
            border-color: #e5e7eb;
            color: #d1d5db;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        .gender-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.625rem;
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border-radius: 2px;
        }

        .gender-male {
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .gender-female {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .gender-unisex {
            background: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }

        .product-card {
            display: flex;
            flex-direction: column;
            cursor: pointer;
            animation: fadeUp 0.6s ease forwards;
            opacity: 0;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 9999px;
            transition: all 0.2s;
            white-space: nowrap;
            color: #4b5563;
        }

        .filter-btn:hover {
            border-color: #9ca3af;
            color: #000;
        }

        .filter-btn.active {
            border-color: #000;
            color: #000;
            font-weight: 500;
        }

        .dropdown-content {
            position: absolute;
            top: 100%;
            margin-top: 0.5rem;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            z-index: 50;
            padding: 0.5rem 0;
            overflow: hidden;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.6rem 1.25rem;
            font-size: 0.8rem;
            color: #4b5563;
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background: #f9fafb;
            color: #000;
        }

        .dropdown-item.active {
            background: #f3f4f6;
            font-weight: 600;
            color: #000;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div class="pt-28 pb-24 px-4 lg:px-8 bg-stone-50 min-h-screen">
        <div class="max-w-[1400px] mx-auto">
            {{-- Page Header --}}
            <div class="text-center mb-12">
                <span class="text-xs tracking-[0.3em] text-gray-400 uppercase">Our Selection</span>
                <h1 class="text-4xl md:text-5xl font-serif tracking-[0.15em] mt-4">THE COLLECTION</h1>
                <div class="w-16 h-px bg-black mx-auto mt-6"></div>
            </div>

            {{-- Search Bar --}}
            <div class="max-w-2xl mx-auto mb-10">
                <form id="mainFilterForm" action="{{ route('collection') }}" method="GET" onsubmit="event.preventDefault();">
                    <div class="relative">
                        {{-- REVISI: Mengubah input search bar agar dimanipulasi real-time oleh Fuse.js --}}
                        <input type="text" id="searchInput" value="{{ request('search') }}"
                            placeholder="Search by name, description, or SKU..."
                            class="w-full border border-gray-300 pl-12 pr-4 py-3.5 text-sm focus:outline-none focus:border-black transition-colors bg-white">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <a href="{{ route('collection') }}" id="clearSearchBtn"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black" style="display: none;">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    </div>

                    {{-- Hidden inputs untuk sinkronisasi state filter URL herarki --}}
                    <input type="hidden" name="category" id="filterCategory" value="{{ request('category') }}">
                    <input type="hidden" name="gender" id="filterGender" value="{{ request('gender') }}">
                    <input type="hidden" name="size" id="filterSize" value="{{ request('size') }}">
                    <input type="hidden" name="sort" id="filterSort" value="{{ request('sort', 'newest') }}">
                </form>
            </div>

            <div class="flex-1 max-w-[1400px] mx-auto w-full">
                {{-- Horizontal Sticky Filter Bar --}}
                <div class="sticky top-20 z-40 bg-stone-50 border-y border-gray-200 py-3 mb-8 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-2 flex-wrap pb-1 md:pb-0">
                            <div class="flex items-center gap-2 mr-2">
                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                <span class="text-[10px] tracking-widest text-gray-500 font-medium uppercase">Filters</span>
                            </div>

                            {{-- Category Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false" class="filter-btn"
                                    :class="{ 'active': '{{ request('category') }}' }">
                                    Category <span
                                        class="text-black font-semibold ml-1">{{ request('category') ?: '' }}</span>
                                    <svg class="h-3 w-3 transition-transform" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" :class="{ 'rotate-180': open }">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" x-transition class="dropdown-content left-0" style="display: none;">
                                    <button type="button" onclick="setFilter('category','')"
                                        class="dropdown-item {{ !request('category') ? 'active' : '' }}">All
                                        Categories</button>
                                    @foreach ($categories as $cat)
                                        <button type="button" onclick="setFilter('category','{{ $cat }}')"
                                            class="dropdown-item {{ request('category') == $cat ? 'active' : '' }}">{{ strtoupper($cat) }}</button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Gender Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false" class="filter-btn"
                                    :class="{ 'active': '{{ request('gender') }}' }">
                                    Gender <span class="text-black font-semibold ml-1">{{ request('gender') ?: '' }}</span>
                                    <svg class="h-3 w-3 transition-transform" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" :class="{ 'rotate-180': open }">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" x-transition class="dropdown-content left-0" style="display: none;">
                                    <button type="button" onclick="setFilter('gender','')"
                                        class="dropdown-item {{ !request('gender') ? 'active' : '' }}">All
                                        Genders</button>
                                    @foreach ($genders as $g)
                                        <button type="button" onclick="setFilter('gender','{{ $g }}')"
                                            class="dropdown-item {{ request('gender') == $g ? 'active' : '' }}">{{ strtoupper($g) }}</button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Size Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false" class="filter-btn"
                                    :class="{ 'active': '{{ request('size') }}' }">
                                    Size <span class="text-black font-semibold ml-1">{{ request('size') ?: '' }}</span>
                                    <svg class="h-3 w-3 transition-transform" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" :class="{ 'rotate-180': open }">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" x-transition class="dropdown-content left-0 min-w-[280px]"
                                    style="display: none;">
                                    <div class="grid grid-cols-3 gap-2 p-4">
                                        <button type="button" onclick="setFilter('size','')"
                                            class="size-btn {{ !request('size') ? 'active' : '' }}">ALL</button>
                                        @foreach ($sizes as $s)
                                            <button type="button" onclick="setFilter('size','{{ $s }}')"
                                                class="size-btn {{ request('size') == $s ? 'active' : '' }}">{{ strtoupper($s) }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            @if (request('category') || request('gender') || request('size'))
                                <a href="{{ route('collection') }}"
                                    class="text-[10px] tracking-widest text-gray-500 hover:text-black transition-colors uppercase ml-2 underline underline-offset-4">Clear
                                    Filters</a>
                            @endif
                        </div>

                        <div class="flex items-center justify-between md:justify-end gap-6 shrink-0">
                            <span class="text-xs text-gray-500 whitespace-nowrap">
                                <span id="resultsCount" class="text-black font-medium">{{ $products->count() }}</span> Results
                            </span>

                            {{-- Sort Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false"
                                    class="text-xs tracking-wider font-medium flex items-center gap-1.5 hover:text-gray-600 transition-colors uppercase">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                    </svg>
                                    Sort By
                                </button>
                                <div x-show="open" x-transition class="dropdown-content right-0 left-auto"
                                    style="display: none;">
                                    <button type="button" onclick="setFilter('sort','newest')"
                                        class="dropdown-item {{ request('sort', 'newest') == 'newest' ? 'active' : '' }}">Newest
                                        First</button>
                                    <button type="button" onclick="setFilter('sort','price_low')"
                                        class="dropdown-item {{ request('sort') == 'price_low' ? 'active' : '' }}">Price:
                                        Low → High</button>
                                    <button type="button" onclick="setFilter('sort','price_high')"
                                        class="dropdown-item {{ request('sort') == 'price_high' ? 'active' : '' }}">Price:
                                        High → Low</button>
                                    <button type="button" onclick="setFilter('sort','name_asc')"
                                        class="dropdown-item {{ request('sort') == 'name_asc' ? 'active' : '' }}">Name: A →
                                        Z</button>
                                    <button type="button" onclick="setFilter('sort','name_desc')"
                                        class="dropdown-item {{ request('sort') == 'name_desc' ? 'active' : '' }}">Name: Z →
                                        A</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products Grid Wrapper --}}
                <div class="w-full">
                    {{-- REVISI: Kontainer diubah menjadi penampung kosong, data murni di-render via displayProducts() --}}
                    <div id="productGridContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                        </div>
                    
                    {{-- Empty State Placeholder --}}
                    <div id="emptySearchPlaceholder" class="text-center py-24" style="display: none;">
                        <svg class="mx-auto h-16 w-16 text-gray-300 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="text-sm tracking-widest text-gray-400 uppercase mb-4">No products found</p>
                        <a href="{{ route('collection') }}"
                            class="inline-block border border-black text-xs tracking-widest px-8 py-3 hover:bg-black hover:text-white transition-colors">CLEAR
                            FILTERS</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK VIEW MODAL --}}
    <div id="quickViewModal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeQuickView()"></div>
        <div class="absolute inset-4 md:inset-10 lg:inset-16 bg-white overflow-hidden flex items-center justify-center">
            <button onclick="closeQuickView()" class="absolute top-6 right-6 z-10 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="w-full h-full grid grid-cols-1 md:grid-cols-2">
                <div class="bg-gray-100 overflow-hidden">
                    <img id="modalImage" src="" alt="" class="w-full h-full object-cover">
                </div>

                <div class="p-8 md:p-12 lg:p-14 flex flex-col justify-center overflow-y-auto">
                    <span id="modalCategory" class="text-xs tracking-[0.3em] text-gray-400 uppercase mb-3"></span>
                    <h2 id="modalName" class="text-3xl md:text-4xl font-serif tracking-[0.1em] mb-4"></h2>

                    <div id="modalGender" class="mb-4"></div>

                    <p id="modalPrice" class="text-2xl font-light mb-6"></p>
                    <div class="w-16 h-px bg-gray-200 mb-6"></div>
                    <p id="modalDescription" class="text-gray-600 text-sm leading-relaxed mb-6"></p>

                    <div id="modalSizeSection" class="mb-6">
                        <span class="text-xs tracking-[0.2em] block mb-3">SELECT SIZE</span>
                        <div id="modalSizes" class="flex flex-wrap gap-2"></div>
                        <p id="sizeStockInfo" class="text-[10px] tracking-wider text-gray-400 mt-2"></p>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center gap-4">
                            <span class="text-xs tracking-[0.2em]">QTY</span>
                            <input type="number" id="modalQuantity" value="1" min="1" disabled
                                class="w-20 border border-gray-200 px-3 py-2 text-center disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                        </div>
                        <p id="qtyWarning" class="text-xs text-red-500 mt-2" style="display: none;">Purchase has reached the maximum limit!</p>
                    </div>

                    <form id="modalAddToCartForm" action="" method="POST">
                        @csrf
                        <input type="hidden" name="quantity" id="modalQuantityInput" value="1">
                        <input type="hidden" name="size" id="modalSizeInput" value="">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" id="addToCartBtn"
                                class="flex-1 bg-black text-white text-xs tracking-[0.2em] py-4 hover:bg-gray-800 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
                                ADD TO CART
                            </button>
                            <button type="button" onclick="buyNow()" id="buyNowBtn"
                                class="flex-1 border border-black text-xs tracking-[0.2em] py-4 hover:bg-black hover:text-white transition-colors disabled:border-gray-300 disabled:text-gray-300 disabled:cursor-not-allowed disabled:hover:bg-white">
                                BUY NOW
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Data Bridge --}}
    <script type="application/json" id="productsData">{!! json_encode($products) !!}</script>
    
    {{-- TAMBAHAN: Inject Fuse.js Core CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0"></script>

    <script>
        const products = JSON.parse(document.getElementById('productsData').textContent);
        let currentModalProductId = null;
        let selectedSize = null;

        // Filter system (Bawaan URL herarki tetap aktif penuh)
        function setFilter(name, value) {
            document.getElementById('filter' + name.charAt(0).toUpperCase() + name.slice(1)).value = value;
            document.getElementById('mainFilterForm').submit();
        }

        // ==========================================
        // REVISI CORE: LOGIKA FUZZY SEARCH (FUSE.JS)
        // ==========================================
        const gridContainer = document.getElementById('productGridContainer');
        const emptyPlaceholder = document.getElementById('emptySearchPlaceholder');
        const resultsCountEl = document.getElementById('resultsCount');
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');

        const fuseOptions = {
            keys: ['name', 'description', 'sku'], // Kolom target ejaan samar
            threshold: 0.4, // Parameter toleransi typo
            includeScore: true
        };

        const fuseInstance = new Fuse(products, fuseOptions);

        function displayProducts(productsList) {
            gridContainer.innerHTML = '';
            
            // Unpack objek jika data dilempar dari struktur output search Fuse.js (.item)
            const normalizedList = productsList.map(p => p.item ? p.item : p);
            resultsCountEl.innerText = normalizedList.length;

            if (normalizedList.length === 0) {
                gridContainer.style.display = 'none';
                emptyPlaceholder.style.display = 'block';
                return;
            }

            gridContainer.style.display = 'grid';
            emptyPlaceholder.style.display = 'none';

            normalizedList.forEach((product, index) => {
                const gc = (product.gender || '').toLowerCase();
                const genderClass = gc === 'male' ? 'gender-male' : (gc === 'female' ? 'gender-female' : 'gender-unisex');
                const genderSymbol = gc === 'male' ? '♂' : (gc === 'female' ? '♀' : '⚥');
                
                let soldOutOverlay = '';
                if (product.total_stock == 0) {
                    soldOutOverlay = `
                        <div class="absolute inset-0 bg-black/70 flex items-center justify-center">
                            <span class="text-white text-xs tracking-[0.3em] border border-white px-6 py-3">SOLD OUT</span>
                        </div>
                    `;
                }

                const productCardHtml = `
                    <article class="product-card group" style="animation-delay: ${index * 0.05}s; opacity: 1;">
                        <div class="relative overflow-hidden bg-gray-200 aspect-[3/4] mb-5 cursor-pointer" onclick="openQuickView(${product.id})">
                            <img src="${product.image_path}" alt="${product.name}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                            <div class="absolute top-3 left-3 z-10">
                                <span class="gender-badge ${genderClass}">
                                    ${genderSymbol} ${product.gender || 'Unisex'}
                                </span>
                            </div>
                            ${soldOutOverlay}
                            <div class="absolute inset-x-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                <button onclick="event.stopPropagation(); openQuickView(${product.id})" class="w-full bg-white/95 backdrop-blur-sm text-black text-xs tracking-[0.2em] py-4 hover:bg-black hover:text-white transition-colors duration-300">
                                    QUICK VIEW
                                </button>
                            </div>
                        </div>
                        <div class="relative w-full flex flex-col items-center text-center pt-2">
                            <span class="text-[10px] tracking-[0.2em] text-gray-400 mb-2 uppercase">${product.category ? product.category.name : ''}</span>
                            <h3 class="text-sm tracking-wide mb-1.5 group-hover:underline underline-offset-4">${product.name}</h3>
                            <p class="text-sm font-light text-gray-800">IDR ${new Intl.NumberFormat('id-ID').format(product.price)}</p>
                            <button onclick="toggleWishlist(event, ${JSON.stringify(product).replace(/"/g, '&quot;')})" class="absolute right-2 bottom-1.5 p-2 text-gray-400 hover:text-red-500 transition-colors z-20 origin-center" id="wishlist-heart-${product.id}" aria-label="Add to Wishlist">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 stroke-current transition-colors duration-300 origin-center" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>
                    </article>
                `;
                gridContainer.insertAdjacentHTML('beforeend', productCardHtml);
            });

            // Re-sync icon hati pasca-render ulang DOM
            if (typeof syncCardHearts === 'function') syncCardHearts();
        }

        // Jalankan render awal data produk pasca-load halaman
        displayProducts(products);

        // Jika URL membawa data parameter pencarian lama, aktifkan silang pembatalan
        if (searchInput.value.trim() !== '') {
            clearSearchBtn.style.display = 'block';
            const initialResults = fuseInstance.search(searchInput.value);
            displayProducts(initialResults);
        }

        // Tembak pencarian real-time fuzzy (Debounce manual diganti instant response murni)
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value;
            if (query.trim() === '') {
                clearSearchBtn.style.display = 'none';
                displayProducts(products);
                return;
            }
            clearSearchBtn.style.display = 'block';
            const filtered = fuseInstance.search(query);
            displayProducts(filtered);
        });

        // ==========================================

        // Quantity sync with max stock enforcement
        let prevQtyValue = 1;
        const qtyInput = document.getElementById('modalQuantity');

        qtyInput.addEventListener('focus', function() {
            prevQtyValue = parseInt(this.value) || 1;
        });

        qtyInput.addEventListener('input', function() {
            const max = parseInt(this.max) || 0;
            let val = parseInt(this.value) || 1;
            const warning = document.getElementById('qtyWarning');

            if (val > max) {
                val = max;
                this.value = max;
                warning.style.display = 'block';
            } else if (val >= max && prevQtyValue >= max) {
                warning.style.display = 'block';
            } else if (val < 1) {
                val = 1;
                this.value = 1;
                warning.style.display = 'none';
            } else {
                warning.style.display = 'none';
            }
            prevQtyValue = val;
            document.getElementById('modalQuantityInput').value = val;
        });

        qtyInput.addEventListener('change', function() {
            const max = parseInt(this.max) || 0;
            let val = parseInt(this.value) || 1;
            const warning = document.getElementById('qtyWarning');

            if (val >= max) {
                val = max;
                this.value = max;
                warning.style.display = 'block';
            } else if (val < 1) {
                val = 1;
                this.value = 1;
                warning.style.display = 'none';
            } else {
                warning.style.display = 'none';
            }
            prevQtyValue = val;
            document.getElementById('modalQuantityInput').value = val;
        });

        qtyInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowUp') {
                const max = parseInt(this.max) || 0;
                const val = parseInt(this.value) || 1;
                if (val >= max) {
                    e.preventDefault();
                    this.value = max;
                    document.getElementById('qtyWarning').style.display = 'block';
                    document.getElementById('modalQuantityInput').value = max;
                }
            } else if (e.key === 'ArrowDown') {
                document.getElementById('qtyWarning').style.display = 'none';
            }
        });

        function openQuickView(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;
            currentModalProductId = productId;
            selectedSize = null;

            document.getElementById('modalAddToCartForm').action = '/cart/add/' + productId;
            document.getElementById('modalImage').src = product.image_path;
            document.getElementById('modalImage').alt = product.name;
            document.getElementById('modalCategory').textContent = product.category ? product.category.name : '';
            document.getElementById('modalName').textContent = product.name;
            document.getElementById('modalPrice').textContent = 'IDR ' + new Intl.NumberFormat('id-ID').format(product.price);
            document.getElementById('modalDescription').textContent = product.description || 'No description available.';
            document.getElementById('modalQuantity').value = '1';
            document.getElementById('modalQuantityInput').value = '1';
            document.getElementById('modalSizeInput').value = '';

            const genderDiv = document.getElementById('modalGender');
            const g = (product.gender || '').toLowerCase();
            const symbol = g === 'male' ? '♂' : g === 'female' ? '♀' : '⚥';
            const cls = g === 'male' ? 'gender-male' : g === 'female' ? 'gender-female' : 'gender-unisex';
            genderDiv.innerHTML = '<span class="gender-badge ' + cls + '">' + symbol + ' ' + (product.gender || 'Unisex') + '</span>';

            const sizesDiv = document.getElementById('modalSizes');
            const sizeSection = document.getElementById('modalSizeSection');
            const variants = product.variants || [];

            if (variants.length > 0) {
                sizeSection.style.display = 'block';
                sizesDiv.innerHTML = '';
                variants.forEach(v => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'size-btn' + (v.stock <= 0 ? ' disabled' : '');
                    btn.textContent = v.size_label;
                    btn.dataset.size = v.size_label;
                    btn.dataset.stock = v.stock;
                    if (v.stock > 0) {
                        btn.onclick = function() {
                            selectSize(this, v.size_label, v.stock);
                        };
                    }
                    sizesDiv.appendChild(btn);
                });
            } else {
                sizeSection.style.display = 'none';
            }

            const totalStock = variants.reduce((s, v) => s + v.stock, 0);
            document.getElementById('modalQuantity').max = totalStock;
            document.getElementById('qtyWarning').style.display = 'none';

            const qtyEl = document.getElementById('modalQuantity');
            if (variants.length > 0) {
                qtyEl.disabled = true;
            } else {
                qtyEl.disabled = totalStock <= 0;
            }

            document.getElementById('sizeStockInfo').textContent = variants.length > 0 ? 'Please select a size' : '';
            updateActionButtons(variants.length > 0 ? false : totalStock > 0);

            document.getElementById('quickViewModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function selectSize(btn, sizeLabel, stock) {
            selectedSize = sizeLabel;
            document.querySelectorAll('#modalSizes .size-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('modalSizeInput').value = sizeLabel;
            document.getElementById('modalQuantity').max = stock;
            document.getElementById('modalQuantity').value = '1';
            document.getElementById('modalQuantity').disabled = false;
            document.getElementById('modalQuantityInput').value = '1';
            document.getElementById('sizeStockInfo').textContent = stock + ' available in size ' + sizeLabel;
            document.getElementById('qtyWarning').style.display = 'none';
            updateActionButtons(true);
        }

        function updateActionButtons(enabled) {
            document.getElementById('addToCartBtn').disabled = !enabled;
            document.getElementById('buyNowBtn').disabled = !enabled;
        }

        function buyNow() {
            if (!currentModalProductId) return;
            document.getElementById('modalQuantityInput').value = document.getElementById('modalQuantity').value;
            const form = document.getElementById('modalAddToCartForm');
            form.action = '/direct-checkout/' + currentModalProductId;
            form.submit();
        }

        function closeQuickView() {
            document.getElementById('quickViewModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function syncCardHearts() {
            document.querySelectorAll('[id^="wishlist-heart-"]').forEach(btn => {
                btn.classList.add('text-gray-400', 'hover:text-red-500');
                btn.classList.remove('text-red-500');
                const svg = btn.querySelector('svg');
                if (svg) {
                    svg.setAttribute('fill', 'none');
                    svg.style.fill = 'none';
                }
            });

            if (typeof wishlistItems !== 'undefined' && wishlistItems.length > 0) {
                wishlistItems.forEach(item => {
                    const btn = document.getElementById('wishlist-heart-' + item.id);
                    if (btn) {
                        btn.classList.remove('text-gray-400', 'hover:text-red-500');
                        btn.classList.add('text-red-500');
                        const svg = btn.querySelector('svg');
                        if (svg) {
                            svg.setAttribute('fill', '#ef4444');
                            svg.style.fill = '#ef4444';
                        }
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            syncCardHearts();

            const urlParams = new URLSearchParams(window.location.search);
            const quickviewId = urlParams.get('quickview');
            if (quickviewId) {
                openQuickView(parseInt(quickviewId));
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + (
                    window.location.search.replace(/quickview=\d+&?/, '').replace(/\?$/, ''));
                window.history.replaceState({ path: newUrl }, '', newUrl);
            }
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeQuickView();
        });
    </script>
@endsection
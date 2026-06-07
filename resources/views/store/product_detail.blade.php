@extends('base.base')

@section('content')
<style>
    /* Styling for smooth scroll to anchor */
    html {
        scroll-behavior: smooth;
    }
    
    /* Menghilangkan panah spinner pada input number */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }

    /* Custom Scrollbar for dropdowns */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d4d4d8;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a1a1aa;
    }
</style>

<div class="bg-white min-h-screen pt-20 pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="mb-5 text-[10px] tracking-wider text-gray-400 font-sans uppercase">
            <a href="{{ route('home') }}" class="hover:text-black transition duration-300">Home</a> /
            <a href="{{ route('collections.index') }}" class="hover:text-black transition duration-300">Collection</a> /
            <span class="text-black font-medium">{{ $product->name }}</span>
        </nav>

        <!-- Layout Utama: Grid 2 Kolom -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-stretch">
            
            <!-- ================= KOLOM KIRI (VISUAL KOTAK, BERSIH TANPA WIDGET) ================= -->
            <div class="lg:col-span-6">
                <div class="w-full lg:sticky lg:top-28">
                    <img src="{{ \Illuminate\Support\Str::startsWith($product->image_path, ['http://', 'https://']) ? $product->image_path : asset('product_image/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-auto object-contain">
                </div>
            </div>

            <!-- ================= KOLOM KANAN (DETAIL & AKSI) ================= -->
            <div class="lg:col-span-6 flex flex-col justify-start pt-2 lg:pt-0 lg:pr-4">
                
                <h2 class="text-xs font-sans text-gray-500 tracking-[0.2em] uppercase mb-3">{{ $product->category->name ?? 'Category' }}</h2>
                
                <!-- Judul -->
                <h1 class="text-3xl lg:text-4xl font-serif font-medium text-black mb-3 leading-tight">{{ $product->name }}</h1>

                <!-- Rating Summary Link -->
                @if($product->reviews_count > 0)
                    <a href="#customer-reviews" class="inline-flex items-center gap-2 mb-6 group cursor-pointer w-max">
                        <div class="flex gap-0.5 text-black">
                            @php $avg = round($product->reviews_avg_rating ?? 0); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $i <= $avg ? 'fill-current text-black' : 'text-gray-200' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        <span class="text-sm font-sans text-gray-600 group-hover:text-black transition-colors underline decoration-gray-300 underline-offset-4">{{ number_format($product->reviews_avg_rating, 1) }} ({{ $product->reviews_count }} Reviews)</span>
                    </a>
                @endif

                <!-- Harga -->
                <p class="text-xl font-sans text-black mb-5">IDR {{ number_format($product->price, 0, ',', '.') }}</p>

                <!-- Deskripsi -->
                <div class="text-gray-600 font-sans leading-relaxed mb-6 text-sm">
                    {{ $product->description ?? 'No description available for this product.' }}
                </div>

                <!-- Formulir ATC / Buy Now -->
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-full">
                    @csrf
                    
                    <!-- Pilihan Ukuran -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-sans font-medium text-black text-sm tracking-wide">Select Size</h3>
                            <button type="button" class="text-xs text-gray-500 underline hover:text-black transition tracking-wide">Size Guide</button>
                        </div>
                        <div class="flex flex-row w-full gap-3 overflow-x-auto custom-scrollbar pb-2">
                            @forelse($product->variants as $variant)
                                <div class="flex-1 min-w-[60px]">
                                    <input type="radio" name="size" id="size_{{ $variant->size_label }}" value="{{ $variant->size_label }}" class="peer hidden" required {{ $variant->stock <= 0 ? 'disabled' : '' }}>
                                    <label for="size_{{ $variant->size_label }}" 
                                           class="flex items-center justify-center w-full py-3.5 border-[1px] border-gray-200 font-sans text-sm cursor-pointer transition-all duration-200 
                                                  peer-checked:border-black peer-checked:bg-black peer-checked:text-white 
                                                  hover:border-black
                                                  {{ $variant->stock <= 0 ? 'opacity-30 cursor-not-allowed bg-gray-50 text-gray-400 border-gray-100 hover:border-gray-100' : '' }}">
                                        {{ $variant->size_label }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">One Size</p>
                                <input type="hidden" name="size" value="All Size">
                            @endforelse
                        </div>
                    </div>

                    <!-- Kuantitas & Tombol CTA -->
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center border border-gray-200 w-max mb-1">
                            <button type="button" class="px-4 py-2 text-gray-500 hover:text-black hover:bg-gray-50 transition" onclick="document.getElementById('qty').value = Math.max(1, parseInt(document.getElementById('qty').value) - 1)">-</button>
                            <input type="number" name="quantity" id="qty" value="1" min="1" class="w-10 text-center font-sans focus:outline-none border-none p-0 text-black text-sm">
                            <button type="button" class="px-4 py-2 text-gray-500 hover:text-black hover:bg-gray-50 transition" onclick="document.getElementById('qty').value = parseInt(document.getElementById('qty').value) + 1">+</button>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-black text-white py-3.5 font-sans font-medium uppercase tracking-[0.15em] text-xs hover:bg-gray-800 transition duration-300">
                                ADD TO CART
                            </button>
                            
                            <button type="submit" formaction="{{ route('direct.checkout', $product->id) }}" class="flex-1 bg-white text-black border border-black py-3.5 font-sans font-medium uppercase tracking-[0.15em] text-xs hover:bg-black hover:text-white transition duration-300">
                                BUY NOW
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Relocated Review Summary Board (Vesta x Nike style) -->
                @if($product->reviews_count > 0)
                    <div class="mt-10 p-6 bg-stone-50 border border-gray-150 rounded-lg">
                        <div class="flex flex-col sm:flex-row items-center justify-between border-b border-gray-200 pb-4 mb-4 gap-4">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl font-serif font-semibold text-black">{{ number_format($product->reviews_avg_rating ?? 0, 1) }}</span>
                                <div>
                                    <div class="flex gap-0.5 text-black">
                                        @php $avg = round($product->reviews_avg_rating ?? 0); @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 {{ $i <= $avg ? 'fill-current text-black' : 'text-gray-200' }}" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-[10px] tracking-wider text-gray-500 uppercase font-sans">Based on {{ $product->reviews_count }} Reviews</span>
                                </div>
                            </div>
                            <a href="#customer-reviews" class="text-[10px] font-sans font-semibold text-black uppercase tracking-[0.15em] border border-black px-4 py-2 hover:bg-black hover:text-white transition duration-300">
                                All Reviews
                            </a>
                        </div>

                        <!-- Rating Breakdown -->
                        <div class="mb-5">
                            @php
                                $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                                foreach($product->reviews as $r) {
                                    $rating = round($r->rating);
                                    if(isset($counts[$rating])) $counts[$rating]++;
                                }
                            @endphp
                            <div class="space-y-2.5">
                                @for($star = 5; $star >= 1; $star--)
                                    @php 
                                        $count = $counts[$star];
                                        $pct = $product->reviews_count > 0 ? ($count / $product->reviews_count * 100) : 0;
                                    @endphp
                                    <div class="flex items-center gap-4 text-xs font-sans group cursor-pointer" onclick="filterByRating({{ $star }})">
                                        <span class="w-12 text-gray-500 group-hover:text-black transition-colors">{{ $star }} Star</span>
                                        <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-black transition-all duration-700 ease-out" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="w-8 text-right text-gray-400 font-mono">{{ $count }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Customer Feedback Tags -->
                        <div class="pt-4 border-t border-gray-200">
                            <span class="text-xs tracking-[0.05em] font-sans font-semibold text-gray-850 block mb-3">Popular tags:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="px-2.5 py-1 bg-white border border-gray-200 rounded text-[10px] font-sans text-gray-600 shadow-sm">Comfortable material</span>
                                <span class="px-2.5 py-1 bg-white border border-gray-200 rounded text-[10px] font-sans text-gray-600 shadow-sm">Premium quality</span>
                                <span class="px-2.5 py-1 bg-white border border-gray-200 rounded text-[10px] font-sans text-gray-600 shadow-sm">Accurate sizing</span>
                                <span class="px-2.5 py-1 bg-white border border-gray-200 rounded text-[10px] font-sans text-gray-600 shadow-sm">Elegant design</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ================= FULL SECTION: CUSTOMER REVIEWS ================= -->
<div id="customer-reviews" class="w-full bg-white border-t border-gray-200 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Sorting & Filtering Controls / Header -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 pb-4 border-b border-gray-100 gap-4">
            <h2 class="text-2xl lg:text-3xl font-serif text-black tracking-wide" id="review-list-title">All Reviews ({{ $product->reviews_count }})</h2>
                
                <div class="flex items-center gap-4 text-xs font-sans">
                    <!-- Filter Select -->
                    <div class="relative">
                        <select id="review-filter" onchange="applyFiltersAndSort()" class="appearance-none bg-white border border-gray-200 text-gray-700 py-2.5 pl-4 pr-10 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black cursor-pointer">
                            <option value="all">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="h-3 w-3 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>

                    <!-- Sort Select -->
                    <div class="relative">
                        <select id="review-sort" onchange="applyFiltersAndSort()" class="appearance-none bg-white border border-gray-200 text-gray-700 py-2.5 pl-4 pr-10 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black cursor-pointer">
                            <option value="recent">Most Recent</option>
                            <option value="highest">Highest Rating</option>
                            <option value="lowest">Lowest Rating</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="h-3 w-3 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Cards Grid (Scrollable) -->
            <div class="max-h-[600px] overflow-y-auto custom-scrollbar pr-2 mb-12">
                <div id="reviews-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Reviews injected by JS -->
                </div>
            </div>

            <!-- Pagination -->
            <div id="pagination-container" class="flex justify-center items-center gap-2">
                <!-- Pagination injected by JS -->
            </div>

        </div>
    </div>
</div>

<!-- ================= JAVASCRIPT FOR REVIEWS ================= -->
@php
    $reviewsData = $product->reviews->map(function($r) {
        return [
            'id' => $r->id,
            'user_name' => $r->user->name ?? 'Anonymous',
            'rating' => (int) $r->rating,
            'date' => $r->created_at->format('M j, Y'),
            'timestamp' => $r->created_at->timestamp,
            'comment' => $r->comment,
            'verified' => !empty($r->transaction_id)
        ];
    })->values()->all();
@endphp
<script>
    // Load reviews data from PHP
    const rawReviews = {!! json_encode($reviewsData) !!};

    // State variables
    let filteredReviews = [...rawReviews];
    let currentPage = 1;
    const itemsPerPage = 50;

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        applyFiltersAndSort();
    });

    function filterByRating(star) {
        const filterSelect = document.getElementById('review-filter');
        filterSelect.value = star.toString();
        applyFiltersAndSort();
        document.getElementById('customer-reviews').scrollIntoView({ behavior: 'smooth' });
    }

    function applyFiltersAndSort() {
        const filterValue = document.getElementById('review-filter').value;
        const sortValue = document.getElementById('review-sort').value;

        // Apply Filter
        if (filterValue === 'all') {
            filteredReviews = [...rawReviews];
        } else {
            const star = parseInt(filterValue);
            filteredReviews = rawReviews.filter(r => r.rating === star);
        }

        // Apply Sort
        if (sortValue === 'recent') {
            filteredReviews.sort((a, b) => b.timestamp - a.timestamp);
        } else if (sortValue === 'highest') {
            filteredReviews.sort((a, b) => b.rating - a.rating || b.timestamp - a.timestamp);
        } else if (sortValue === 'lowest') {
            filteredReviews.sort((a, b) => a.rating - b.rating || b.timestamp - a.timestamp);
        }

        // Reset page and render
        currentPage = 1;
        updateTitle();
        renderReviews();
        renderPagination();
    }

    function updateTitle() {
        const title = document.getElementById('review-list-title');
        const filterValue = document.getElementById('review-filter').value;
        const count = filteredReviews.length;
        
        if (filterValue === 'all') {
            title.textContent = `All Reviews (${count})`;
        } else {
            title.textContent = `${filterValue}-Star Reviews (${count})`;
        }
    }

    function renderReviews() {
        const container = document.getElementById('reviews-container');
        container.innerHTML = '';

        if (filteredReviews.length === 0) {
            container.innerHTML = `
                <div class="col-span-1 md:col-span-2 py-16 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <p class="text-gray-500 font-sans">No reviews found matching your criteria.</p>
                    <button onclick="document.getElementById('review-filter').value='all';applyFiltersAndSort();" class="mt-4 text-xs font-medium text-black underline">Clear Filters</button>
                </div>
            `;
            return;
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageItems = filteredReviews.slice(startIndex, endIndex);

        pageItems.forEach((review, index) => {
            const initials = review.user_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
            
            // Build stars html
            let starsHtml = '';
            for(let i=1; i<=5; i++) {
                starsHtml += `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ${i <= review.rating ? 'fill-current text-black' : 'text-gray-200'}" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                `;
            }

            const verifiedBadgeHtml = review.verified ? `
                <span class="inline-flex items-center gap-1 text-[9px] tracking-wider text-green-700 uppercase bg-green-50 px-2 py-0.5 rounded-sm border border-green-100 font-medium ml-3">
                    <svg class="h-2.5 w-2.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Verified Purchase
                </span>
            ` : '';

            // Card HTML
            const cardHtml = `
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.08)] transition-shadow duration-300 flex flex-col h-full animate-fade-in" style="animation-delay: ${index * 0.05}s">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold flex items-center justify-center tracking-widest border border-gray-200 flex-shrink-0">
                                ${initials}
                            </div>
                            <div>
                                <div class="flex items-center">
                                    <span class="text-sm font-semibold text-black">${review.user_name}</span>
                                </div>
                                <div class="flex gap-0.5 mt-1 items-center">
                                    ${starsHtml}
                                    ${verifiedBadgeHtml}
                                </div>
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-400 font-mono tracking-wider flex-shrink-0 ml-4">${review.date}</span>
                    </div>
                    <p class="text-sm text-gray-600 font-sans leading-relaxed mt-2 flex-grow">
                        ${review.comment}
                    </p>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', cardHtml);
        });
    }

    function renderPagination() {
        const container = document.getElementById('pagination-container');
        container.innerHTML = '';
        
        const totalPages = Math.ceil(filteredReviews.length / itemsPerPage);
        if (totalPages <= 1) return;

        // Prev Button
        container.insertAdjacentHTML('beforeend', `
            <button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} 
                class="px-4 py-2 border border-gray-200 rounded-md text-xs font-medium text-gray-600 hover:text-black hover:border-black disabled:opacity-50 disabled:cursor-not-allowed transition-colors mr-2">
                Previous
            </button>
        `);

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                const isActive = i === currentPage;
                container.insertAdjacentHTML('beforeend', `
                    <button onclick="changePage(${i})" 
                        class="w-8 h-8 flex items-center justify-center border rounded-md text-xs font-medium transition-colors 
                        ${isActive ? 'bg-black text-white border-black' : 'bg-white text-gray-600 border-gray-200 hover:border-black hover:text-black'}">
                        ${i}
                    </button>
                `);
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                container.insertAdjacentHTML('beforeend', `<span class="px-1 text-gray-400">...</span>`);
            }
        }

        // Next Button
        container.insertAdjacentHTML('beforeend', `
            <button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} 
                class="px-4 py-2 border border-gray-200 rounded-md text-xs font-medium text-gray-600 hover:text-black hover:border-black disabled:opacity-50 disabled:cursor-not-allowed transition-colors ml-2">
                Next
            </button>
        `);
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredReviews.length / itemsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            renderReviews();
            renderPagination();
            // Scroll back to top of review list slightly above to see the controls
            document.getElementById('review-list-title').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
        opacity: 0;
    }
</style>
@endsection

@extends('base.base')

@section('content')
<div class="pt-32 pb-24 px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Section Title -->
        <div class="text-center mb-10">
            <span class="text-xs tracking-[0.3em] text-gray-400 uppercase">Our Selection</span>
            <h2 class="text-4xl md:text-5xl font-serif tracking-[0.15em] mt-4">THE COLLECTION</h2>
            <div class="w-16 h-px bg-black mx-auto mt-8"></div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="mb-12 flex justify-center">
            <form action="{{ route('collection') }}" method="GET" class="flex flex-col md:flex-row gap-4 w-full max-w-3xl">
                <input type="text" name="search" placeholder="Search product..." value="{{ request('search') }}" 
                       class="flex-1 border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:border-black">
                
                <select name="category" class="border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:border-black">
                    <option value="">All Categories</option>
                    <option value="Outerwear" {{ request('category') == 'Outerwear' ? 'selected' : '' }}>Outerwear</option>
                    <option value="Dress" {{ request('category') == 'Dress' ? 'selected' : '' }}>Dress</option>
                    <option value="Pants" {{ request('category') == 'Pants' ? 'selected' : '' }}>Pants</option>
                    <option value="Clothing" {{ request('category') == 'Clothing' ? 'selected' : '' }}>Clothing</option>
                    <option value="Accessories" {{ request('category') == 'Accessories' ? 'selected' : '' }}>Accessories</option>
                </select>
                
                <button type="submit" class="bg-black text-white px-8 py-2 text-sm tracking-widest hover:bg-gray-800 transition">FILTER</button>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            @foreach($products as $product)
            <article class="group flex flex-col cursor-pointer">
                <!-- Product Image -->
                <div class="relative overflow-hidden bg-gray-200 aspect-[3/4] mb-6">
                    <img
                        src="{{ $product->image_path }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        loading="lazy"
                    >

                    @if($product->total_stock == 0)
                    <div class="absolute inset-0 bg-black/70 flex items-center justify-center">
                        <span class="text-white text-xs tracking-[0.3em] border border-white px-6 py-3">SOLD OUT</span>
                    </div>
                    @endif

                    <div class="absolute inset-x-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                        <button onclick="openQuickView({{ $product->id }})" class="w-full bg-white/95 backdrop-blur-sm text-black text-xs tracking-[0.2em] py-4 hover:bg-black hover:text-white transition-colors duration-300">
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
</div>

<!-- QUICK VIEW MODAL -->
<div id="quickViewModal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeQuickView()"></div>
    <div class="absolute inset-4 md:inset-10 lg:inset-20 bg-white overflow-hidden flex items-center justify-center">
        <button onclick="closeQuickView()" class="absolute top-6 right-6 z-10 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="w-full h-full grid grid-cols-1 md:grid-cols-2">
            <div class="bg-gray-100 overflow-hidden">
                <img id="modalImage" src="" alt="" class="w-full h-full object-cover">
            </div>

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
    document.getElementById('modalDescription').textContent = product.description || 'No description available.';
    document.getElementById('modalQuantity').value = '1';
    var totalStock = (product.variants || []).reduce(function(sum, v) { return sum + v.stock; }, 0);
    document.getElementById('modalQuantity').max = totalStock;
    const stockDiv = document.getElementById('modalStock');
    stockDiv.innerHTML = totalStock > 0
        ? '<span class="text-xs tracking-[0.2em] text-green-600">IN STOCK (' + totalStock + ' available)</span>'
        : '<span class="text-xs tracking-[0.2em] text-red-500">SOLD OUT</span>';
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

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeQuickView();
});
</script>
@endsection
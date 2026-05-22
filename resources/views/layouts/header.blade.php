<nav
    class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand -->
            <a href="/" class="text-xl font-serif tracking-[0.3em]">VESTA</a>
            <!-- Navigation Links (desktop) -->
            <div class="hidden md:flex items-center gap-10">
                <a href="/" class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">HOME</a>
                <a href="{{ route('collection') }}"
                    class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">COLLECTION</a>
                <a href="{{ route('about') }}"
                    class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">ABOUT</a>
                <a href="{{ route('contact') }}"
                    class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors">CONTACT</a>
            </div>
            <!-- Right Icons -->
            <div class="flex items-center gap-6">
                <!-- Wishlist Button -->
                <button onclick="openWishlistModal()" class="hover:text-gray-600 transition-colors relative"
                    aria-label="Wishlist" id="wishlist-trigger-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" id="navbar-wishlist-icon"
                        class="h-5 w-5 stroke-current transition-all duration-300 origin-center" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span id="wishlist-count"
                        class="absolute -top-2 -right-2 bg-black text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
                </button>
                @auth
                    @php
                        $fullName = auth()->user()->name;
                        $nameParts = explode(' ', trim($fullName));
                        $firstName = $nameParts[0] ?? 'User';
                        $initial = strtoupper(substr($firstName, 0, 1));
                    @endphp
                    <a href="{{ route('profile') }}" class="flex items-center group transition-all duration-300">
                        <span
                            class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center text-xs font-semibold transition-transform duration-300 group-hover:scale-105">
                            {{ $initial }}
                        </span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="text-xs tracking-[0.2em] hover:text-gray-600 transition-colors border border-current px-4 py-2">
                        LOGIN
                    </a>
                @endauth
                <a href="{{ route('cart.view') }}" class="hover:text-gray-600 transition-colors relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    @php $cartCount = count(session('cart', [])); @endphp
                    @if ($cartCount > 0)
                        <span
                            class="absolute -top-2 -right-2 bg-black text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</nav>
<!-- Wishlist Drawer -->
<div id="wishlistDrawer" class="fixed inset-0 z-[150] hidden" aria-hidden="true">
    <!-- Backdrop -->
    <div id="wishlistBackdrop"
        class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"
        onclick="closeWishlistModal()"></div>

    <!-- Drawer Content -->
    <div id="wishlistContent"
        class="absolute inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300">
        <!-- Drawer Header -->
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm tracking-[0.2em] font-serif uppercase">YOUR WISHLIST</h3>
            <button onclick="closeWishlistModal()" class="hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Drawer Body -->
        <div class="flex-1 overflow-y-auto p-6" id="wishlistItemsContainer">
            <!-- Wishlist items dynamically loaded -->
        </div>

        <!-- Drawer Footer -->
        <div class="p-6 border-t border-gray-100 bg-gray-50 flex flex-col gap-3">
            <a href="{{ route('collection') }}"
                class="w-full bg-black text-white text-center text-xs tracking-[0.2em] py-4 hover:bg-gray-800 transition-colors"
                onclick="closeWishlistModal()">
                CONTINUE SHOPPING
            </a>
        </div>
    </div>
</div>
<style>
    .flying-heart {
        position: fixed;
        z-index: 9999;
        pointer-events: none;
        color: #ef4444;
        fill: #ef4444;
        transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1);
    }
</style>
<script>
    // Wishlist global JS
    const IS_AUTHENTICATED = @json(auth()->check());
    let wishlistItems = [];

    // Branded VESTA dynamic toast notifications
    function showVestaToast(message, type = 'error') {
        let existing = document.getElementById('globalToast');
        if (existing) existing.remove();
        
        let toast = document.createElement('div');
        toast.className = `vesta-global-toast toast-${type}`;
        toast.id = 'globalToast';
        toast.innerHTML = `
            <div>
                <div class="toast-brand">VESTA</div>
                <div class="toast-msg">${message}</div>
            </div>
            <span class="toast-close" onclick="this.parentElement.style.animation='vestaToastOut 0.4s ease forwards';setTimeout(()=>this.parentElement.remove(),400)">✕</span>
        `;
        document.body.appendChild(toast);
        setTimeout(function() {
            if (toast) {
                toast.style.animation = 'vestaToastOut 0.4s ease forwards';
                setTimeout(function() { toast.remove(); }, 400);
            }
        }, 5000);
    }
    // Helper to get csrf token
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            '{{ csrf_token() }}';
    }
    // Load wishlist
    async function initWishlist() {
        if (IS_AUTHENTICATED) {
            // Logged in: Sync local items first, then load from database
            let localIds = getLocalWishlistProductIds();
            if (localIds.length > 0) {
                try {
                    await syncLocalWishlistToDb(localIds);
                    localStorage.removeItem('vesta_wishlist'); // clean up local storage once synced
                } catch (e) {
                    console.error('Error syncing wishlist:', e);
                }
            }
            await fetchWishlistFromDb();
        }

        // Sync any heart buttons on the collection page if active
        if (typeof syncCardHearts === 'function') {
            syncCardHearts();
        }
    }

    function getLocalWishlistProductIds() {
        const local = JSON.parse(localStorage.getItem('vesta_wishlist') || '[]');
        return local.map(item => item.id);
    }
    async function syncLocalWishlistToDb(productIds) {
        const res = await fetch('{{ route('wishlist.sync') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                product_ids: productIds
            })
        });
        return res.json();
    }
    async function fetchWishlistFromDb() {
        try {
            const res = await fetch('{{ route('wishlist.items') }}');
            wishlistItems = await res.json();
            updateWishlistCountUI();
        } catch (e) {
            console.error('Error fetching database wishlist:', e);
        }
    }

    function updateWishlistCountUI() {
        const countBadge = document.getElementById('wishlist-count');
        const navbarIcon = document.getElementById('navbar-wishlist-icon');
        const count = wishlistItems.length;
        if (countBadge) {
            if (count > 0) {
                countBadge.textContent = count;
                countBadge.classList.remove('hidden');
                navbarIcon.classList.add('text-red-500');
                navbarIcon.setAttribute('fill', '#ef4444');
                navbarIcon.style.fill = '#ef4444';
            } else {
                countBadge.classList.add('hidden');
                navbarIcon.classList.remove('text-red-500');
                navbarIcon.setAttribute('fill', 'none');
                navbarIcon.style.fill = 'none';
            }
        }
    }
    // Toggle logic called from collection cards
    async function toggleWishlist(event, product) {
        if (event) {
            event.stopPropagation();
        }
        if (!IS_AUTHENTICATED) {
            showVestaToast('Please login or sign up first to add items to your wishlist.', 'error');
            return;
        }
        const heartBtn = document.getElementById('wishlist-heart-' + product.id);
        const heartSvg = heartBtn ? heartBtn.querySelector('svg') : null;
        const navIcon = document.getElementById('navbar-wishlist-icon');
        const isCurrentlyWishlisted = wishlistItems.some(item => item.id === product.id);
        
        // Persist to DB
        try {
            // Instantly update UI for snappy feeling
            if (!isCurrentlyWishlisted) {
                if (heartBtn) {
                    heartBtn.classList.remove('text-gray-400', 'hover:text-red-500');
                    heartBtn.classList.add('text-red-500');
                    if (heartSvg) {
                        heartSvg.setAttribute('fill', '#ef4444');
                        heartSvg.style.fill = '#ef4444';
                    }
                }
                if (heartBtn && navIcon) {
                    animateHeartToNavbar(heartBtn, navIcon);
                }
            } else {
                if (heartBtn) {
                    heartBtn.classList.add('text-gray-400', 'hover:text-red-500');
                    heartBtn.classList.remove('text-red-500');
                    if (heartSvg) {
                        heartSvg.setAttribute('fill', 'none');
                        heartSvg.style.fill = 'none';
                    }
                }
            }
            const res = await fetch('/wishlist/toggle/' + product.id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });
            const data = await res.json();

            // Fetch latest wishlist items to sync state perfectly
            await fetchWishlistFromDb();

            if (typeof syncCardHearts === 'function') {
                syncCardHearts();
            }
        } catch (e) {
            console.error('Error toggling DB wishlist:', e);
        }
    }
    // Direct removal from within drawer
    async function removeFromWishlist(productId) {
        const item = wishlistItems.find(i => i.id === productId);
        if (item) {
            await toggleWishlist(null, item);
            renderWishlist();
        }
    }
    // Open/Close drawer
    function openWishlistModal() {
        const drawer = document.getElementById('wishlistDrawer');
        const backdrop = document.getElementById('wishlistBackdrop');
        const content = document.getElementById('wishlistContent');

        drawer.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        void drawer.offsetHeight; // Reflow

        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');

        content.classList.remove('translate-x-full');
        content.classList.add('translate-x-0');

        renderWishlist();
    }

    function closeWishlistModal() {
        const drawer = document.getElementById('wishlistDrawer');
        const backdrop = document.getElementById('wishlistBackdrop');
        const content = document.getElementById('wishlistContent');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');

        content.classList.remove('translate-x-0');
        content.classList.add('translate-x-full');

        setTimeout(() => {
            drawer.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }
    // Render list in drawer
    function renderWishlist() {
        const container = document.getElementById('wishlistItemsContainer');
        if (!container) return;
        if (wishlistItems.length === 0) {
            container.innerHTML = `
                <div class="text-center py-24 flex flex-col items-center justify-center h-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <p class="text-[10px] tracking-widest text-gray-400 uppercase">Your wishlist is empty</p>
                </div>
            `;
            return;
        }
        let html = '<div class="flex flex-col gap-6">';
        wishlistItems.forEach(item => {
            const priceFormatted = new Intl.NumberFormat('id-ID').format(item.price);
            html += `
                <div class="flex gap-4 items-center pb-4 border-b border-gray-100 last:border-0" id="wishlist-item-row-${item.id}">
                    <div class="w-16 h-20 bg-gray-100 flex-shrink-0 cursor-pointer overflow-hidden" onclick="handleWishlistItemClick(${item.id})">
                        <img src="${item.image_path}" alt="${item.name}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[8px] tracking-widest text-gray-400 uppercase block mb-1">${item.category || 'Clothing'}</span>
                        <h4 class="text-xs tracking-wide text-gray-900 truncate hover:underline cursor-pointer" onclick="handleWishlistItemClick(${item.id})">${item.name}</h4>
                        <p class="text-xs font-light text-gray-800 mt-1">IDR ${priceFormatted}</p>
                    </div>
                    <div class="flex flex-col gap-2 items-end">
                        <button onclick="removeFromWishlist(${item.id})" class="text-gray-300 hover:text-red-500 p-1 transition-colors" title="Remove">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        <button onclick="handleWishlistItemClick(${item.id})" class="text-[9px] tracking-[0.15em] border border-black px-2 py-1 hover:bg-black hover:text-white transition-colors uppercase whitespace-nowrap">
                            QUICK VIEW
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }
    // Redirect or open quickview logic
    function handleWishlistItemClick(productId) {
        closeWishlistModal();
        const isCollectionPage = window.location.pathname.endsWith('/collection');
        if (isCollectionPage) {
            openQuickView(productId);
        } else {
            window.location.href = "{{ route('collection') }}?quickview=" + productId;
        }
    }
    // Micro-interaction: Fly animation
    function animateHeartToNavbar(startEl, endEl) {
        const startRect = startEl.getBoundingClientRect();
        const endRect = endEl.getBoundingClientRect();
        const clone = document.createElement('div');
        clone.className = 'flying-heart';
        clone.style.top = `${startRect.top}px`;
        clone.style.left = `${startRect.left}px`;
        clone.style.width = `${startRect.width}px`;
        clone.style.height = `${startRect.height}px`;

        // Use identical heart SVG content
        clone.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full" viewBox="0 0 24 24" fill="#ef4444" stroke="none">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        `;
        document.body.appendChild(clone);
        // Force reflow
        clone.offsetHeight;
        // Animate
        clone.style.transition = 'all 0.8s cubic-bezier(0.19, 1, 0.22, 1)';
        clone.style.top = `${endRect.top}px`;
        clone.style.left = `${endRect.left}px`;
        clone.style.transform = 'scale(0.3) rotate(360deg)';
        clone.style.opacity = '0.1';
        setTimeout(() => {
            clone.remove();

            // Pulse navbar icon
            endEl.style.transform = 'scale(1.3)';
            setTimeout(() => {
                endEl.style.transform = '';
            }, 250);
        }, 800);
    }
    // Auto load
    document.addEventListener('DOMContentLoaded', initWishlist);
</script>

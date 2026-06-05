<!-- Elegant Premium Membership Modal -->
<div id="membershipModal" class="fixed inset-0 z-[150] hidden flex items-center justify-center opacity-0 transition-opacity duration-300 pointer-events-none" role="dialog" aria-modal="true">
    <!-- Dark semi-transparent background overlay -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" onclick="closeMembershipModal()"></div>
    
    <!-- Modal Card Box (Clean, Minimalist Layout Matching Vesta) -->
    <div class="relative bg-white border border-black text-stone-900 max-w-xl w-full p-8 md:p-10 mx-4 shadow-2xl transform scale-95 transition-all duration-300 max-h-[85vh] overflow-y-auto custom-scrollbar flex flex-col">
        <!-- Close Button (Top Right 'X') -->
        <button type="button" onclick="closeMembershipModal()" class="absolute top-6 right-6 text-stone-400 hover:text-black transition-colors duration-200 focus:outline-none cursor-pointer" aria-label="Close Modal">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Header -->
        <div class="mb-8 border-b border-stone-200 pb-6">
            <span class="text-[9px] tracking-[0.3em] text-stone-400 uppercase font-bold">Vesta Privilege Club</span>
            <h2 class="text-2xl font-serif tracking-wider text-black mt-1 uppercase">Membership Guide</h2>
            <p class="text-xs text-stone-500 mt-2 font-light leading-relaxed">Understand our tiers, how to accumulate privilege points, and how to redeem them for your orders.</p>
        </div>

        <!-- Content Sections -->
        <div class="space-y-8 pr-1">
            <!-- 1. Tier Hierarchy & Level Up Requirements -->
            <div>
                <h3 class="text-[10px] tracking-[0.25em] text-black uppercase font-bold mb-4 flex items-center gap-2">
                    <span class="inline-block w-1.5 h-1.5 bg-black rounded-full"></span>
                    1. Tier Hierarchy & Requirements
                </h3>
                <p class="text-xs text-stone-600 leading-relaxed font-light mb-4">
                    Your membership tier is evaluated automatically based on your annual spending (successful orders within the last 365 days):
                </p>
                <div class="space-y-3 font-mono text-[11px] bg-stone-50 p-4 border border-stone-200">
                    <div class="flex justify-between items-center text-stone-700">
                        <span class="font-bold">Bronze</span>
                        <span>Default / Spend < IDR 5,000,000</span>
                    </div>
                    <div class="flex justify-between items-center text-stone-700">
                        <span class="font-bold text-stone-900">Silver</span>
                        <span>Spend ≥ IDR 5,000,000</span>
                    </div>
                    <div class="flex justify-between items-center text-stone-700">
                        <span class="font-bold text-amber-600">Gold</span>
                        <span>Spend ≥ IDR 15,000,000</span>
                    </div>
                    <div class="flex justify-between items-center text-stone-700">
                        <span class="font-bold text-purple-700">Platinum</span>
                        <span>Spend ≥ IDR 30,000,000</span>
                    </div>
                </div>
            </div>

            <!-- 2. How to Earn Points -->
            <div>
                <h3 class="text-[10px] tracking-[0.25em] text-black uppercase font-bold mb-4 flex items-center gap-2">
                    <span class="inline-block w-1.5 h-1.5 bg-black rounded-full"></span>
                    2. Earning Points
                </h3>
                <p class="text-xs text-stone-600 leading-relaxed font-light mb-3">
                    Points are automatically credited to your account when an order status is updated to success. Higher tiers earn points at faster rates:
                </p>
                <div class="grid grid-cols-2 gap-3 font-mono text-[10px] bg-stone-50 p-4 border border-stone-200">
                    <div class="text-stone-700">
                        <span class="font-bold">Bronze:</span> IDR 50,000 = 1 Point
                    </div>
                    <div class="text-stone-700">
                        <span class="font-bold">Silver:</span> IDR 40,000 = 1 Point
                    </div>
                    <div class="text-stone-700">
                        <span class="font-bold">Gold:</span> IDR 30,000 = 1 Point
                    </div>
                    <div class="text-stone-700">
                        <span class="font-bold">Platinum:</span> IDR 25,000 = 1 Point
                    </div>
                </div>
            </div>

            <!-- 3. How to Use Points -->
            <div>
                <h3 class="text-[10px] tracking-[0.25em] text-black uppercase font-bold mb-4 flex items-center gap-2">
                    <span class="inline-block w-1.5 h-1.5 bg-black rounded-full"></span>
                    3. Redeeming Points
                </h3>
                <p class="text-xs text-stone-600 leading-relaxed font-light">
                    Redeem your accumulated points directly at the checkout page to get discounts on your orders:
                </p>
                <div class="mt-3 font-mono text-[11px] bg-stone-50 p-4 border border-stone-200 text-stone-850">
                    <span class="font-bold text-black">Redemption Rate:</span> 1 Point = IDR 1,000 Discount
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 border-t border-stone-250 pt-6 flex justify-end">
            <button type="button" onclick="closeMembershipModal()" class="bg-black text-white hover:bg-stone-800 text-[10px] tracking-[0.2em] uppercase font-bold px-8 py-3.5 transition-colors duration-200 focus:outline-none cursor-pointer border border-black">
                Close Guide
            </button>
        </div>
    </div>
</div>

<!-- Custom Scrollbar Styles for the Modal -->
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f5f5f4; /* stone-100 */
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d6d3d1; /* stone-300 */
        border-radius: 2px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a29e; /* stone-400 */
    }
</style>

<script>
    function openMembershipModal() {
        const modal = document.getElementById('membershipModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        // Force reflow
        modal.offsetHeight;
        modal.classList.remove('opacity-0', 'pointer-events-none');
        const card = modal.querySelector('.transform');
        if (card) {
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }
        // Lock body scroll
        document.body.style.overflow = 'hidden';
    }

    function closeMembershipModal() {
        const modal = document.getElementById('membershipModal');
        if (!modal) return;
        modal.classList.add('opacity-0', 'pointer-events-none');
        const card = modal.querySelector('.transform');
        if (card) {
            card.classList.remove('scale-100');
            card.classList.add('scale-95');
        }
        // Wait for animation transition
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
        // Unlock body scroll
        document.body.style.overflow = '';
    }
    
    // Close modal when Esc key is pressed
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeMembershipModal();
        }
    });
</script>

@extends('base.base')

@section('content')
<div class="pt-20 min-h-screen bg-stone-50 text-black flex flex-col items-center justify-start overflow-hidden">
    
    <!-- Alpine JS Component -->
    <div x-data="vestaPhilosophy()" class="w-full max-w-6xl px-4 md:px-8 py-20 flex flex-col items-center flex-1 justify-center min-h-[70vh]">
        
        <div class="text-center mb-16">
            <span class="text-xs tracking-[0.3em] text-gray-400 uppercase">Our Philosophy</span>
        </div>

        <!-- Interactive Letters -->
        <div class="flex items-center justify-center gap-3 md:gap-10 mb-16" 
             x-on:touchstart="touchStart($event)" 
             x-on:touchend="touchEnd($event)">
            <template x-for="(letter, index) in letters" :key="index">
                <button 
                    @click="active = index"
                    class="text-6xl sm:text-7xl md:text-8xl lg:text-[10rem] font-serif transition-all duration-500 hover:text-black cursor-pointer select-none"
                    :class="active === index ? 'text-black scale-110 drop-shadow-lg' : 'text-gray-300 opacity-50'"
                >
                    <span x-text="letter.char"></span>
                </button>
            </template>
        </div>

        <!-- Philosophy Content -->
        <div class="relative w-full max-w-2xl h-48 md:h-40 flex items-center justify-center text-center px-4">
            <template x-for="(letter, index) in letters" :key="index">
                <div 
                    x-show="active === index"
                    x-transition:enter="transition ease-out duration-500 delay-150"
                    x-transition:enter-start="opacity-0 translate-y-8"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-300 absolute inset-0"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-8"
                    class="w-full"
                >
                    <h3 class="text-xl md:text-3xl font-serif tracking-[0.2em] mb-4 uppercase text-black" x-text="letter.title"></h3>
                    <div class="w-12 h-px bg-black mx-auto mb-6"></div>
                    <p class="text-gray-600 text-sm md:text-base leading-relaxed tracking-wide" x-text="letter.desc"></p>
                </div>
            </template>
        </div>

        <!-- Controls (Mobile/Slider feel) -->
        <div class="flex items-center gap-6 mt-12">
            <button @click="prev()" class="p-3 border border-gray-200 rounded-full hover:bg-black hover:text-white transition-colors" :class="{'opacity-30 cursor-not-allowed': active === 0}">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <div class="flex items-center gap-3">
                <template x-for="(_, index) in letters" :key="index">
                    <button @click="active = index" class="w-2 h-2 rounded-full transition-all duration-300" :class="active === index ? 'bg-black w-6' : 'bg-gray-300'"></button>
                </template>
            </div>
            <button @click="next()" class="p-3 border border-gray-200 rounded-full hover:bg-black hover:text-white transition-colors" :class="{'opacity-30 cursor-not-allowed': active === letters.length - 1}">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

    </div>

    <!-- Heritage Section -->
    <div class="w-full bg-white py-32 px-6 lg:px-8 border-t border-gray-100">
        <div class="max-w-3xl mx-auto text-center">
            <span class="text-xs tracking-[0.3em] text-gray-400 uppercase">The Essence of VESTA</span>
            <h2 class="text-3xl md:text-4xl font-serif tracking-[0.1em] mt-8 mb-12 leading-relaxed">
                "Where Silence Speaks Volumes and Simplicity Becomes the Ultimate Sophistication."
            </h2>
            <div class="w-16 h-px bg-black mx-auto mb-12"></div>
            
            <p class="text-gray-600 text-base md:text-lg leading-loose mb-16 font-light">
                At VESTA, we do not simply make clothes; we sculpt identities. In a world cluttered with noise and fleeting trends, we stand as a sanctuary of pure aesthetics. Every stitch, every fold, and every drape is a deliberate brushstroke on the canvas of modern elegance.
            </p>

            <div class="grid md:grid-cols-2 gap-16 text-left mt-20">
                <div class="relative">
                    <div class="absolute -left-4 -top-6 text-7xl text-gray-100 font-serif opacity-50 pointer-events-none">01</div>
                    <h4 class="text-xs font-semibold tracking-[0.2em] text-black mb-4 uppercase relative z-10">The Artisanal Touch</h4>
                    <p class="text-gray-500 text-sm leading-relaxed font-light relative z-10">
                        We traverse the globe to source the most extraordinary fabrics, selecting only those that speak to our stringent standards. Our master artisans breathe life into these raw materials, transforming them into wearable masterpieces that feel like a second skin.
                    </p>
                </div>
                <div class="relative">
                    <div class="absolute -left-4 -top-6 text-7xl text-gray-100 font-serif opacity-50 pointer-events-none">02</div>
                    <h4 class="text-xs font-semibold tracking-[0.2em] text-black mb-4 uppercase relative z-10">Timeless Canvas</h4>
                    <p class="text-gray-500 text-sm leading-relaxed font-light relative z-10">
                        We design for the unapologetic minimalist. Our silhouettes are stripped of the unnecessary, leaving only what is essential and profound. VESTA is not just fashion for a season; it is an enduring legacy crafted for a lifetime.
                    </p>
                </div>
            </div>
            
            <div class="mt-32 pt-16 border-t border-gray-100">
                <h3 class="text-xl md:text-2xl font-serif tracking-[0.15em] italic text-gray-800">
                    "Redefining the Luxury of Less."
                </h3>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('vestaPhilosophy', () => ({
            active: 0,
            touchStartX: 0,
            letters: [
                { char: 'V', title: 'Visionary', desc: 'Looking beyond fleeting trends to create enduring style. We design for tomorrow while honoring the classic silhouettes of the past.' },
                { char: 'E', title: 'Excellence', desc: 'An uncompromising commitment to quality. From the finest materials to masterful craftsmanship, excellence is woven into every thread.' },
                { char: 'S', title: 'Sophistication', desc: 'The art of refined simplicity. Our collections embody an understated elegance that speaks volumes without saying a word.' },
                { char: 'T', title: 'Timelessness', desc: 'Fashion that transcends seasons. We create wardrobe staples designed to be cherished and worn for years to come.' },
                { char: 'A', title: 'Authenticity', desc: 'Staying true to our core values. We believe in transparent practices, ethical sourcing, and genuine self-expression.' }
            ],
            next() {
                if (this.active < this.letters.length - 1) this.active++;
            },
            prev() {
                if (this.active > 0) this.active--;
            },
            touchStart(e) {
                this.touchStartX = e.changedTouches[0].screenX;
            },
            touchEnd(e) {
                let touchEndX = e.changedTouches[0].screenX;
                if (this.touchStartX - touchEndX > 50) {
                    this.next(); // Swipe left
                } else if (this.touchStartX - touchEndX < -50) {
                    this.prev(); // Swipe right
                }
            }
        }))
    })
</script>
@endsection

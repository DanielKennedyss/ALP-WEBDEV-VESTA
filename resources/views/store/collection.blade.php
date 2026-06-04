@extends('base.base')

@section('content')
    <div class="pt-28 pb-24 px-4 lg:px-8 bg-stone-50 min-h-[80vh] flex items-center justify-center">
        <div class="max-w-[1400px] mx-auto w-full">
            
            {{-- Dynamic Event Banner / Fallback Section --}}
            <div class="mb-8 w-full">
                @if($activeEvent)
                    {{-- Active Event Banner --}}
                    <div class="relative overflow-hidden border p-8 md:p-12 transition-all duration-500 rounded-lg shadow-sm"
                         style="background-color: {{ $activeEvent->theme_color ?? '#000000' }}; color: {{ $activeEvent->text_color ?? '#ffffff' }}; border-color: {{ ($activeEvent->text_color ?? '#ffffff') }}44;">
                        {{-- Decorative pattern / background image with overlay --}}
                        @if($activeEvent->banner_image)
                            <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-25 pointer-events-none" style="background-image: url('{{ $activeEvent->banner_image }}')"></div>
                        @endif
                        
                        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                            <div>
                                <div class="inline-flex items-center gap-2 border px-3 py-1 mb-4 text-[9px] tracking-[0.25em] uppercase font-semibold" style="border-color: currentColor;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                    Limited Time Event
                                </div>
                                <h2 class="text-3xl md:text-5xl font-serif tracking-[0.1em] mb-4 uppercase">
                                    {{ $activeEvent->name }}
                                </h2>
                                <p class="text-xs md:text-sm tracking-widest font-light mb-6 opacity-90">
                                    EXCLUSIVE OFFERS FROM {{ $activeEvent->start_date->format('M d, Y') }} UNTIL {{ $activeEvent->end_date->format('M d, Y') }}
                                </p>
                                <a href="{{ route('collections.index', ['filter_event' => $activeEvent->id]) }}#product-grid"
                                   class="inline-block transition-all text-[10px] tracking-[0.2em] px-8 py-3.5 font-medium border duration-300"
                                   style="background-color: {{ $activeEvent->text_color ?? '#ffffff' }}; color: {{ $activeEvent->theme_color ?? '#000000' }}; border-color: {{ $activeEvent->text_color ?? '#ffffff' }};"
                                   onmouseover="this.style.backgroundColor='transparent'; this.style.color='{{ $activeEvent->text_color ?? '#ffffff' }}'"
                                   onmouseout="this.style.backgroundColor='{{ $activeEvent->text_color ?? '#ffffff' }}'; this.style.color='{{ $activeEvent->theme_color ?? '#000000' }}'">
                                    EXPLORE COLLECTION
                                </a>
                            </div>
                            
                            @if($activeEvent->banner_image)
                                <div class="hidden lg:block relative aspect-[16/9] w-full overflow-hidden border" style="border-color: {{ ($activeEvent->text_color ?? '#ffffff') }}22;">
                                    <img src="{{ $activeEvent->banner_image }}" alt="{{ $activeEvent->name }}" class="w-full h-full object-cover object-center transform hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Fallback Default Banner (New Arrivals) --}}
                    <div class="relative overflow-hidden bg-gradient-to-r from-stone-900 via-neutral-900 to-stone-900 text-white border border-stone-800 p-8 md:p-12 rounded-lg shadow-sm">
                        <div class="relative z-10 flex flex-col items-center text-center py-6">
                            <span class="text-xs tracking-[0.4em] text-amber-400/90 uppercase mb-3 font-semibold">Exclusively VESTA</span>
                            <h2 class="text-3xl md:text-5xl font-serif tracking-[0.2em] mb-4 uppercase">NEW ARRIVALS</h2>
                            <p class="text-xs md:text-sm tracking-widest font-light mb-8 max-w-xl opacity-80 leading-relaxed">
                                Discover the latest additions to our luxury apparel collection. Crafted with exceptional detail and timeless aesthetics.
                            </p>
                            <div class="flex gap-4">
                                <a href="{{ route('collections.index') }}"
                                   class="bg-white text-stone-900 hover:bg-stone-900 hover:text-white border border-white transition-all text-[10px] tracking-[0.2em] px-8 py-3.5 font-medium">
                                    SHOP NEW DESIGNS
                                </a>
                            </div>
                        </div>
                        {{-- subtle decorative luxury ring --}}
                        <div class="absolute -right-20 -bottom-20 w-96 h-96 border border-white/5 rounded-full pointer-events-none"></div>
                        <div class="absolute -left-20 -top-20 w-96 h-96 border border-white/5 rounded-full pointer-events-none"></div>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

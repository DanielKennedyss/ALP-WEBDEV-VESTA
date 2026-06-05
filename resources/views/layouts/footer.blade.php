<footer id="contact" class="w-full bg-[#fafafa] text-black pt-32 pb-16 px-6 lg:px-16 border-t border-[#e9e9e6] mt-32">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <div class="md:col-span-2">
                <a href="/" class="text-2xl font-serif tracking-[0.3em] text-black">VESTA</a>
                <p class="text-gray-500 text-sm mt-4 leading-relaxed max-w-sm">
                    Luxury fashion for the discerning individual. Crafted with passion, worn with pride.
                </p>
            </div>
            <div>
                <h4 class="text-xs tracking-[0.25em] mb-6">NAVIGATE</h4>
                <ul class="space-y-3">
                    <li><a href="/" class="text-gray-400 text-sm hover:text-black transition-colors">Home</a></li>
                    <li><a href="{{ route('collections.index') }}" class="text-gray-400 text-sm hover:text-black transition-colors">Collection</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-400 text-sm hover:text-black transition-colors">About</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-400 text-sm hover:text-black transition-colors">Contact</a></li>
                </ul>
            </div>

            {{-- Sektor Kanan: High-End Navigation (Spasi Lebar & Ringkas) --}}
            <div class="lg:col-span-5 grid grid-cols-2 gap-8 lg:justify-end">
                {{-- Kolom Kosong sengaja dibuat untuk memaksimalkan Whitespace asimetris pada layar besar --}}
                <div class="hidden md:block"></div>
                
                <div class="text-left lg:text-right">
                    <h4 class="text-[10px] font-bold tracking-[0.25em] text-[#999990] uppercase mb-6">
                        Navigation
                    </h4>
                    <ul class="space-y-4">
                        <li>
                            <a href="/" class="text-xs font-medium text-black tracking-wide uppercase no-underline transition-opacity duration-300 hover:opacity-50">
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('collection') }}" class="text-xs font-medium text-black tracking-wide uppercase no-underline transition-opacity duration-300 hover:opacity-50">
                                Collection
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('about') }}" class="text-xs font-medium text-black tracking-wide uppercase no-underline transition-opacity duration-300 hover:opacity-50">
                                About
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}" class="text-xs font-medium text-black tracking-wide uppercase no-underline transition-opacity duration-300 hover:opacity-50">
                                Contact
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- Row 2: Sub-Footer Line --}}
        <div class="pt-8 border-t border-[#e9e9e6] flex flex-col sm:flex-row items-center justify-between gap-6">
            
            {{-- Copyright dengan format waktu dinamis & nama legal bisnis rapi --}}
            <p class="text-[11px] tracking-widest text-[#8a8a85] uppercase font-light">
                &copy; {{ date('Y') }} VESTA Atelier. All rights reserved.
            </p>
            
            {{-- Social Media Iconography Minimalis --}}
            <div class="flex items-center gap-8">
                <a href="#" class="text-black transition-opacity duration-300 hover:opacity-40" aria-label="VESTA on X">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.226 1.523 4.077 3.415 4.099-1.107 1.05-2.655 1.64-4.237 1.64-.26 0-.515-.015-.766-.04.56 1.745 2.193 3.02 4.098 3.06-1.503 1.18-3.396 1.883-5.454 1.883-.355 0-.704-.02-1.048-.05 1.954.98 4.298 1.62 6.803 1.62 8.162 0 12.628-6.41 12.628-11.98 0-.182 0-.363-.01-.543.868-.628 1.624-1.408 2.223-2.303"/>
                    </svg>
                </a>
                <a href="#" class="text-black transition-opacity duration-300 hover:opacity-40" aria-label="VESTA on Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204 0.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
            </div>

        </div>

    </div>
</footer>
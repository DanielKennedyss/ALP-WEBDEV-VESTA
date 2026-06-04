@extends('base.base')

@section('content')
<!-- Premium Fonts Integration -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<div class="w-full bg-stone-50 text-black py-24 min-h-[85vh] flex flex-col justify-center items-center font-sans">
    <div class="w-full max-w-6xl px-6 lg:px-8 mx-auto">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start mt-8">
            
            <!-- LEFT PANEL: Striking Bold Header Info (col-span-5) -->
            <div class="lg:col-span-5 space-y-12">
                <div class="space-y-4">
                    <!-- Serif font tracking used for premium branding look matching VESTA -->
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-serif font-light tracking-[0.08em] leading-[1.05] text-black">
                        Contact<br>Support
                    </h1>
                    <!-- Guaranteed visible red accent using inline style -->
                    <div class="w-16 h-0.5 mt-6" style="background-color: #dc2626;"></div>
                </div>

                <div class="space-y-8 pt-4">
                    <div>
                        <!-- Guaranteed visible red subhead using inline style -->
                        <h4 class="text-[11px] tracking-[0.25em] font-bold uppercase mb-2 font-sans" style="color: #dc2626;">OFFICIAL INQUIRY</h4>
                        <a href="mailto:vestaclothingg@gmail.com" class="text-gray-600 text-sm md:text-base font-light hover:text-black transition-colors font-sans">
                            vestaclothingg@gmail.com
                        </a>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: Custom Light Form matching Vesta & reference alignment (col-span-7) -->
            <div class="lg:col-span-7 bg-white border border-gray-100 p-8 sm:p-12 shadow-sm rounded-none">
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Names (First Name & Last Name side-by-side) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 font-sans">
                        <div>
                            <label for="first_name" class="text-[11px] tracking-wider font-semibold text-gray-800 uppercase mb-2 block">First Name *</label>
                            <input type="text" name="first_name" id="first_name" required
                                class="w-full bg-stone-50 border border-gray-200 px-4 py-3 text-sm text-black placeholder-gray-400 focus:border-red-500 focus:bg-white focus:outline-none transition-all duration-300 rounded-none">
                        </div>
                        <div>
                            <label for="last_name" class="text-[11px] tracking-wider font-semibold text-gray-800 uppercase mb-2 block">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" required
                                class="w-full bg-stone-50 border border-gray-200 px-4 py-3 text-sm text-black placeholder-gray-400 focus:border-red-500 focus:bg-white focus:outline-none transition-all duration-300 rounded-none">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="font-sans">
                        <label for="email" class="text-[11px] tracking-wider font-semibold text-gray-800 uppercase mb-2 block">Email *</label>
                        <input type="email" name="email" id="email" required
                            class="w-full bg-stone-50 border border-gray-200 px-4 py-3 text-sm text-black placeholder-gray-400 focus:border-red-500 focus:bg-white focus:outline-none transition-all duration-300 rounded-none">
                    </div>

                    <!-- Subject / Pilihan Kendala (Radio bullets list) -->
                    <div class="font-sans">
                        <label class="text-[11px] tracking-wider font-semibold text-gray-800 uppercase mb-3 block">Subject *</label>
                        <div class="space-y-3.5 pl-1">
                            
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="subject" value="Technical Support" required
                                    class="w-4 h-4 text-red-650 border-gray-300 bg-white focus:ring-red-500 accent-red-600 cursor-pointer">
                                <span class="text-sm font-light text-gray-600 group-hover:text-black transition-colors">
                                    Technical Support
                                </span>
                            </label>
                            
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="subject" value="Order Status"
                                    class="w-4 h-4 text-red-650 border-gray-300 bg-white focus:ring-red-500 accent-red-600 cursor-pointer">
                                <span class="text-sm font-light text-gray-600 group-hover:text-black transition-colors">
                                    Order Status
                                </span>
                            </label>
                            
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="subject" value="Performance Inquiry"
                                    class="w-4 h-4 text-red-650 border-gray-300 bg-white focus:ring-red-500 accent-red-600 cursor-pointer">
                                <span class="text-sm font-light text-gray-600 group-hover:text-black transition-colors">
                                    Performance Inquiry
                                </span>
                            </label>
                            
                        </div>
                    </div>

                    <!-- Phone Number (Placed BELOW subject, supports all countries via single tel input field) -->
                    <div class="font-sans">
                        <label for="phone" class="text-[11px] tracking-wider font-semibold text-gray-800 uppercase mb-2 block">Phone Number</label>
                        <input type="tel" name="phone" id="phone" placeholder="Include country code, e.g. +62 812 3456 7890"
                            class="w-full bg-stone-50 border border-gray-200 px-4 py-3 text-sm text-black placeholder-gray-400 focus:border-red-500 focus:bg-white focus:outline-none transition-all duration-300 rounded-none">
                    </div>

                    <!-- Message -->
                    <div class="font-sans">
                        <label for="message" class="text-[11px] tracking-wider font-semibold text-gray-800 uppercase mb-2 block">Detailed Message *</label>
                        <textarea name="message" id="message" rows="5" required placeholder="Describe your inquiry in detail..."
                            class="w-full bg-stone-50 border border-gray-200 px-4 py-3 text-sm text-black placeholder-gray-400 focus:border-red-500 focus:bg-white focus:outline-none transition-all duration-300 resize-none rounded-none"></textarea>
                    </div>

                    <!-- Submit Button (Vibrant red guaranteed visible via inline styles) -->
                    <div class="pt-2 font-sans">
                        <button type="submit"
                            class="w-full text-white text-[11px] tracking-[0.3em] font-bold py-4 transition-all duration-300 uppercase shadow-none rounded-none hover:opacity-90 cursor-pointer"
                            style="background-color: #dc2626; color: #ffffff !important;">
                            Submit Inquiry
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
        
    </div>
</div>
@endsection

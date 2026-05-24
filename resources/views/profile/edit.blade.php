@extends('base.base')

@section('content')
<style>
    .profile-card {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
    }
    .profile-card:hover {
        transform: translateY(-5px);
        border-color: #000 !important;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
</style>

<div class="pt-32 pb-24 px-6 lg:px-8 bg-white min-h-screen">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 animate-fade-in">
            <div>
                <span class="text-[10px] tracking-[0.3em] text-gray-400 uppercase font-medium">Welcome Back</span>
                <h1 class="text-4xl md:text-5xl font-serif tracking-[0.1em] mt-2 uppercase">{{ Auth::user()->name }}</h1>
            </div>
            <div class="mt-6 md:mt-0">
                <div class="border border-black px-6 py-3 inline-block bg-black text-white">
                    <span class="text-xs tracking-[0.2em] uppercase font-bold">STATUS: {{ Auth::user()->membership_tier_badge }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
            <div class="profile-card delay-1 border border-gray-200 bg-stone-50 p-8 flex flex-col justify-between">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Privilege Points</span>
                <div class="flex items-baseline gap-2">
                    <h2 class="text-4xl font-light">{{ number_format(Auth::user()->loyalty_points ?? 0, 0, ',', '.') }}</h2>
                    <span class="text-xs tracking-widest text-gray-400">PTS</span>
                </div>
            </div>

            <div class="profile-card delay-2 border border-gray-200 p-8 flex flex-col justify-between">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Lifetime Spending</span>
                <h2 class="text-3xl font-light">IDR {{ number_format(Auth::user()->total_spending ?? 0, 0, ',', '.') }}</h2>
            </div>

            <div class="profile-card delay-3 border border-gray-200 p-8 flex flex-col justify-between">
                <span class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium mb-6">Active Orders</span>
                <h2 class="text-4xl font-light">{{ \App\Models\Transaction::where('user_id', Auth::id())->where('status', 'pending')->count() }}</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 items-start mb-16">
            <!-- Navigation Sidebar -->
            <div class="lg:col-span-1 space-y-3 profile-card delay-4">
                <a href="{{ route('profile') }}" class="w-full text-left py-4 px-6 border border-stone-200 hover:border-black font-semibold text-[11px] tracking-[0.2em] uppercase transition-all duration-300 flex justify-between items-center group">
                    <span>My Order History</span>
                    <svg class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ route('profile.edit') }}" class="w-full text-left py-4 px-6 border border-black bg-stone-900 text-white font-semibold text-[11px] tracking-[0.2em] uppercase transition-all duration-300 flex justify-between items-center group">
                    <span>My Profile</span>
                    <svg class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <div class="pt-6 !mt-6 border-t border-stone-100">
                    <form action="{{ route('logout') }}" method="POST" class="w-full m-0">
                        @csrf
                        <button type="submit" class="w-full text-left py-4 px-6 border border-stone-200 hover:border-red-600 font-semibold text-[11px] tracking-[0.2em] uppercase transition-all duration-300 text-stone-500 hover:text-red-600 flex justify-between items-center group bg-white">
                            <span>Sign Out</span>
                            <svg class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content Area: Profile & Account Settings -->
            <div class="lg:col-span-3 profile-card delay-4">
                
                <!-- Section 1: Personal Details -->
                <div class="border border-stone-200 p-8 bg-white">
                    <h4 class="text-xs tracking-[0.2em] uppercase font-bold mb-8 text-stone-900 border-b border-stone-100 pb-4">Personal Details</h4>
                    
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                                @error('name')
                                    <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Email Address</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                                @error('email')
                                    <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="phone_number" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" placeholder="e.g. +62812345678">
                            @error('phone_number')
                                <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="pt-4 text-right">
                            <button type="submit" class="bg-black text-white text-[10px] tracking-[0.2em] uppercase px-8 py-4 hover:bg-stone-800 transition-colors">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Section 2: Change Password -->
                <div class="border border-stone-200 p-8 bg-white">
                    <h4 class="text-xs tracking-[0.2em] uppercase font-bold mb-8 text-stone-900 border-b border-stone-100 pb-4">Security & Password</h4>

                    <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label for="current_password" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Current Password</label>
                            <input type="password" name="current_password" id="current_password" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                            @if($errors->updatePassword->has('current_password'))
                                <span class="text-[10px] text-red-600 mt-1 block">{{ $errors->updatePassword->first('current_password') }}</span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">New Password</label>
                                <input type="password" name="password" id="password" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                                @if($errors->updatePassword->has('password'))
                                    <span class="text-[10px] text-red-600 mt-1 block">{{ $errors->updatePassword->first('password') }}</span>
                                @endif
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                            </div>
                        </div>

                        <div class="pt-4 text-right">
                            <button type="submit" class="bg-black text-white text-[10px] tracking-[0.2em] uppercase px-8 py-4 hover:bg-stone-800 transition-colors">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Section 3: Delete Account -->
                <div class="border border-red-200 bg-red-50/30 p-8">
                    <h4 class="text-xs tracking-[0.2em] uppercase font-bold mb-4 text-red-900">Danger Zone</h4>
                    <p class="text-xs text-stone-500 mb-6 leading-relaxed">Once you delete your account, there is no going back. All of your privilege points, loyalty histories, and transaction records will be permanently erased.</p>
                    
                    @if($errors->userDeletion->any())
                        <div class="mb-6 p-4 bg-red-100/50 border border-red-200 text-red-700 text-xs">
                            {{ $errors->userDeletion->first() }}
                        </div>
                    @endif

                    <div>
                        <button type="button" onclick="openDeleteModal()" class="border border-red-600 text-red-600 hover:bg-red-600 hover:text-white text-[10px] tracking-[0.2em] uppercase px-6 py-3.5 transition-colors font-bold">
                            Delete Account
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-black text-white p-12 text-center profile-card delay-4">
            <h3 class="text-2xl md:text-3xl font-serif tracking-[0.1em] mb-4">VESTA Winter Collection '26 is coming.</h3>
            <p class="text-[10px] text-gray-400 uppercase tracking-[0.3em]">Exclusive preview for {{ Auth::user()->membership_tier_badge }} members.</p>
        </div>
    </div>
</div>

<!-- Elegant Delete Account Modal -->
<div id="deleteModal" class="fixed inset-0 z-[100] {{ $errors->userDeletion->any() ? '' : 'hidden' }} flex items-center justify-center" aria-hidden="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white max-w-md w-full p-8 shadow-2xl border border-stone-100 flex flex-col z-[101] animate-fade-in text-center">
        <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <h3 class="text-lg font-serif tracking-wider mb-2 text-stone-900">Are you absolutely sure?</h3>
        <p class="text-xs text-stone-500 mb-6 leading-relaxed">This action cannot be undone. Please type your password to confirm and permanently delete your account.</p>
        
        <form action="{{ route('profile.destroy') }}" method="POST" class="space-y-4">
            @csrf
            @method('DELETE')
            
            <div class="text-left">
                <label for="delete_password" class="block text-[9px] uppercase tracking-[0.2em] text-stone-400 font-bold mb-2">Your Password</label>
                <input type="password" name="password" id="delete_password" class="w-full border border-stone-200 p-4 text-xs focus:outline-none focus:border-black bg-stone-50/50 transition-colors" required>
                @if($errors->userDeletion->has('password'))
                    <span class="text-[10px] text-red-600 mt-1 block text-left">{{ $errors->userDeletion->first('password') }}</span>
                @endif
            </div>

            <div class="space-y-2 pt-2">
                <button type="submit" class="w-full bg-red-600 text-white text-xs tracking-[0.2em] py-4 hover:bg-red-700 transition-colors uppercase font-bold">
                    Yes, Delete My Account
                </button>
                <button type="button" onclick="closeDeleteModal()" class="w-full border border-stone-200 text-stone-600 text-xs tracking-[0.2em] py-4 hover:bg-stone-50 transition-colors uppercase font-bold bg-white">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
</script>
@endsection

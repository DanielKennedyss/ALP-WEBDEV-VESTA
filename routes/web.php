<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\AuthOtpController; // REVISI: Import Controller OTP Baru
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Middleware\AdminMiddleware;
use App\Mail\ContactInquiryMail; // REVISI: Import Mailable Baru untuk Fitur Kontak
use App\Mail\ContactAutoResponseMail; // REVISI: Import Mailable Baru untuk Auto-Responder Customer
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Public Routes (Front-end)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    session()->forget('buy_now');
    session()->forget('applied_voucher');
    $products = \App\Models\Product::orderBy('created_at', 'desc')->take(8)->get();
    $currentTime = \Carbon\Carbon::now();
    $activeEvents = \App\Models\Event::where('start_date', '<=', $currentTime)
        ->where('end_date', '>=', $currentTime)
        ->get();
    return view('home', compact('products', 'activeEvents'));
})->name('home');

Route::middleware(['web'])->group(function () {
    Route::get('/about', function () { return view('about'); })->name('about');
    Route::get('/contact', function () { return view('contact'); })->name('contact');
    
    // REVISI: Mengubah fungsionalitas kirim pesan agar mengirim ke email toko DAN balasan otomatis ke customer sekaligus
    Route::post('/contact', function (\Illuminate\Http\Request $request) {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'subject'    => 'required|string',
            'message'    => 'required|string|max:5000',
        ]);
        
        // 1. Mengirim email rangkuman tiket bantuan ke vestaclothingg@gmail.com
        Mail::to('vestaclothingg@gmail.com')->send(new ContactInquiryMail($validatedData));

        // 2. Mengirim balasan otomatis (Auto-Responder Receipt) ke email milik customer/sender
        $customerName = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
        Mail::to($validatedData['email'])->send(new ContactAutoResponseMail($customerName));

        return redirect()->back()->with('success', 'Thank you! Your inquiry has been sent to our team successfully.');
    })->name('contact.submit');

    Route::controller(StoreController::class)->group(function () {
        Route::get('/collection', 'collection')->name('collection');
        Route::get('/catalog', 'catalog')->name('catalog');
        Route::get('/collections', 'catalog')->name('collections.index');
        Route::get('/product/{id}', 'product_detail')->name('product.detail');
        Route::get('/cart', 'view_cart')->name('cart.view');
        Route::post('/cart/add/{product_id}', 'add_to_cart')->name('cart.add');
        Route::post('/cart/remove/{cart_key}', 'remove_from_cart')->name('cart.remove');
        Route::post('/cart/update/{cart_key}', 'update_cart')->name('cart.update');
        Route::post('/cart/update-size/{cart_key}', 'update_cart_size')->name('cart.update.size');
        
        Route::post('/direct-checkout/{product_id}', 'direct_checkout')->name('direct.checkout');
        Route::get('/checkout', 'view_checkout')->name('checkout.view');
        Route::post('/checkout/process', 'checkout')->name('checkout.process');

        // Shipping Routes (RajaOngkir Proxy)
        Route::get('/shipping/provinces', [ShippingController::class, 'get_provinces'])->name('shipping.provinces');
        Route::get('/shipping/cities/{province_id}', [ShippingController::class, 'get_cities'])->name('shipping.cities');
        Route::post('/shipping/cost', [ShippingController::class, 'get_shipping_cost'])->name('shipping.cost');
        
        Route::get('/payment/return/{order_id}', 'payment_return')->name('payment_return');
        Route::get('/payment/status/{order_id}', 'payment_status')->name('payment_status');
        Route::post('/payment/callback/{order_id}', 'payment_callback')->name('payment.callback');
        Route::get('/payment/retry/{order_id}', 'payment_retry')->name('payment.retry');

        Route::get('/wishlist/items', 'get_wishlist')->name('wishlist.items');
        Route::post('/wishlist/toggle/{product_id}', 'toggle_wishlist')->name('wishlist.toggle');
        Route::post('/wishlist/sync', 'sync_wishlist')->name('wishlist.sync');
    });
});

/*
|--------------------------------------------------------------------------
| Guest Routes (Login / Register / Forgot Password)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
    
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // REVISI: Tambahan Rute Lupa Password via OTP (Brevo / Gmail SMTP)
    Route::get('/forgot-password', [AuthOtpController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthOtpController::class, 'sendResetOtp'])->name('password.email');
    Route::get('/reset-password', [AuthOtpController::class, 'showResetPasswordForm'])->name('password.reset.form');
    Route::post('/reset-password', [AuthOtpController::class, 'resetPassword'])->name('password.reset.update');
    Route::post('/verify-reset-otp', [AuthOtpController::class, 'verifyResetOtp'])->name('password.verify.otp');
    Route::post('/resend-reset-otp', [AuthOtpController::class, 'resendResetOtp'])->name('password.resend.otp');
});

/*
|--------------------------------------------------------------------------
| Google Open Authentication Routes (Harus di luar Guest Middleware)
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Customer & Admin & OTP Verification)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // REVISI PINDAH TEMPAT: Rute Klaim Voucher ditaruh di bawah kawalan Middleware Auth agar Session Cookie aman 100%
    Route::post('/checkout/apply-voucher', [StoreController::class, 'apply_voucher'])->name('checkout.applyVoucher');

    // REVISI: Tambahan Rute Verifikasi & Resend OTP Akun (Brevo / Gmail SMTP)
    Route::get('/verify-otp', [AuthOtpController::class, 'showVerifyForm'])->name('otp.verify.form');
    Route::post('/verify-otp', [AuthOtpController::class, 'verifyOtp'])->name('otp.verify.submit');
    Route::post('/resend-otp', [AuthOtpController::class, 'sendVerificationOtp'])->name('otp.resend');

    Route::get('/profile', function () {
        session()->forget('buy_now');
        session()->forget('applied_voucher');
        $user = auth()->user();
        if (in_array($user->role, ['owner', 'manager', 'staff'])) {
            return redirect()->route('admin.dashboard');
        }
        $transactions = \App\Models\Transaction::where('user_id', $user->id)
            ->with(['product', 'reviews'])->orderBy('created_at', 'desc')->get();
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->latest()->get();
        return view('profile', compact('transactions', 'addresses'));
    })->name('profile');

    Route::get('/profile/edit', function () {
        return redirect()->route('profile')->with('open-profile-tab', true);
    });
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'changePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'deleteAccount'])->name('profile.destroy');

    Route::get('/profile/change-email/verify', [ProfileController::class, 'showChangeEmailVerifyForm'])->name('profile.change-email.verify.form');
    Route::post('/profile/change-email/verify', [ProfileController::class, 'verifyChangeEmailOtp'])->name('profile.change-email.verify');
    Route::post('/profile/change-email/resend', [ProfileController::class, 'resendChangeEmailOtp'])->name('profile.change-email.resend');

    Route::post('/profile/addresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::put('/profile/addresses/{address}', [ProfileController::class, 'updateAddress'])->name('profile.addresses.update');
    Route::delete('/profile/addresses/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.addresses.destroy');

    Route::post('/profile/orders/{order}/cancel', [StoreController::class, 'cancelOrder'])->name('profile.orders.cancel');
    Route::get('/profile/orders/{order}/track', [StoreController::class, 'trackOrder'])->name('profile.orders.track');
    Route::post('/profile/orders/{order}/receive', [StoreController::class, 'markAsReceived'])->name('profile.orders.receive');
    Route::post('/profile/orders/{order}/review', [StoreController::class, 'submitReview'])->name('profile.orders.review');

    Route::get('/dashboard', function () { return redirect()->route('profile'); });

    Route::middleware([AdminMiddleware::class])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('admin.dashboard.export');
        
        // Product Management
        Route::resource('products', ProductController::class)->except(['show'])->names('admin.products');
        Route::get('/inventory', [ProductController::class, 'index'])->name('admin.inventory');

        // Transaction Management
        Route::controller(TransactionController::class)->group(function () {
            Route::get('/transactions', 'index')->name('admin.transactions.index');
            Route::patch('/transactions/{transaction}/status', 'updateStatus')->name('admin.transactions.updateStatus');
            Route::post('/transactions/export-email', 'sendReportToEmail')->name('admin.transactions.export_email');
        });

        // Voucher Management
        Route::resource('vouchers', VoucherController::class)->names('admin.vouchers');

        // Event Collection Management
        Route::resource('events', EventController::class)->names('admin.events');

        // Staff Management
        Route::resource('staff', StaffController::class)->names('admin.staff');
    });
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\StaffController; 
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Admin\TransactionController; // Tambahkan ini di atas

// Landing Page
Route::get('/', function () {
    $products = \App\Models\Product::all();
    return view('home', compact('products'));
});

Route::middleware(['web'])->group(function () {
    Route::get('/about', function () { return view('about'); })->name('about');
    Route::get('/contact', function () { return view('contact'); })->name('contact');
    Route::post('/contact', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string',
            'message' => 'required|string|max:5000',
        ]);
        return redirect()->back()->with('success', 'Thank you! Your message has been sent successfully. Our team will contact you shortly.');
    })->name('contact.submit');
    Route::get('/collection', [StoreController::class, 'collection'])->name('collection');
    Route::get('/cart', [StoreController::class, 'view_cart'])->name('cart.view');
    
    // Cart Actions
    Route::post('/cart/add/{product_id}', [StoreController::class, 'add_to_cart'])->name('cart.add');
    Route::post('/cart/remove/{cart_key}', [StoreController::class, 'remove_from_cart'])->name('cart.remove');
    Route::post('/cart/update/{cart_key}', [StoreController::class, 'update_cart'])->name('cart.update');
    Route::post('/cart/update-size/{cart_key}', [StoreController::class, 'update_cart_size'])->name('cart.update.size');
    
    // Checkout & Payment
    Route::post('/direct-checkout/{product_id}', [StoreController::class, 'direct_checkout'])->name('direct.checkout');
    Route::post('/checkout', [StoreController::class, 'checkout'])->name('checkout');
    Route::get('/payment/return/{order_id}', [StoreController::class, 'payment_return'])->name('payment_return');
    Route::get('/payment/status/{order_id}', [StoreController::class, 'payment_status'])->name('payment_status');
    Route::post('/payment/callback/{order_id}', [StoreController::class, 'payment_callback'])->name('payment.callback');
    Route::get('/payment/retry/{order_id}', [StoreController::class, 'payment_retry'])->name('payment.retry');

    // Wishlist Actions
    Route::get('/wishlist/items', [StoreController::class, 'get_wishlist'])->name('wishlist.items');
    Route::post('/wishlist/toggle/{product_id}', [StoreController::class, 'toggle_wishlist'])->name('wishlist.toggle');
    Route::post('/wishlist/sync', [StoreController::class, 'sync_wishlist'])->name('wishlist.sync');
});

/*
|--------------------------------------------------------------------------
| Guest Routes (Login / Register)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
    
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    
    // Dashboard Customer
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Admin & Owner Routes (Gunakan Class Path langsung)
Route::middleware([AdminMiddleware::class])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Product Management
        Route::get('/inventory', [ProductController::class, 'index'])->name('admin.inventory');
        Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');

        // Transaction Management
        Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
        Route::patch('/transactions/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('admin.transactions.updateStatus');

        // Route Staff
        Route::get('/staff', [StaffController::class, 'index'])->name('admin.staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('admin.staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('admin.staff.store');
    });
});
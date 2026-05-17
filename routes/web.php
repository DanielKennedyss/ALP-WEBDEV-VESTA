<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\StaffController; 
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Admin\TransactionController;

// Landing Page
Route::get('/', function () {
    $products = \App\Models\Product::orderBy('created_at', 'desc')->take(8)->get();
    return view('home', compact('products'));
})->name('home');

// Store Routes (accessible by guests too - but needs web middleware for session/CSRF)
Route::middleware(['web'])->group(function () {
    Route::get('/collection', [StoreController::class, 'collection'])->name('collection');
    Route::post('/cart/add/{product_id}', [StoreController::class, 'add_to_cart'])->name('cart.add');
    Route::get('/cart', [StoreController::class, 'view_cart'])->name('cart.view');
    Route::post('/cart/remove/{product_id}', [StoreController::class, 'remove_from_cart'])->name('cart.remove');
    Route::post('/cart/update/{product_id}', [StoreController::class, 'update_cart'])->name('cart.update');
    Route::post('/direct-checkout/{product_id}', [StoreController::class, 'direct_checkout'])->name('direct.checkout');
    Route::post('/checkout', [StoreController::class, 'checkout'])->name('checkout');
    Route::get('/payment/return/{order_id}', [StoreController::class, 'payment_return'])->name('payment_return');
    Route::get('/payment/status/{order_id}', [StoreController::class, 'payment_status'])->name('payment_status');

    // Debug route to test payment page directly
    Route::get('/test-payment/{transaction_id}', function ($transaction_id) {
        $order = \App\Models\Transaction::findOrFail($transaction_id);
        $snapToken = $order->payment_url;
        return view('store.payment', compact('snapToken', 'order'));
    })->name('test.payment');
});

// Guest Routes
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
        $user = auth()->user();
        $transactions = \App\Models\Transaction::whereHas('product', function ($q) use ($user) {
            $q->where('customer_name', $user->name);
        })->with('product')->orderBy('created_at', 'desc')->get();
        return view('dashboard', compact('transactions'));
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
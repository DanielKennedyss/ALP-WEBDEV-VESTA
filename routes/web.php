<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Middleware\AdminMiddleware;
/*
|--------------------------------------------------------------------------
| Public Routes (Front-end)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $products = \App\Models\Product::orderBy('created_at', 'desc')->take(8)->get();
    return view('home', compact('products'));
})->name('home');

Route::middleware(['web'])->group(function () {
    Route::get('/about', function () { return view('about'); })->name('about');
    Route::get('/contact', function () { return view('contact'); })->name('contact');
    
    Route::post('/contact', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'subject'    => 'required|string',
            'message'    => 'required|string|max:5000',
        ]);
        return redirect()->back()->with('success', 'Thank you! Your message has been sent successfully.');
    })->name('contact.submit');

    Route::controller(StoreController::class)->group(function () {
        Route::get('/collection', 'collection')->name('collection');
        Route::get('/cart', 'view_cart')->name('cart.view');
        Route::post('/cart/add/{product_id}', 'add_to_cart')->name('cart.add');
        Route::post('/cart/remove/{cart_key}', 'remove_from_cart')->name('cart.remove');
        Route::post('/cart/update/{cart_key}', 'update_cart')->name('cart.update');
        Route::post('/cart/update-size/{cart_key}', 'update_cart_size')->name('cart.update.size');
        
        Route::post('/direct-checkout/{product_id}', 'direct_checkout')->name('direct.checkout');
        Route::get('/checkout', 'view_checkout')->name('checkout.view');
        Route::post('/checkout/process', 'checkout')->name('checkout.process');
        
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
| Guest Routes (Login / Register)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
    
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
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
| Authenticated Routes (Customer & Admin)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/profile', function () {
        $user = auth()->user();
        if (in_array($user->role, ['owner', 'manager', 'staff'])) {
            return redirect()->route('admin.dashboard');
        }
        $transactions = \App\Models\Transaction::where('user_id', $user->id)
            ->with('product')->orderBy('created_at', 'desc')->get();
        return view('profile', compact('transactions'));
    })->name('profile');

    Route::get('/dashboard', function () { return redirect()->route('profile'); });

    Route::middleware([AdminMiddleware::class])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Product Management
        Route::resource('products', ProductController::class)->except(['show'])->names('admin.products');
        Route::get('/inventory', [ProductController::class, 'index'])->name('admin.inventory');

        // Transaction Management
        Route::controller(TransactionController::class)->group(function () {
            Route::get('/transactions', 'index')->name('admin.transactions.index');
            Route::patch('/transactions/{transaction}/status', 'updateStatus')->name('admin.transactions.updateStatus');
        });

        // Voucher Management
        Route::resource('vouchers', VoucherController::class)->names('admin.vouchers');

        // Staff Management
        Route::resource('staff', StaffController::class)->names('admin.staff');
    });
});
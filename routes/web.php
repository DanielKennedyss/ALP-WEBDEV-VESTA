<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\VoucherController; // <--- BEST PRACTICE: Import namespace controller voucher baru
use App\Http\Middleware\AdminMiddleware;

/*
|--------------------------------------------------------------------------
| Public Routes (E-commerce Front-end)
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
});

/*
|--------------------------------------------------------------------------
| Guest Routes (Login / Register)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
    
    // Registrasi Akun Baru Vesta
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Customer & Admin)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Global Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    /*
     |--- Customer Side ---
     | Route ini hanya bisa diakses oleh role 'customer'. 
     | Jika Owner/Manager/Staff login, mereka tidak masuk ke sini.
     */
    Route::get('/profile', function () {
        $user = auth()->user();
        
        // Proteksi: Jika admin personil mencoba akses profil customer, arahkan ke dashboard admin
        if (in_array($user->role, ['owner', 'manager', 'staff'])) {
            return redirect()->route('admin.dashboard');
        }

        $transactions = \App\Models\Transaction::where('customer_name', $user->name)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('profile', compact('transactions'));
    })->name('profile');

    // Redirect dashboard lama ke profile customer
    Route::get('/dashboard', function () {
        return redirect()->route('profile');
    });

    /*
     |--- Admin Panel Side (Owner, Manager, Staff) ---
     | Menggunakan AdminMiddleware untuk memfilter personil internal.
     */
    Route::middleware([AdminMiddleware::class])->prefix('admin')->group(function () {
        
        // Main Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Inventory & Product Management
        Route::controller(ProductController::class)->group(function () {
            Route::get('/inventory', 'index')->name('admin.inventory');
            Route::get('/products/create', 'create')->name('admin.products.create');
            Route::post('/products', 'store')->name('admin.products.store');
            Route::get('/products/{product}/edit', 'edit')->name('admin.products.edit');
            Route::put('/products/{product}', 'update')->name('admin.products.update');
            Route::delete('/products/{product}', 'destroy')->name('admin.products.destroy');
        });

        // Transaction & Order Management
        Route::controller(TransactionController::class)->group(function () {
            Route::get('/transactions', 'index')->name('admin.transactions.index');
            Route::patch('/transactions/{transaction}/status', 'updateStatus')->name('admin.transactions.updateStatus');
        });

        // ====== OPERATIONAL FITUR: Luxury Voucher Management ======
        // Menggunakan Route::resource dengan kustomisasi penamaan alias rute 'admin.vouchers.*'
        Route::resource('vouchers', VoucherController::class)->names([
            'index'   => 'admin.vouchers.index',
            'create'  => 'admin.vouchers.create',
            'store'   => 'admin.vouchers.store',
            'edit'    => 'admin.vouchers.edit',
            'update'  => 'admin.vouchers.update',
            'destroy' => 'admin.vouchers.destroy',
        ]);

        // Staff & Access Management (Hanya Owner yang bisa akses penuh biasanya)
        Route::controller(StaffController::class)->group(function () {
            Route::get('/staff', 'index')->name('admin.staff.index');
            Route::get('/staff/create', 'create')->name('admin.staff.create');
            Route::post('/staff', 'store')->name('admin.staff.store');
            Route::get('/staff/{id}/edit', 'edit')->name('admin.staff.edit');
            Route::put('/staff/{id}', 'update')->name('admin.staff.update');
            Route::delete('/staff/{id}', 'destroy')->name('admin.staff.destroy');
        });
    });
});
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
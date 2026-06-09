<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan profil customer dengan statistik belanja.
     * * @return View
     */
    public function index(): View
    {
        // 1. Ambil data user yang sedang login
        $user = Auth::user();

        // 2. Kalkulasi statistik belanja (Best practice: Ambil dari model Transaction)
        // Kita hanya menghitung 'success' atau 'settlement' untuk spending
        $totalSpending = Transaction::where('user_id', $user->id)
            ->paid() 
            ->sum('total_price');

        // 3. Hitung total semua order (termasuk pending/failed untuk riwayat)
        $totalOrders = Transaction::where('user_id', $user->id)->count();

        // 4. Ambil riwayat transaksi terbaru (Eager Load product jika ada relasi)
        $transactions = Transaction::where('user_id', $user->id)
            ->with('product') // Pastikan ada relasi 'product' di model Transaction
            ->latest()
            ->take(5)
            ->get();

        // 5. Kirim ke view dengan compact
        return view('store.profile', compact(
            'user',
            'totalSpending', 
            'totalOrders', 
            'transactions'
        ));
    }
}
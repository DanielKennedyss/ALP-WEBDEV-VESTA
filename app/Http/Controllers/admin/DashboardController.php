<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Tambahkan ini untuk manipulasi tanggal

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Menghitung Statistik Utama (Sama seperti sebelumnya)
        $totalRevenue = Transaction::where('status', 'completed')->sum('total_price');
        $totalOrders = Transaction::count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // 2. Mengambil Top Products
        $topProducts = Transaction::select('product_id', DB::raw('SUM(quantity) as units_sold'))
            ->where('status', 'completed')
            ->groupBy('product_id')
            ->orderBy('units_sold', 'desc')
            ->with('product')
            ->take(3)
            ->get();

        // 3. LOGIC BARU: Data untuk Chart (7 Hari Terakhir)
        // Kita ambil data pendapatan harian agar grafik "Sales Intelligence" muncul
        $salesTrend = Transaction::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as daily_revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Pisahkan data menjadi array untuk dikirim ke Chart.js
        $chartLabels = $salesTrend->pluck('date'); // Contoh: ["2026-05-03", "2026-05-04"]
        $chartData = $salesTrend->pluck('daily_revenue'); // Contoh: [500000, 1200000]

        return view('admin.dashboard', compact(
            'totalRevenue', 
            'totalOrders', 
            'avgOrderValue', 
            'topProducts',
            'chartLabels', // Kirim ke view
            'chartData'    // Kirim ke view
        ));
    }
}
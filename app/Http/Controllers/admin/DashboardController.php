<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Admin (Sales Intelligence)
     */
    public function index(): View
    {
        // 1. Tentukan Range Waktu (Menggunakan helper now() yang lebih bersih)
        $now = now();
        $thirtyDaysAgo = now()->subDays(30);
        $sixtyDaysAgo = now()->subDays(60);

        // 2. Data Periode Sekarang (Current Period)
        // BEST PRACTICE: Gunakan scope paid() dari Model daripada hardcode 'status'
        $totalRevenue = Transaction::paid()
            ->whereBetween('created_at', [$thirtyDaysAgo, $now])
            ->sum('total_price');
            
        $totalOrders = Transaction::whereBetween('created_at', [$thirtyDaysAgo, $now])
            ->count();

        // 3. Data Periode Sebelumnya (Last Period) untuk Kalkulasi Growth
        $lastRevenue = Transaction::paid()
            ->whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])
            ->sum('total_price');

        $lastOrders = Transaction::whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])
            ->count();

        // 4. Kalkulasi Persentase Pertumbuhan (Growth)
        $revenueGrowth = $lastRevenue > 0 ? (($totalRevenue - $lastRevenue) / $lastRevenue) * 100 : 0;
        $orderGrowth = $lastOrders > 0 ? (($totalOrders - $lastOrders) / $lastOrders) * 100 : 0;

        // 5. Avg. Order Value (AOV) & Growth
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $lastAOV = $lastOrders > 0 ? $lastRevenue / $lastOrders : 0;
        $aovGrowth = $lastAOV > 0 ? (($avgOrderValue - $lastAOV) / $lastAOV) * 100 : 0;

        // 6. Mengambil Top Products (Berdasarkan transaksi sukses)
        $topProducts = Transaction::select('product_id', DB::raw('SUM(quantity) as units_sold'))
            ->paid() // Menggantikan where('status', 'completed')
            ->groupBy('product_id')
            ->orderByDesc('units_sold') // Lebih rapi dari orderBy('...', 'desc')
            ->with('product.category') 
            ->take(3)
            ->get();

        // 7. Data untuk Chart (7 Hari Terakhir)
        $salesTrend = Transaction::paid()
            ->where('created_at', '>=', now()->subDays(7))
            ->select(   
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as daily_revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Format Label Tanggal agar lebih cantik di Grafik (Misal: "17 May")
        $chartLabels = $salesTrend->map(function ($trend) {
            return Carbon::parse($trend->date)->format('d M');
        })->values();
        
        $chartData = $salesTrend->pluck('daily_revenue');

        return view('admin.dashboard', compact(
            'totalRevenue', 
            'totalOrders', 
            'avgOrderValue', 
            'revenueGrowth', 
            'orderGrowth', 
            'aovGrowth',
            'topProducts',
            'chartLabels',
            'chartData'
        ));
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Exports\SalesReportExport; // Import class export
use Maatwebsite\Excel\Facades\Excel; // Import facade excel

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Admin (Sales Intelligence)
     */
    public function index(Request $request): View
    {
        // 1. Tangkap Parameter Periode (Default: 30 Hari)
        $period = (int) $request->input('period', 30);
        $validPeriods = [1, 7, 30];
        
        // Proteksi jika user usil mengganti parameter di URL
        if (!in_array($period, $validPeriods)) {
            $period = 30;
        }

        $now = now();

        // 2. Setup Rentang Waktu (Current & Previous)
        if ($period === 1) {
            // Jika "Today" (1 Hari)
            $currentStartDate = now()->startOfDay();
            $previousStartDate = now()->subDay()->startOfDay();
            $previousEndDate = now()->subDay()->endOfDay();
        } else {
            // Jika 7 Hari atau 30 Hari
            $currentStartDate = now()->subDays($period)->startOfDay();
            $previousStartDate = now()->subDays($period * 2)->startOfDay();
            $previousEndDate = now()->subDays($period)->startOfDay();
        }

        // 3. Data Periode Sekarang (Current Period) - FIXED: Ditambahkan filter paid() agar sinkron
        $totalRevenue = Transaction::paid()
            ->whereBetween('created_at', [$currentStartDate, $now])
            ->sum('total_price');
            
        $totalOrders = Transaction::paid()
            ->whereBetween('created_at', [$currentStartDate, $now])
            ->count();

        // 4. Data Periode Sebelumnya (Last Period) untuk Kalkulasi Growth - FIXED: Ditambahkan filter paid()
        $lastRevenue = Transaction::paid()
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('total_price');

        $lastOrders = Transaction::paid()
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->count();

        // 5. Kalkulasi Persentase Pertumbuhan (Growth)
        $revenueGrowth = $lastRevenue > 0 ? (($totalRevenue - $lastRevenue) / $lastRevenue) * 100 : 0;
        $orderGrowth = $lastOrders > 0 ? (($totalOrders - $lastOrders) / $lastOrders) * 100 : 0;

        // 6. Avg. Order Value (AOV) & Growth
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $lastAOV = $lastOrders > 0 ? $lastRevenue / $lastOrders : 0;
        $aovGrowth = $lastAOV > 0 ? (($avgOrderValue - $lastAOV) / $lastAOV) * 100 : 0;

        // 7. Mengambil Top Products (Disaring berdasarkan periode waktu)
        $topProducts = Transaction::select('product_id', DB::raw('SUM(quantity) as units_sold'))
            ->paid()
            ->whereBetween('created_at', [$currentStartDate, $now])
            ->groupBy('product_id')
            ->orderByDesc('units_sold')
            ->with('product.category') 
            ->take(3)
            ->get();

        // 8. INTEGRASI: Dynamic Intelligence AI Insight Engine
        $topProduct = $topProducts->first();
        $contributionPercentage = 0;
        $insightText = "";

        if ($topProduct && $totalRevenue > 0) {
            // Kalkulasi matematis kontribusi nominal rupiah dari produk terlaris
            $topProductRevenue = $topProduct->units_sold * ($topProduct->product->price ?? 0);
            $contributionPercentage = ($topProductRevenue / $totalRevenue) * 100;

            // Algoritma penentuan narasi rekomendasi bisnis berbasis kondisi data riil
            if ($contributionPercentage > 50) {
                $insightText = "Kategori <strong>" . ($topProduct->product->category->name ?? 'Clothing') . "</strong> mendominasi secara absolut dengan menguasai " . number_format($contributionPercentage, 0) . "% dari total revenue. Struktur bisnis VESTA saat ini mengalami indikasi 'Over-reliance' pada satu produk tunggal. Pertimbangkan restrukturisasi alokasi modal iklan ke varian koleksi lain demi mereduksi risiko tumpukan inventaris mati.";
            } else {
                $insightText = "Aliran distribusi penjualan periode ini berjalan sangat stabil dan sehat. Produk utama kontemporer berkontribusi sebesar " . number_format($contributionPercentage, 0) . "% dari total revenue. Struktur portofolio ini aman dari risiko dominasi tunggal. Amankan kontinuitas suplai untuk kain varian <strong>" . $topProduct->product->name . "</strong> guna mempertahankan traksi pasar pekan depan.";
            }
        } else {
            $insightText = "Sistem mesin kecerdasan analitik belum mendeteksi volume transaksi riil yang signifikan pada rentang waktu ini untuk menyusun rekomendasi teori. Lakukan simulasi transaksi sukses untuk memicu kalkulasi matriks operasional.";
        }

        // 9. Data untuk Chart (Menyesuaikan Hari vs Jam)
        if ($period === 1) {
            // Chart Real-time Hari Ini (Group By Hour)
            $salesTrend = Transaction::paid()
                ->whereBetween('created_at', [$currentStartDate, $now])
                ->select(   
                    DB::raw('HOUR(created_at) as time_group'),
                    DB::raw('SUM(total_price) as daily_revenue')
                )
                ->groupBy('time_group')
                ->orderBy('time_group')
                ->get();

            // Format Label Jam (Misal: "08:00")
            $chartLabels = $salesTrend->map(function ($trend) {
                return str_pad($trend->time_group, 2, '0', STR_PAD_LEFT) . ':00';
            })->values();

        } else {
            // Chart 7 Hari / 30 Hari (Group By Date)
            $salesTrend = Transaction::paid()
                ->whereBetween('created_at', [$currentStartDate, $now])
                ->select(   
                    DB::raw('DATE(created_at) as time_group'),
                    DB::raw('SUM(total_price) as daily_revenue')
                )
                ->groupBy('time_group')
                ->orderBy('time_group')
                ->get();

            // Format Label Tanggal (Misal: "17 May")
            $chartLabels = $salesTrend->map(function ($trend) {
                return Carbon::parse($trend->time_group)->format('d M');
            })->values();
        }
        
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
            'chartData',
            'contributionPercentage', // Lempar variabel kontribusi riil ke view dashboard
            'insightText'             // Lempar teks rekomendasi dinamis ke view dashboard
        ));
    }

    /**
     * Memproses eksekusi download laporan sales intelijen (Excel)
     */
    public function export(Request $request)
    {
        // Tangkap parameter period dari request JavaScript di Blade (default: 30)
        $period = $request->input('period', 30);
        
        // Susun nama file luxury yang rapi menggunakan timestamp saat ini
        $fileName = 'VESTA_Sales_Report_' . now()->format('Ymd_His') . '.xlsx';

        // Lempar data period ke constructor SalesReportExport dan trigger download
        return Excel::download(new SalesReportExport($period), $fileName);
    }
}
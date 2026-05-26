<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Exports\SalesReportExport; // Tambahan: Import class export
use Maatwebsite\Excel\Facades\Excel; // Tambahan: Import facade excel

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

        // 3. Data Periode Sekarang (Current Period)
        $totalRevenue = Transaction::paid()
            ->whereBetween('created_at', [$currentStartDate, $now])
            ->sum('total_price');
            
        $totalOrders = Transaction::whereBetween('created_at', [$currentStartDate, $now])
            ->count();

        // 4. Data Periode Sebelumnya (Last Period) untuk Kalkulasi Growth
        $lastRevenue = Transaction::paid()
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('total_price');

        $lastOrders = Transaction::whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->count();

        // 5. Kalkulasi Persentase Pertumbuhan (Growth)
        $revenueGrowth = $lastRevenue > 0 ? (($totalRevenue - $lastRevenue) / $lastRevenue) * 100 : 0;
        $orderGrowth = $lastOrders > 0 ? (($totalOrders - $lastOrders) / $lastOrders) * 100 : 0;

        // 6. Avg. Order Value (AOV) & Growth
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $lastAOV = $lastOrders > 0 ? $lastRevenue / $lastOrders : 0;
        $aovGrowth = $lastAOV > 0 ? (($avgOrderValue - $lastAOV) / $lastAOV) * 100 : 0;

        // 7. Mengambil Top Products (Disaring berdasarkan periode waktu juga)
        $topProducts = Transaction::select('product_id', DB::raw('SUM(quantity) as units_sold'))
            ->paid()
            ->whereBetween('created_at', [$currentStartDate, $now]) // Filter waktu agar akurat
            ->groupBy('product_id')
            ->orderByDesc('units_sold')
            ->with('product.category') 
            ->take(3)
            ->get();

        // 8. Data untuk Chart (Menyesuaikan Hari vs Jam)
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
            'chartData'
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
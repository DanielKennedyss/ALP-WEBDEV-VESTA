<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data produk terbaru atau top products untuk dashboard
        $top_products = Product::orderBy('stock', 'asc')->take(3)->get(); // Sementara pake stock terendah sebagai 'top'
        
        return view('admin.dashboard', compact('top_products'));
    }
}
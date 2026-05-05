<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // 1. FUNGSI INDEX: Untuk menampilkan halaman tabel Inventory
    public function index()
    {
        // Mengambil semua data produk dari database
        $products = Product::all(); 
        
        // Melempar data ke file resources/views/admin/products/index.blade.php
        return view('admin.products.index', compact('products'));
    }

    // 2. FUNGSI CREATE: Untuk menampilkan form tambah produk
    public function create()
    {
        return view('admin.products.create');
    }

    // 3. FUNGSI STORE: Untuk menyimpan data dan gambar ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('product_image'), $filename);
            $data['image_path'] = $filename;
        }

        Product::create($data);

        return redirect('/admin/inventory')->with('success', 'Product added to VESTA Inventory.');
    }
}
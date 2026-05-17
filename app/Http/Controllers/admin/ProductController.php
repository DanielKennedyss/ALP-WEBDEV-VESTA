<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // 1. FUNGSI INDEX: Untuk menampilkan halaman tabel Inventory
    public function index()
    {
        // Eager load category dan variants agar tidak N+1 query
        $products = Product::with(['category', 'variants'])->get();
        
        return view('admin.products.index', compact('products'));
    }

    // 2. FUNGSI CREATE: Untuk menampilkan form tambah produk
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    // 3. FUNGSI STORE: Untuk menyimpan data dan gambar ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'gender' => 'required|string|in:Male,Female,Unisex',
            'size_type' => 'required|string|in:one_size,custom',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Validasi tambahan untuk custom sizes
        if ($request->size_type === 'custom') {
            $request->validate([
                'sizes' => 'required|array|min:1',
                'sizes.*' => 'in:S,M,L,XL',
            ], [
                'sizes.required' => 'Pilih minimal satu ukuran untuk Custom Size.',
                'sizes.min' => 'Pilih minimal satu ukuran untuk Custom Size.',
            ]);

            // Validasi stock dan minimum_stock per size
            foreach ($request->sizes as $size) {
                $request->validate([
                    "stock_{$size}" => 'required|integer|min:0',
                    "min_stock_{$size}" => 'required|integer|min:1',
                ]);
            }
        } else {
            // One Size: validasi stock dan min stock
            $request->validate([
                'stock_one_size' => 'required|integer|min:0',
                'min_stock_one_size' => 'required|integer|min:1',
            ]);
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('product_image'), $filename);
            $imagePath = $filename;
        }

        // Description fallback
        $description = $request->description;
        if (empty($description)) {
            $description = 'No description available.';
        }

        // Generate SKU
        $count = Product::withTrashed()->count() + 1;
        $category = Category::find($request->category_id);
        $categoryCode = strtoupper(substr($category->name, 0, 3));
        $sku = 'VST-' . $categoryCode . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Create product
        $product = Product::create([
            'name' => $request->name,
            'sku' => $sku,
            'description' => $description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'gender' => $request->gender,
            'image_path' => $imagePath,
        ]);

        // Create variants
        if ($request->size_type === 'custom') {
            foreach ($request->sizes as $size) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size_label' => $size,
                    'stock' => $request->input("stock_{$size}", 0),
                    'minimum_stock' => $request->input("min_stock_{$size}", 5),
                ]);
            }
        } else {
            // One Size
            ProductVariant::create([
                'product_id' => $product->id,
                'size_label' => 'One Size',
                'stock' => $request->input('stock_one_size', 0),
                'minimum_stock' => $request->input('min_stock_one_size', 5),
            ]);
        }

        return redirect('/admin/inventory')->with('success', 'Product added to VESTA Inventory.');
    }
}
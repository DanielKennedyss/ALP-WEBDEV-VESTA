<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * 1. INDEX: Menampilkan inventory.
     * Dapat diakses oleh Owner, Manager, dan Staff.
     */
    public function index()
    {
        $products = Product::with(['category', 'variants'])->orderBy('created_at', 'desc')->get();
        return view('admin.products.index', compact('products'));
    }

    /**
     * 2. CREATE: Form tambah produk.
     * Hanya Owner & Manager. Staff dilarang.
     */
    public function create()
    {
        if (Auth::user()->role === 'staff') {
            return redirect()->back()->with('error', 'Akses ditolak. Staff hanya diizinkan mengelola stok yang ada.');
        }

        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * 3. STORE: Simpan ke database.
     * Hanya Owner & Manager.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role === 'staff') {
            abort(403, 'Unauthorized.');
        }

        $this->validateProduct($request);

        // Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('product_image'), $filename);
            $imagePath = $filename;
        }

        // Generate SKU Otomatis
        $count = Product::withTrashed()->count() + 1;
        $category = Category::find($request->category_id);
        $categoryCode = strtoupper(substr($category->name, 0, 3));
        $sku = 'VST-' . $categoryCode . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Create Product Base
        $product = Product::create([
            'name' => $request->name,
            'sku' => $sku,
            'description' => $request->description ?? 'No description available.',
            'price' => $request->price,
            'weight' => $request->weight,
            'category_id' => $request->category_id,
            'gender' => $request->gender,
            'image_path' => $imagePath,
        ]);

        // Simpan Variants (Size & Stock)
        $this->saveVariants($request, $product);

        return redirect('/admin/inventory')->with('success', 'New piece added to VESTA Inventory.');
    }

    /**
     * 4. EDIT: Form ubah data.
     * Owner, Manager, & Staff (Staff biasanya butuh akses edit untuk update stok).
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('variants');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * 5. UPDATE: Update database.
     */
    public function update(Request $request, Product $product)
    {
        // Validasi Role: Staff HANYA boleh update jika dia mengedit produk miliknya sendiri 
        // atau jika bisnis mengizinkan staff update stok.
        $this->validateProduct($request);

        // Handle Image Update
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->image_path && File::exists(public_path('product_image/' . $product->image_path))) {
                File::delete(public_path('product_image/' . $product->image_path));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('product_image'), $filename);
            $product->image_path = $filename;
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description ?? 'No description available.',
            'price' => $request->price,
            'weight' => $request->weight,
            'category_id' => $request->category_id,
            'gender' => $request->gender,
            'image_path' => $product->image_path,
        ]);

        // Re-sync Variants: Hapus yang lama, buat yang baru
        $product->variants()->delete();
        $this->saveVariants($request, $product);

        return redirect('/admin/inventory')->with('success', 'Piece updated successfully.');
    }

    /**
     * 6. DESTROY: Hapus produk.
     * KERAS: Hanya Owner & Manager. Staff dilarang delete.
     */
    public function destroy(Product $product)
    {
        if (Auth::user()->role === 'staff') {
            return redirect()->back()->with('error', 'Akses ditolak. Staff tidak memiliki otoritas untuk menghapus database produk.');
        }

        // Hapus Gambar
        if ($product->image_path && File::exists(public_path('product_image/' . $product->image_path))) {
            File::delete(public_path('product_image/' . $product->image_path));
        }

        $product->variants()->delete();
        $product->delete();

        return redirect('/admin/inventory')->with('success', 'Piece permanently removed from inventory.');
    }

    /**
     * HELPER: Validasi agar kode tidak duplikat (DRY)
     */
    private function validateProduct(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'gender' => 'required|string|in:Male,Female,Unisex',
            'size_type' => 'required|string|in:one_size,custom',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($request->size_type === 'custom') {
            $rules['sizes'] = 'required|array|min:1';
            foreach ($request->sizes ?? [] as $size) {
                $rules["stock_{$size}"] = 'required|integer|min:0';
                $rules["min_stock_{$size}"] = 'required|integer|min:1';
            }
        } else {
            $rules['stock_one_size'] = 'required|integer|min:0';
            $rules['min_stock_one_size'] = 'required|integer|min:1';
        }

        return $request->validate($rules);
    }

    /**
     * HELPER: Simpan Variabel Stock & Size
     */
    private function saveVariants(Request $request, $product)
    {
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
            ProductVariant::create([
                'product_id' => $product->id,
                'size_label' => 'One Size',
                'stock' => $request->input('stock_one_size', 0),
                'minimum_stock' => $request->input('min_stock_one_size', 5),
            ]);
        }
    }
}
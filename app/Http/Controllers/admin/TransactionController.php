<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi.
     */
    public function index(): View
    {
        // BEST PRACTICE: 
        // 1. Eager load 'user' dan 'product' untuk mencegah N+1 Query.
        // 2. Gunakan latest() sebagai alias dari orderBy('created_at', 'desc').
        // 3. Gunakan paginate() agar performa dashboard admin tetap cepat.
        $transactions = Transaction::with(['product', 'user'])
            ->latest()
            ->paginate(15); 

        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Memperbarui status pesanan dari dashboard Admin.
     */
    public function updateStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        // 1. Sesuaikan validasi dengan ENUM baru di database
        $validated = $request->validate([
            'status' => 'required|in:pending,success,failed,cancelled,expired'
        ]);

        // 2. Update status transaksi
        $transaction->status = $validated['status'];

        // 3. BEST PRACTICE: Catat waktu lunas jika diubah menjadi success
        if ($validated['status'] === 'success' && is_null($transaction->paid_at)) {
            $transaction->paid_at = now();
        }

        // Simpan perubahan ke database
        $transaction->save();

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }
}
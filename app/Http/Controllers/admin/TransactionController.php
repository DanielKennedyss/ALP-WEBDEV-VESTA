<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // Mengambil semua transaksi beserta data produknya (One-to-Many)
        $transactions = Transaction::with('product')->orderBy('created_at', 'desc')->get();
        return view('admin.transactions.index', compact('transactions'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate(['status' => 'required|in:pending,completed,cancelled']);
        
        $transaction->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }
}
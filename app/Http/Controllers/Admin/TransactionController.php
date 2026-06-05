<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\LoyaltyPointHistory; // <--- Wajib di-import untuk log refund poin
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['product', 'user']);

        if ($request->filled('product')) {
            $product = $request->product;
            $query->where(function($q) use ($product) {
                $q->whereHas('product', function($qp) use ($product) {
                    $qp->where('name', 'like', "%{$product}%")
                       ->orWhere('sku', 'like', "%{$product}%");
                })
                ->orWhere('cart_items', 'like', "%{$product}%")
                ->orWhere('invoice_number', 'like', "%{$product}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'processing') {
                $query->whereIn('status', ['processing', 'success', 'settlement', 'paid']);
            } elseif ($status === 'delivered') {
                $query->whereIn('status', ['delivered', 'completed']);
            } elseif ($status === 'cancelled') {
                $query->whereIn('status', ['cancelled', 'failed', 'expired']);
            } else {
                $query->where('status', $status);
            }
        }

        $transactions = $query->latest()->paginate(15)->withQueryString(); 

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.transactions.table_rows', compact('transactions'))->render(),
                'details' => view('admin.transactions.detail_cards', compact('transactions'))->render(),
                'pagination' => $transactions->hasPages() ? $transactions->links('pagination::bootstrap-5')->render() : ''
            ]);
        }

        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Memperbarui status pesanan dari dashboard Admin secara bertahap (Luxury Workflow).
     */
    public function updateStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        // 1. Validasi Input Status Sesuai Alur Logistik Baru VESTA
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,expired,refunded'
        ]);

        $oldStatus = $transaction->status;
        $newStatus = $validated['status'];

        // Jika status yang dipilih sama dengan status saat ini, langsung kembalikan
        if ($oldStatus === $newStatus) {
            return redirect()->back()->with('info', 'No changes were made.');
        }

        // 2. Gunakan Database Transaction untuk memastikan integritas data multi-tabel (Garda Aman)
        DB::beginTransaction();
        try {
            $transaction->status = $newStatus;

            // BEST PRACTICE: Anggap dana lunas (paid_at) saat pesanan mulai dikonfirmasi dan diproses
            if ($newStatus === 'processing' && is_null($transaction->paid_at)) {
                $transaction->paid_at = now();
            }

            // 3. GARDA PENGAMAN REFUND POIN LOYALTY
            // Jika pesanan digagalkan (cancelled/expired/refunded) dari status aktif sebelumnya
            if (in_array($newStatus, ['cancelled', 'expired', 'refunded']) && !in_array($oldStatus, ['cancelled', 'expired', 'refunded'])) {
                
                // Mengambil relasi user yang melakukan transaksi
                $user = $transaction->user;
                
                if ($user && $transaction->points_redeemed > 0) {
                    // Kembalikan poin loyalty ke akun customer
                    $user->increment('loyalty_points', $transaction->points_redeemed);

                    // Catat mutasi pengembalian ke dalam Ledger History
                    LoyaltyPointHistory::create([
                        'user_id'        => $user->id,
                        'transaction_id' => $transaction->id,
                        'type'           => 'refund',
                        'points'         => $transaction->points_redeemed,
                        'description'    => "Points refunded from Admin cancellation/refund on Order #" . $transaction->invoice_number,
                    ]);
                }
            }

            $transaction->save();
            DB::commit();

            return redirect()->back()->with('success', 'Order logistics status updated to ' . strtoupper($newStatus) . ' successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error asli agar mudah di-debug di storage/logs/laravel.log
            Log::error('Admin Update Status Error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Failed to update status: An internal error occurred.');
        }
    }
}
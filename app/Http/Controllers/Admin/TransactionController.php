<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\LoyaltyPointHistory; // <--- Wajib di-import untuk log refund poin
use App\Exports\SalesReportExport; // REVISI: Import class export buatanmu
use App\Mail\FinancialReportMail;  // REVISI: Import class mailable laporan
use Maatwebsite\Excel\Facades\Excel; // REVISI: Import facade Maatwebsite Excel
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; // REVISI EMAIL: Import Facade Mail untuk otentikasi pengiriman

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

            // ==========================================================================
            // REVISI EMAIL: Pemicu Otomatis Email Notifikasi Resi Kurir (Status Shipped)
            // ==========================================================================
            if ($newStatus === 'shipped') {
                try {
                    Mail::to($transaction->customer_email)->send(new \App\Mail\OrderShippedMail($transaction));
                } catch (\Exception $mailEx) {
                    Log::error('Admin Mail Shipped Notification Warning: ' . $mailEx->getMessage());
                }
            }

            return redirect()->back()->with('success', 'Order logistics status updated to ' . strtoupper($newStatus) . ' successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error asli agar mudah di-debug di storage/logs/laravel.log
            Log::error('Admin Update Status Error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Failed to update status: An internal error occurred.');
        }
    }

    /**
     * REVISI OPERASIONAL: Menjana data laporan penjualan berdasarkan periode aktif 
     * lalu menembakkannya langsung ke email owner/admin (Format: HTML / Excel XLSX).
     */
    public function sendReportToEmail(Request $request): RedirectResponse
    {
        // 1. Validasi parameter input penarikan data
        $request->validate([
            'email_target'  => 'required|email',
            'period'        => 'required|in:1,7,30',
            'format_choice' => 'required|in:html,xlsx'
        ]);

        $emailTarget = $request->email_target;
        $period      = $request->period;
        $format      = $request->format_choice;

        try {
            // 2. Instansiasi class SalesReportExport milikmu untuk mendapatkan query data ter-filter
            $exportInstance = new SalesReportExport($period);
            
            // Ambil data transaksi riil dari query bawaan class export kamu
            $transactions = $exportInstance->query()->get();

            $excelRawData = null;
            $filename     = 'VESTA_Sales_Report_' . date('Ymd_His') . '.xlsx';

            // 3. Jika admin memilih format berkas Excel, rakit data binernya ke memori lokal
            if ($format === 'xlsx') {
                $excelRawData = Excel::raw($exportInstance, \Maatwebsite\Excel\Excel::XLSX);
            }

            // 4. Kirim data ke background antrean database server (Asynchronous Dispatch)
           Mail::to($emailTarget)->send(new \App\Mail\FinancialReportMail($format, $period));

            return redirect()->back()->with('success', 'Laporan berhasil di-render dan masuk ke dalam antrean background! Mohon periksa inbox email secara berkala.');

        } catch (\Exception $e) {
            Log::error('Admin Send Report via Email Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses pengiriman laporan: ' . $e->getMessage());
        }
    }
}
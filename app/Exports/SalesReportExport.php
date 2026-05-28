<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SalesReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $period;

    public function __construct($period)
    {
        // Memastikan tipe data bersih
        $this->period = (int) $period;
    }

    public function query()
    {
        // Paksa timezone ke Asia/Jakarta khusus saat runtime penarikan laporan penjualan
        $now = Carbon::now('Asia/Jakarta');
        $endDate = $now->copy()->endOfDay();

        // Tentukan batas awal tanggal berdasarkan pilihan filter admin
        $startDate = match($this->period) {
            1 => $now->copy()->startOfDay(), // Hari ini
            7 => $now->copy()->subDays(7)->startOfDay(), // 7 hari terakhir
            30 => $now->copy()->subDays(30)->startOfDay(), // 30 hari terakhir
            default => $now->copy()->subDays(30)->startOfDay(),
        };

        // ==========================================================================
        // DEBUGGING LOG: Menulis log otomatis untuk memastikan range tanggal worker
        // ==========================================================================
        Log::info('VESTA Export Debugger:', [
            'period_input' => $this->period,
            'start_range'  => $startDate->toDateTimeString(),
            'end_range'    => $endDate->toDateTimeString(),
        ]);

        // Query Utama: Menggunakan rentang waktu Carbon yang presisi
        return Transaction::with(['product', 'user'])
            ->whereIn('status', ['success', 'PAID', 'paid']) // Toleransi variasi penulisan status di database
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Invoice ID',
            'Customer Name',
            'Product Name',
            'Quantity',
            'Total Price',
            'Transaction Date'
        ];
    }

    public function map($transaction): array
    {
        return [
            '#' . $transaction->id,
            $transaction->user->name ?? 'GUEST',
            $transaction->product->name ?? 'DELETED PRODUCT',
            $transaction->quantity . ' PCS',
            'IDR ' . number_format($transaction->total_price, 0, ',', '.'),
            $transaction->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
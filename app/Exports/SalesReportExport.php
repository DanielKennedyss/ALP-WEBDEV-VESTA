<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromQuery, WithHeadings, WithMapping
{
    protected $period;

    /**
     * Constructor untuk menangkap parameter waktu dari DashboardController
     */
    public function __construct($period)
    {
        $this->period = (int) $period;
    }

    /**
     * Query data transaksi secara dinamis sesuai periode yang dipilih admin
     */
    public function query()
    {
        // Tentukan batas tanggal awal berdasarkan kecocokan nilai period
        $startDate = match($this->period) {
            1 => now()->startOfDay(),
            7 => now()->subDays(7)->startOfDay(),
            default => now()->subDays(30)->startOfDay(),
        };

        // Mengambil transaksi yang sudah berstatus sukses/paid
        return Transaction::with(['product', 'user'])
            ->paid() // Menggunakan scope paid() bawaan model kamu
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Menentukan Judul Baris Pertama di File Excel (CAPSLOCK LUXURY STYLE)
     */
    public function headings(): array
    {
        return [
            'TRANSACTION ID',
            'DATE',
            'CUSTOMER NAME',
            'PRODUCT NAME',
            'QUANTITY',
            'TOTAL PRICE',
            'STATUS'
        ];
    }

    /**
     * Memetakan struktur kolom database ke baris spreadsheet secara rapi
     * * @param mixed $transaction
     */
    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->created_at->format('Y-m-d H:i'),
            $transaction->user->name ?? 'GUEST',
            $transaction->product->name ?? 'DELETED PIECE',
            $transaction->quantity . ' PCS',
            'IDR ' . number_format($transaction->total_price, 0, ',', '.'),
            strtoupper($transaction->status)
        ];
    }
}
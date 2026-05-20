<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voucher extends Model
{
    use HasFactory;

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'code',
        'type',
        'reward_value',
        'total_quota',
        'used_quota',
        'expired_at'
    ];

    /**
     * Konversi tipe data otomatis (Casting) saat dipanggil dari database.
     */
    protected function casts(): array
    {
        return [
            'reward_value' => 'decimal:2',
            'total_quota'  => 'integer',
            'used_quota'   => 'integer',
            'expired_at'   => 'datetime', // Otomatis dikonversi menjadi objek Carbon
        ];
    }

    /**
     * HELPER METHOD: Mengecek apakah voucher masih valid secara keseluruhan.
     * Penggunaan di Controller: if ($voucher->isValid()) { ... }
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isSoldOut();
    }

    /**
     * HELPER METHOD: Mengecek apakah voucher sudah melewati batas tanggal kedaluwarsa.
     */
    public function isExpired(): bool
    {
        if (is_null($this->expired_at)) {
            return false;
        }

        return $this->expired_at->isPast();
    }

    /**
     * HELPER METHOD: Mengecek apakah kuota pemakaian voucher sudah habis terpakai.
     */
    public function isSoldOut(): bool
    {
        return $this->used_quota >= $this->total_quota;
    }

    /**
     * HELPER METHOD: Menghitung nilai nominal potongan harga berdasarkan subtotal keranjang.
     * Fungsi ini menjamin fleksibilitas hitungan baik tipe persentase maupun nominal tetap.
     */
    public function calculateDiscount($subtotal): float
    {
        if ($this->type === 'percentage') {
            // Skema persentase: (Subtotal * Nilai Diskon) / 100
            $discount = ($subtotal * $this->reward_value) / 100;
            return min($discount, $subtotal); // Diskon tidak boleh melebihi subtotal
        }

        // Skema fixed nominal: Potongan harga tetap kaku
        return min($this->reward_value, $subtotal);
    }
}
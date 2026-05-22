<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPointHistory extends Model
{
    use HasFactory;

    // Tambahkan user_id dan kolom lainnya di sini
    protected $fillable = [
        'user_id',
        'transaction_id', // Pastikan ini juga ada jika poin dicatat berdasarkan transaksi
        'points',
        'type',           // Misalnya: 'earn', 'redeem', 'refund'
        'description',
    ];
}
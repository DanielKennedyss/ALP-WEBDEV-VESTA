<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'product_id', 'invoice_number', 'quantity', 
        'total_price', 'customer_name', 'customer_email', 
        'payment_url', 'paid_at', 'status'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'paid_at' => 'datetime',
        'quantity' => 'integer',
    ];

    protected static function booted()
    {
        static::updated(function ($transaction) {
            // Logic: Hanya update jika status BERUBAH menjadi 'success'
            if ($transaction->wasChanged('status') && 
                $transaction->status === 'success' && 
                $transaction->getOriginal('status') !== 'success') {
                
                $user = $transaction->user;

                if ($user) {
                    $totalSpending = $user->transactions()->where('status', 'success')->sum('total_price');
                    $earnedPoints = floor($transaction->total_price / 10000);

                    $user->update([
                        'total_spending' => $totalSpending,
                        'loyalty_points' => $user->loyalty_points + $earnedPoints
                    ]);
                }
            }
        });

        static::deleted(function ($transaction) {
            if ($transaction->status === 'success') {
                $user = $transaction->user;
                if ($user) {
                    $user->decrement('total_spending', $transaction->total_price);
                    $lostPoints = floor($transaction->total_price / 10000);
                    $user->decrement('loyalty_points', $lostPoints);
                }
            }
        });
    }

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function scopePaid($query)
    {
        return $query->whereIn('status', ['success', 'settlement', 'paid']);
    }
}
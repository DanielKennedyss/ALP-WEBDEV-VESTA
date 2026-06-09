<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 
        'product_id', 
        'invoice_number', 
        'quantity', 
        'subtotal',          
        'discount_voucher',  
        'discount_points',  
        'points_redeemed',   
        'cart_items', 
        'total_price', 
        'customer_name', 
        'customer_email', 
        'shipping_address',
        'shipping_courier',
        'shipping_service',
        'shipping_cost',
        'payment_url', 
        'paid_at', 
        'status'
    ];

    /**
     * Get the attributes that should be cast. (Laravel 11 Style)
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id'          => 'integer',
            'subtotal'         => 'decimal:2', 
            'discount_voucher' => 'decimal:2', 
            'discount_points'  => 'decimal:2',  
            'points_redeemed'  => 'integer',  
            'total_price'      => 'decimal:2',
            'shipping_cost'    => 'decimal:2',
            'paid_at'          => 'datetime',
            'quantity'         => 'integer',
            'cart_items'       => 'array',
        ];
    }

    /**
     * --------------------------------------------------------------------------
     * MODEL EVENTS (BOOTED)
     * --------------------------------------------------------------------------
     */
    public const PAID_STATUSES = ['success', 'settlement', 'paid', 'processing', 'shipped', 'delivered', 'completed'];

    /**
     * --------------------------------------------------------------------------
     * MODEL EVENTS (BOOTED)
     * --------------------------------------------------------------------------
     */
    protected static function booted(): void
    {
        static::updated(function (Transaction $transaction) {
            $wasPaid = in_array($transaction->getOriginal('status'), self::PAID_STATUSES);
            $isPaid = in_array($transaction->status, self::PAID_STATUSES);

            if ($transaction->wasChanged('status')) {
                $user = $transaction->user;

                if ($isPaid && !$wasPaid) {
                    // --- 1. LOGIKA LOYALTY POINTS & TIERING VESTA ---
                    if ($user) {
                        // Update total spending seumur hidup
                        $lifetimeSpending = $user->transactions()->paid()->sum('total_price');
                        $user->update(['total_spending' => $lifetimeSpending]);

                        // Update Tier Luxury berdasarkan pengeluaran 1 tahun terakhir
                        $currentTier = $user->updateMembershipTier();

                        // Tentukan Divisor berdasarkan tier
                        $pointDivisor = match ($currentTier) {
                            'platinum' => 25000, // VESTA PRIVÉ
                            'gold'     => 30000, // HAUTE CIRCLE
                            'silver'   => 40000, // LA MAISON
                            default    => 50000, // THE ATELIER
                        };

                        // Berikan poin (1 Poin = Rp 1.000 Benefit)
                        $earnedPoints = floor($transaction->total_price / $pointDivisor);
                        $user->increment('loyalty_points', $earnedPoints);
                    }

                    // --- 2. LOGIKA PENGURANGAN STOK INVENTORY ---
                    $cartItems = $transaction->cart_items ?? [];
                    foreach ($cartItems as $item) {
                        if (!empty($item['size'])) {
                            // Jika ada spesifikasi ukuran
                            $variant = ProductVariant::where('product_id', $item['product_id'])
                                ->where('size_label', $item['size'])
                                ->first();
                                
                            if ($variant) {
                                $variant->decrement('stock', $item['quantity']);
                            }
                        } else {
                            // Jika tidak ada ukuran, kurangi dari varian mana saja yang masih ada stok
                            $variants = ProductVariant::where('product_id', $item['product_id'])
                                ->where('stock', '>', 0)
                                ->get();
                                
                            $remaining = $item['quantity'];
                            foreach ($variants as $v) {
                                if ($remaining <= 0) break;
                                $deduct = min($remaining, $v->stock);
                                $v->decrement('stock', $deduct);
                                $remaining -= $deduct;
                            }
                        }
                    }
                } elseif (!$isPaid && $wasPaid) {
                    // --- LOGIKA PEMBATALAN: CABUT SPENDING & POIN YANG DIPEROLEH ---
                    if ($user) {
                        // Hitung ulang total spending (karena transaksi ini statusnya sudah bukan paid lagi)
                        $lifetimeSpending = $user->transactions()->paid()->sum('total_price');
                        $user->update(['total_spending' => $lifetimeSpending]);

                        // Update Tier Luxury
                        $currentTier = $user->updateMembershipTier();

                        $pointDivisor = match ($currentTier) {
                            'platinum' => 25000,
                            'gold'     => 30000,
                            'silver'   => 40000,
                            default    => 50000,
                        };

                        $lostPoints = floor($transaction->total_price / $pointDivisor);
                        $user->decrement('loyalty_points', $lostPoints);
                    }

                    // --- LOGIKA PENGEMBALIAN STOK INVENTORY ---
                    $cartItems = $transaction->cart_items ?? [];
                    foreach ($cartItems as $item) {
                        if (!empty($item['size'])) {
                            $variant = ProductVariant::where('product_id', $item['product_id'])
                                ->where('size_label', $item['size'])
                                ->first();
                            if ($variant) {
                                $variant->increment('stock', $item['quantity']);
                            }
                        } else {
                            // Jika tidak ada ukuran, kembalikan ke varian pertama (atau default)
                            $variant = ProductVariant::where('product_id', $item['product_id'])->first();
                            if ($variant) {
                                $variant->increment('stock', $item['quantity']);
                            }
                        }
                    }
                }
            }
        });

        static::deleted(function (Transaction $transaction) {
            // Jika transaksi sukses/paid dihapus, cabut poin dan spending-nya
            $isPaid = in_array($transaction->status, self::PAID_STATUSES);
            if ($isPaid) {
                $user = $transaction->user;
                if ($user) {
                    $user->decrement('total_spending', $transaction->total_price);
                    
                    // Hitung ulang tier pasca penghapusan
                    $currentTier = $user->updateMembershipTier();
                    
                    $pointDivisor = match ($currentTier) {
                        'platinum' => 25000,
                        'gold'     => 30000,
                        'silver'   => 40000,
                        default    => 50000,
                    };
                    
                    $lostPoints = floor($transaction->total_price / $pointDivisor);
                    $user->decrement('loyalty_points', $lostPoints);
                }

                // Kembalikan stok saat dihapus
                $cartItems = $transaction->cart_items ?? [];
                foreach ($cartItems as $item) {
                    if (!empty($item['size'])) {
                        $variant = ProductVariant::where('product_id', $item['product_id'])
                            ->where('size_label', $item['size'])
                            ->first();
                        if ($variant) {
                            $variant->increment('stock', $item['quantity']);
                        }
                    } else {
                        $variant = ProductVariant::where('product_id', $item['product_id'])->first();
                        if ($variant) {
                            $variant->increment('stock', $item['quantity']);
                        }
                    }
                }
            }
        });
    }

    /**
     * --------------------------------------------------------------------------
     * RELATIONS & SCOPES
     * --------------------------------------------------------------------------
     */

    public function product(): BelongsTo 
    { 
        return $this->belongsTo(Product::class); 
    }
    
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Scope untuk mengambil transaksi yang sudah dibayar
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->whereIn('status', self::PAID_STATUSES);
    }
}
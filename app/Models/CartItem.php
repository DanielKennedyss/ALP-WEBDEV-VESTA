<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'size',
    ];

    /**
     * Get the user that owns the cart item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product associated with the cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Synchronize session cart with database cart for the authenticated user.
     * Merges guest items into database cart and loads database cart into session.
     * 
     * @param \App\Models\User $user
     * @return void
     */
    public static function syncCart($user)
    {
        if (!$user) {
            return;
        }

        // Get standard session cart
        $sessionCart = session()->get('cart', []);

        // 1. Merge session items into database
        if (!empty($sessionCart)) {
            foreach ($sessionCart as $item) {
                // Find existing database item for same user, product, and size
                $dbItem = self::where('user_id', $user->id)
                    ->where('product_id', $item['product_id'])
                    ->where('size', $item['size'])
                    ->first();

                if ($dbItem) {
                    $newQty = $dbItem->quantity + $item['quantity'];
                    
                    // Enforce variant stock limit
                    $product = Product::with('variants')->find($item['product_id']);
                    if ($product) {
                        if ($item['size']) {
                            $variant = $product->variants->where('size_label', $item['size'])->first();
                            $maxStock = $variant ? $variant->stock : 0;
                        } else {
                            $maxStock = $product->total_stock;
                        }
                        $newQty = min($newQty, $maxStock);
                    }

                    $dbItem->update([
                        'quantity' => $newQty
                    ]);
                } else {
                    self::create([
                        'user_id'    => $user->id,
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['quantity'],
                        'size'       => $item['size']
                    ]);
                }
            }
        }

        // 2. Load all consolidated database items back into session cart
        $dbCartItems = self::where('user_id', $user->id)->get();
        $cart = [];
        foreach ($dbCartItems as $dbItem) {
            $product = $dbItem->product;
            if ($product) {
                $cartKey = $dbItem->size ? $dbItem->product_id . '-' . $dbItem->size : (string) $dbItem->product_id;
                $cart[$cartKey] = [
                    'product_id' => $dbItem->product_id,
                    'name'       => $product->name,
                    'price'      => $product->price,
                    'quantity'   => $dbItem->quantity,
                    'size'       => $dbItem->size,
                    'image_path' => $product->image_path,
                ];
            }
        }

        session()->put('cart', $cart);
    }

    /**
     * Overwrites database cart items with the standard session cart state.
     * 
     * @param \App\Models\User $user
     * @return void
     */
    public static function saveSessionCartToDb($user)
    {
        if (!$user) {
            return;
        }

        // Clear existing database items for this user
        self::where('user_id', $user->id)->delete();

        // Save current session cart
        $sessionCart = session()->get('cart', []);
        foreach ($sessionCart as $item) {
            self::create([
                'user_id'    => $user->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'size'       => $item['size']
            ]);
        }
    }
}

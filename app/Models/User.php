<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'phone_number', 
        'avatar',
        'role', 
        'membership_level', 
        'loyalty_points', 
        'total_spending', 
        'status',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed', // Mengotomatiskan hashing string password saat mutation
        'total_spending'    => 'decimal:2',
        'loyalty_points'    => 'integer',
    ];

    /**
     * --------------------------------------------------------------------------
     * RELATIONS
     * --------------------------------------------------------------------------
     */

    /**
     * RELASI: One-to-Many ke model Transaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * RELASI: One-to-Many ke model LoyaltyPointHistory
     */
    public function loyaltyHistories(): HasMany
    {
        return $this->hasMany(LoyaltyPointHistory::class)->latest();
    }

    /**
     * --------------------------------------------------------------------------
     * ACCESSORS & MUTATORS (Laravel Style)
     * --------------------------------------------------------------------------
     */

    /**
     * Accessor: Menghitung total spending khusus 1 tahun terakhir (Annual Spending)
     * Cara panggil di Code: $user->annual_spending
     */
    public function getAnnualSpendingAttribute(): float
    {
        return (float) $this->transactions()
            ->whereIn('status', ['success', 'settlement', 'paid'])
            ->where('created_at', '>=', now()->subYear())
            ->sum('total_price');
    }

    /**
     * Accessor: Mapping nilai database ke Nama Luxury di Tampilan Blade (UI)
     * Cara panggil di Blade: {{ auth()->user()->membership_tier_badge }}
     */
    public function getMembershipTierBadgeAttribute(): string
    {
        return match ($this->membership_level) {
            'platinum' => 'VESTA PRIVÉ',
            'gold'     => 'HAUTE CIRCLE',
            'silver'   => 'LA MAISON',
            default    => 'THE ATELIER',
        };
    }

    /**
     * --------------------------------------------------------------------------
     * BUSINESS LOGIC & HELPERS
     * --------------------------------------------------------------------------
     */

    /**
     * Fungsi Otomatis: Mengevaluasi Annual Spending & memperbarui Tier di Database
     */
    public function updateMembershipTier(): string
    {
        $spending = $this->annual_spending;

        if ($spending >= 30000000) {
            $tier = 'platinum';
        } elseif ($spending >= 15000000) {
            $tier = 'gold';
        } elseif ($spending >= 5000000) {
            $tier = 'silver';
        } else {
            $tier = 'bronze';
        }

        if ($this->membership_level !== $tier) {
            $this->update(['membership_level' => $tier]);
        }

        return $tier;
    }

    /**
     * Role-Based Access Control (RBAC) Helpers
     */
    public function isOwner(): bool 
    { 
        return $this->role === 'owner'; 
    }

    public function isManager(): bool 
    { 
        return $this->role === 'manager'; 
    }

    public function isStaff(): bool 
    { 
        return $this->role === 'staff'; 
    }
    
    public function isCustomer(): bool 
    { 
        return $this->role === 'customer'; 
    }
}
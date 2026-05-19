<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; // Tambahkan ini

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone_number', 'avatar',
        'role', 'membership_level', 'loyalty_points', 'total_spending', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'total_spending' => 'decimal:2',
            'loyalty_points' => 'integer',
        ];
    }

    /**
     * RELASI: User has many Transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Helper functions
    public function isOwner() { return $this->role === 'owner'; }
    public function isManager() { return $this->role === 'manager'; }
    public function isStaff() { return $this->role === 'staff'; }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPointHistory extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_id',
        'type',
        'points',
        'description',
    ];
}

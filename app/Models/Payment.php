<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'subscription_id',
        'amount',
        'currency',
        'status',
        'provider',
        'provider_payment_id',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array'
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // 👈 AGREGA ESTO
        'payment_method',
        'payment_status',
        'bill_number',
        'total',
        'billed_at',
    ];    
}

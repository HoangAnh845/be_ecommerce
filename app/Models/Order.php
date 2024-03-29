<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_address_id',
        'order_code',
        'order_discount_id',
        'note',
        'status',
        'total_amount',
        'payment_method',
        'shipping_method',
        'shipping_fee'
    ];
}

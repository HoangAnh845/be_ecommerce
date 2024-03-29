<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'status',
        'note',
        'name',
        'avatar',
        'image_other',
        'describe',
        'amount',
        'price',
    ];
}



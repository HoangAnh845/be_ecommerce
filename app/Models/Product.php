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
        "tiki_best",
        "genuine",
        "support",
        'avatar',
        'image_other',
        'describe',
        'outstan',
        'amount',
        'price',
    ];

    public function properties()
    {
        return $this->hasMany(Properties::class);
    }
}

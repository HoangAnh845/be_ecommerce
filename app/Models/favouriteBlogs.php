<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class favouriteBlogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_id',
        'reference_type',
    ];
}

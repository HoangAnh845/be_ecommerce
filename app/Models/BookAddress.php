<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "province_id",
        "district_id",
        "wards_id",
        "status",
        "fullname",
        "phone",
        "address",
        "address_type",
    ];
}


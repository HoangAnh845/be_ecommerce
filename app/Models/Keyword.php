<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'postion', // Trang đích cho từ khóa
        // 'search_volume', // Khối lượng tìm kiếm
        // 'competition', // Mức độ cạnh tranh
    ];
}

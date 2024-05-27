<?php 

namespace App\Core\Enums;

class EnumProduct{
    const STATUS = [
        'INSTOCK' => 'Sản phẩm có hàng',
        'OUTOFSTOCK' => 'Sản phẩm hết hàng',
        'COMINGSOON' => 'Sản phẩm sắp về',
        'ONSALE' => 'Sản phẩm giảm giá'
    ];
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $products_total = DB::table('products')->where('category_id', $this->id)->count();
        $category_children = DB::table('categorys')->where('parent_id', $this->id)->get();
        $category_children = CategoryResource::collection($category_children); // dùng collection để chuyển đổi dữ liệu từ object sang mảng
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "avatar" => $this->avatar,
            "products_total" => $products_total,
            "category_children" => $category_children,
        ];
    }
}

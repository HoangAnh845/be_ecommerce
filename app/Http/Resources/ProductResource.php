<?php

namespace App\Http\Resources;

use App\Models\Properties;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
// use P

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $properties = DB::table('properties')->where('product_id', $this->id)->get();
        foreach ($properties as $key => $property) {
            $property = new PropertieResource($property);
            $propertiesResouce[] = $property;
        }
        return [
            "id" => $this->id,
            "category" => $this->category_id,
            "name" => $this->name,
            "describe"=> $this->describe,
            "avatar" => $this->avatar,
            "image_other"=> $this->image_other,
            "total_sell"=> $this->total_sell,
            "tiki_best" => $this->tiki_best,
            "genuine" => $this->genuine,
            "price" => $this->price,
            "note" => $this->note,
            "outstan" => $this->outstan,
            'properties' => $propertiesResouce,
        ]; 
    }
}









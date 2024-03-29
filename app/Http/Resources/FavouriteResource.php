<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\Blog;
use App\Models\Product;

class FavouriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::find($this->user_id);
        $title = $this->reference_type === 'blogs' ? Blog::find($this->reference_id)->title : Product::find($this->reference_id)->name;
        return [
            "id" => $this->id,
            "user" => $user->username,
            "name" => $title,
            "type" => $this->reference_type,
        ];
    }
}

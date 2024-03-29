<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'gender' => $this->gender,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "birthday" => $this->birthday,
            "avatar" => $this->avatar,
            "image_cover" => $this->image_cover,
            "address" => $this->address,
            "city" => $this->city,
            "country" => $this->country,
            "phone" => $this->phone,
        ];
    }
}

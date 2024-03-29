<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use App\Models\Chat;
use App\Models\User;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sender = User::find($this->sender_id);
        $receiver = User::find($this->receiver_id);
        return [
            'message_content' => $this->message_content,
            'sender_id' => $sender->username,
            'receiver_id' => $receiver->username,
        ];
    }
}

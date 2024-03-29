<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Resources\ChatResource;

class ChatController extends Controller
{
    public function index(Request $request)
    {
    
        // Sắp xếp theo thời gian gửi tin nhắn
        $chat = DB::table('chats')
            ->whereIn('sender_id', [$request->sender_id, $request->receiver_id])
            ->whereIn('receiver_id', [$request->sender_id, $request->receiver_id])
            ->get();
        foreach ($chat as $key => $value) {
            $chat = new ChatResource($value);
            $chatResource[] = $chat;
        }
        if ($chat) {
            return response()->json([
                'status' => 200,
                'message' => 'Lấy tin nhắn thành công',
                'data' =>  $chatResource
            ]);
        }
        return response()->json([
            'status' => 400,
            'message' => 'Lấy tin nhắn thất bại'
        ]);
    }

    public function show($id)
    {
        $chat = DB::table('chats')->where('receiver_id', $id)->get();
        foreach ($chat as $key => $value) {
            $chat = new ChatResource($value);
            $chatResource[] = $chat;
        }
        if ($chat) {

            return response()->json([
                'status' => 200,
                'message' => 'Lấy tin nhắn thành công',
                'data' =>  $chatResource
            ]);
        }
        return response()->json([
            'status' => 400,
            'message' => 'Lấy tin nhắn thất bại'
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $chat = Chat::create($input);
        if ($chat) {
            return response()->json([
                'status' => 200,
                'message' => 'Gửi tin nhắn thành công',
                'data' => $chat
            ]);
        }
        return response()->json([
            'status' => 400,
            'message' => 'Gửi tin nhắn thất bại'
        ]);
    }

    // public function destroy($id)
    // {
    //     return 'deleteChats';
    // }
}

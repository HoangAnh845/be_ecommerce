<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Laravel\Passport\Client;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function getAllClients()
    {
        $clients = DB::table('oauth_clients')->get();
        return Response(['status' => true, 'data' => $clients], 200);
    }

    public function getClient(Request $request)
    {
        $client = DB::table('oauth_clients')->where("id", $request->id)->get();
        return Response(['status' => true, 'data' => $client], 200);
    }

    public function addClient(Request $request): Response
    {
        $client = new Client;
        $client->name = $request['name'];
        $client->type = $request['type'];
        $client->secret = Str::random(40); // Tạo một "secret" ngẫu nhiên
        $client->redirect = $request['redirect'];
        $client->personal_access_client = true;
        $client->password_client = false;
        $client->revoked = false;
        $client->user_id = null; // or any specific user id
        $client->save();
        return Response(['status' => true, 'data' => $client], 200);
    }

    public function updateClient(Request $request)
    {
        $lock = $request->input('personal_access_client') === null || $request->input('personal_access_client') !== "1" ? true : false;
        $client = Client::find($request->id);
        $client->personal_access_client = $lock;
        $client->save();
        return Response(['status' => true, 'message' => "Update client thành công", "data"=>$lock], 200);
        // $client->name = $request['name'];
        // $client->type = $request['type'];
        // $client->redirect = $request['redirect'];
        // $client->password_client = false;
        // $client->revoked = false;
        // $client->user_id = null; // or any specific user id
    }

    public function deleteClient(Request $request)
    {
        $client = Client::find($request->id);
        $client->delete();
        return Response(['status' => true, 'message' => 'Xóa client thành công'], 200);
    }
}

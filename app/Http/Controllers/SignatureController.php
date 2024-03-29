<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\DB;

class SignatureController extends Controller
{
    // public function handleAuth(Request $request)
    // {
    //     $data = $request->all();

    //     // Tạo lại chuỗi đầu vào từ dữ liệu nhận được
    //     $inputString = $data['timestamp'] . $data['clientId']; // Thêm bất kỳ dữ liệu nào khác bạn đã gửi

    //     // Tìm client dựa trên clientId nhận được
    //     $client = Client::find($data['clientId']);
    //     // Tạo lại chữ ký từ chuỗi đầu vào và secret của client
    //     // hash_hmac: Tạo một mã băm sử dụng thuật toán băm được chỉ định
    //     $signature_received = hash_hmac('sha256', $inputString, $client->secret);
    //     // dd(['data'=>$data,'signature_received'=>$signature_received]);
    //     // dd($client->personal_access_client);
    //     // So sánh chữ ký nhận được với chữ ký đã tạo
    //     // Và nếu chúng không bị khóa
    //     if (!$client->personal_access_client) {
    //         return response()->json(['message' => 'Truy cập của bạn đã bị vô hiệu'], 200);
    //     } else if ($signature_received === $data['signature']) {
    //         // Chữ ký hợp lệ, tiếp tục xử lý yêu cầu
    //         return response()->json(['message' => 'Chữ ký hợp lệ'], 200);
    //     } else {
    //         // Chữ ký không hợp lệ, từ chối yêu cầu
    //         return response()->json(['message' => 'Chữ ký không hợp lệ'], 403); // 403 là mã lỗi HTTP
    //     }
    // }

    public function createSignature(Request $request)
    {
        $data = [
            'timestamp' => time(),
            'clientId' => $request->id,
        ];

        // Tạo chuỗi đầu vào bằng cách nối các giá trị trong $data
        $inputString = implode('', $data);

        // Tìm client dựa trên clientId nhận được
        $client = Client::find($data['clientId']);
        $secret = $client->secret;

        // Tạo chữ ký bằng cách băm chuỗi đầu vào cùng với secret
        $signature = hash_hmac('sha256', $inputString, $secret);

        // Thêm chữ ký vào dữ liệu
        $data['signature'] = $signature;

        // DB::table('api_signatures')->insert([
        //     'client_id' => $request->id,
        //     'client_secret' => $secret,
        //     'signature' => $signature,
        //     'timestamp' => $data['timestamp']
        // ]);

        if ($client->personal_access_client) { // Nếu truy cập của bạn không bị vô hiệu
            return response()->json($data, 200);
        } else {
            return response()->json(['message' => "Không thể tạo chữ ký"], 500);
        }
    }

    public function getSignature(Request $request)
    {
        $signature = DB::table('api_signatures')->where('client_id', $request->id)->get();
        return response()->json(['status' => true, "data" => $signature], 200);
    }
}

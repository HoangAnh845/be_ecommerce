<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Passport\Client;

class CheckSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-Signature');
        $clientId = $request->header('X-Client-Id');
        $client = Client::find($clientId);
        // $query = DB::table('api_signatures')->where('client_id', $clientId)->get();
        $timestamp = $request->header('X-Timestamp');;

        // Tạo lại chuỗi đầu vào từ dữ liệu nhận được
        $inputString = $timestamp . $clientId; // Thêm bất kỳ dữ liệu nào khác bạn đã gửi

        // Tạo lại chữ ký từ chuỗi đầu vào và secret của client
        // hash_hmac: Tạo một mã băm sử dụng thuật toán băm được chỉ định
        $signature_received = hash_hmac('sha256', $inputString, $client->secret);
        // So sánh chữ ký nhận được với chữ ký đã tạo
        // Và nếu chúng không bị khóa
        if (!$client->personal_access_client) {
            return response()->json(['status' => false, 'message' => 'Truy cập của bạn đã bị vô hiệu'], 200);
        } else if ($signature_received === $signature) {
            // Chữ ký hợp lệ, tiếp tục xử lý yêu cầu
            // return response()->json(['status' => false,'message' => 'Chữ ký hợp lệ'], 200);
            return $next($request);
        } else {
            // Chữ ký không hợp lệ, từ chối yêu cầu
            return response()->json(['status' => false, 'message' => 'Chữ ký không hợp lệ'], 403); // 403 là mã lỗi HTTP
        }
    }
}

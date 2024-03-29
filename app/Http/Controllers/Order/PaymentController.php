<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Order;
use App\Models\Payment;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    public function store(Request $request) //: Response
    {
        // Tạo phiên thanh toán cho từng sản phẩm trong đơn hàng
        // Kiểm tra xem đơn hàng có tồn tại không?
        $order = Order::find($request->order_id);
        // Kiểm tra xem phiên thanh toán đã tồn tại chưa?
        $paymentQuery = DB::table('payment_orders')->where('transaction_id', $request->transaction_id)->where('order_id', $request->order_id)->first();
        // dd($order, $paymentQuery);
        if ($order && !$paymentQuery) {
            $payment = DB::table('payment_orders')->insert($request->all());
            // Lấy số lượng transactions có trạng thái BEINGSHIPPED
            if ($payment) {
                // Tìm kiếm các phiên giao dịch của đơn hàng -> update lại trạng thái đơn hàng đã thanh toán
                $transaction = DB::table('transactions')->where('id', $request->transaction_id)->where('order_id', $request->order_id);
                
                // Update trạng thái transactions thành đã được giao hàng
                $transaction->update(['status' => 'BEINGSHIPPED']);

                // Kiểm tra xem các phiên giao dịch của đơn hàng đã được giao hàng hết chưa?
                // Thì update trạng thái đơn hàng thành COMPLETED
                $transactionCount = DB::table('transactions')->where('order_id', $request->order_id)->count();
                $transaction_beingshipped = DB::table('transactions')->where('order_id', $request->order_id)->where('status', 'BEINGSHIPPED')->count();

                if ($transactionCount == $transaction_beingshipped) {
                    $order = DB::table('orders')->where('id', $request->order_id);
                    $order->update(['status' => 'COMPLETED']);
                    $order = new OrderResource($order->get()->toArray()[0]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Đơn hàng đã được hoàn thành',
                        'data' => $order,
                    ]);
                }else{
                    $transaction = new TransactionResource($transaction->get()->toArray()[0]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Tạo thanh toán thành công',
                        'data' => $transaction,
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Tạo thanh toán thất bại',
            ]);
        }
    }
}

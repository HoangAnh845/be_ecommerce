<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDO;

class OrderController extends Controller
{
    public function show(Request $request) // : Response
    {
        $order = DB::table('orders')->where('user_id', $request->id)->get();
        
        foreach ($order->toArray() as $key => $value) {
            $order = new OrderResource($value);
            $orderResource[] = $order;
        }
        if ($orderResource) {
            return $this->resOrder(Response::HTTP_OK, 'Lấy đơn hàng thành công', ['data' => $orderResource]);
        }
        return $this->resOrder(Response::HTTP_NOT_FOUND, 'Không tìm thấy đơn hàng');
    }

    public function store(Request $request): Response
    {
        /* 
            Xác thực người dùng
            |
            Chọn Sản Phẩm và Thêm vào Giỏ Hàng -> Gửi
                - Hệ thống: kiểm tra tính tồn tại của 3 trường "book_address_id" , "transaction" , "payment_method"
                - Hệ thống: Tạo đơn hàng
                - Hệ thống: Tạo các phiên giao dịch cho từng sản phẩm trong đơn hàng 
                - Hệ thống: Tính toán giá đơn hàng phải trả (Nếu áp dụng mã giảm giá thì là bao nhiêu?) dựa vào các phiên giao dịch
                - Hệ thống: Cập nhật lại giá vào đơn hàng
            |
            Thanh Toán Đơn Hàng 
                - 
            |
            Xác Nhận Đơn Hàng
            |
            Gửi Xác Nhận và Thông Báo
            | 
            Xử Lý Đơn Hàng
            |
            Vận Chuyển và Giao Hàng
            |
            Hoàn Tất Đơn Hàng
        */
        $orders = $request->except('discount_id');
        
        // Xác thực dữ liệu
        $validator = Validator::make($orders, [
            'book_address_id' => 'required',
            'transaction' => 'required',
            'payment_method' => 'required'
        ],[
            'book_address_id.required' => 'Vui lòng chọn địa chỉ nhận hàng',
            'transaction.required' => 'Vui lòng chọn sản phẩm',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán'
        ]);

        if ($validator->fails()) {
            return $this->resOrder(422, 'Lỗi xác thực',['data' => $validator->errors()]);
        }

        $orders['order_code'] = 'DH' . time();
        $discount = $request->only('discount_id');
        // Xử lý dữ liệu giao dịch nhận được từ client
        $transaction = $request->only('transaction');
        $json = str_replace("'", "\"", $transaction['transaction']);
        $transaction = json_decode($json, true);

        

        DB::beginTransaction(); // dùng để bắt đầu một giao dịch

        try {
            unset($orders['transaction']); // Xóa trường transaction ra khỏi mảng orders
            // Tạo đơn hàng
            $order = DB::table('orders')->insertGetId($orders); // insertGetId trả về id của bản ghi vừa tạo

            foreach ($transaction as $key => $value) {
                $quantityNow = $value['quantity'];
                $product = DB::table('products')->where('id', $value['product_id'])->get();
                // Tính toán giá cho số lượng sản phẩm
                $product_price = $product[0]->price * $quantityNow;
                // Thêm thuộc tính vào phiên giao dịch
                $transaction[$key]['order_id'] = $order;
                $transaction[$key]['avatar'] = $product[0]->avatar;
                $transaction[$key]['name'] = $product[0]->name;
                $transaction[$key]['user_id'] = $orders['user_id'];
                $transaction[$key]['price_total'] = $product_price;
            }
            // Tạo các phiên giao dịch
            DB::table('transactions')->insert($transaction);

            // Tính tổng giá đơn hàng hiện tại dựa vào phiên giao dịch 
            $price_total = DB::table('transactions')->where('order_id', $order)->where('status', 'WAITTOPAY')->sum('price_total');

            // Kiểm tra xem đơn hàng có mã giảm giá áp dụng không?
            if (isset($discount['discount_id'])) {
                DB::table('order_discounts')->insert([
                    'order_id' => $order,
                    'discount_id' => $discount['discount_id']
                ]);

                // Mã giảm giá áp dụng
                $discount_total = DB::table('discounts')->where('id', $discount['discount_id'])->value('discount');
                // Tính toán giá đơn hàng sau khi áp dụng mã giảm giá
                $price_total -= $discount_total;
            }

            // Update giá đơn hàng
            DB::table('orders')->where('id', $order)->update(['total_amount' => $price_total]);

            DB::commit(); // Dùng để lưu lại giao dịch

            $orders = new OrderResource(DB::table('orders')->where('id', $order)->first());
            return $this->resOrder(200, 'Tạo đơn hàng thành công', ['data' => $orders]);
        } catch (\Exception $e) {
            DB::rollback(); // Dùng để hủy bỏ giao dịch
            return $this->resOrder(500, 'Lỗi khi tạo đơn hàng', ['data' => $e->getMessage()]);
        }
    }


    protected function resOrder(int $status, string $message, ?array $resource = []): Response
    {
        $result = [
            'status' => $status,
            'message' => $message
        ];
        if (count($resource)) {
            $result = array_merge(
                $result,
                [
                    'data' => $resource['data'],
                ]
            );
        }
        return Response($result, $status);
    }
}
// public function createOrder(Request $request): Response
// {
//     $orders = $request->except('discount_id', 'transaction');
//     $orders['order_code'] = 'DH' . time();
//     // Phiên giao dịch
//     $discount = $request->only('discount_id');
//     $transaction = $request->only('transaction');
//     $json = str_replace("'", "\"", $transaction['transaction']); // Thay thế dấu nháy đơn bằng dấu nháy kép để đảm bảo định dạng JSON hợp lệ
//     $transaction = json_decode($json, true);
//     // B1: Tạo đơn hàng
//     DB::table('orders')->insert($orders);
//     # lấy ra id order vừa tạo
//     $order_id = DB::getPdo()->lastInsertId(); // Lấy ra id order vừa tạo
//     if (isset($order_id)) {
//         // B2: Tạo phiên giao dịch
//         // Thêm thuộc tính order vừa tạo vào phiên giao dịch
//         foreach ($transaction as $key => $value) {
//             $transaction[$key]['order_id'] = $order_id;
//             $transaction[$key]['user_id'] = $orders['user_id'];
//         }
//         // Tạo mối quan hệ giao dịch với đơn hàng
//         $transactionInsert = DB::table('transactions')->insert($transaction);
//         $transaction_id = DB::table('transactions')->where('order_id', $order_id)->get();
//         if (count($transaction_id) > 0) { // Kiểm tra xem có giao dịch nào không?
//             $price_total = DB::table('transactions')->where('order_id', $order_id)->sum('price_total'); // Tính tổng giá đơn hàng
//             // B3: Tạo mối quan hệ mã giảm giá với order
//             // Kiểm tra xem đơn hàng có mã giảm giá áp dụng không?
//             $order_discounts_id = null;
//             if (isset($discount['discount_id'])) {
//                 DB::table('order_discounts')->insert([
//                     'order_id' => $order_id,
//                     'discount_id' => $discount['discount_id']
//                 ]);
//                 $order_discounts_id = DB::getPdo()->lastInsertId(); // Lấy ra id mối quan hệ mã giảm giá với order
//                 // B4: Tính toán sau khi có mã giảm giá thì giá đơn hàng sẽ bao nhiêu -> update lại vào bảng order
//                 $discount = DB::table('discounts')->where('id', $discount['discount_id'])->first();
//                 $discount_total = $discount->discount;
//                 $price_total = intval($price_total) - $discount_total;
//             }
//             // Update giá đơn hàng
//             $orderUpdate = DB::table('orders')->where('id', $order_id)->update(['total_amount' => $price_total, 'order_discount_id' => $order_discounts_id]);
//             if($orderUpdate){
//                 return $this->resOrder(201, 'Tạo đơn hàng thành công', ['data' => $orders]);
//             }

//             // B5: Tạo phiên thanh toán - Update lại trạng thái đơn hàng - Đã thanh toán
//             // Sẽ tạo phiên thanh toán khi nào? - Người dùng nhận được hàng
//             // Làm thế nào biết người dùng nhận được hàng? - Khi shipper sử dụng app để phản hồi lại trạng thái đơn hàng

//         }
//     }

//     return $this->resOrder(201, 'Order created successfully');
// }
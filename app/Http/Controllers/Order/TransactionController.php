<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    public function update(Request $request)
    {
        // Kiểm tra hiện của phiên giao dịch
        $transaction = DB::table('transactions')->find($request->id);
        
        if ($transaction->status === 'WAITTOPAY') {
            $quantityNow = $request->quantity;

            $product = DB::table('products')->where('id', $transaction->product_id)->get();
            // Tính toán lại giá 
            $product_price = $product[0]->price * $quantityNow;


            // Update số lượng, giá cho phiên giao dịch 
            $transactionUpdate = DB::table('transactions')->where('id', $request->id)->update(['quantity' => $request->quantity, 'price_total' => $product_price]);

            // Update lại giá cho đơn hàng
            $price_total = DB::table('transactions')->where('order_id', $transaction->order_id)->sum('price_total');

            // Kiểm tra xem đơn hàng có mã giảm giá áp dụng không?
            $order_discount = DB::table('order_discounts')->where('order_id', $transaction->order_id)->get();
            if ($order_discount->count() > 0) {
                // Tính toán sau khi có mã giảm giá thì giá đơn hàng sẽ bao nhiêu?
                $order_discounts = DB::table('order_discounts')->where('order_id', $transaction->order_id)->value('discount_id');
                $discount_total = DB::table('discounts')->where('id', $order_discounts)->value('discount');
                // Tính toán giá đơn hàng sau khi áp dụng mã giảm giá
                $price_total -= $discount_total;
            }

            // Update lại giá cho đơn hàng
            if($transactionUpdate){
                DB::table('orders')->where('id', $transaction->order_id)->update(['total_amount' => $price_total]);
                return response()->json([
                    'status' => 200,
                    'message' => 'Cập nhật phiên giao dịch thành công',
                ]);
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => 'Cập nhật phiên giao dịch thất bại',
                ]);
            }


            // Nếu trạng thái của phiên giao chờ thanh toán thì được thay đổi số lượng sản phẩm của phiên giao dịch đó 
            // Sau khi thay số lượng sẽ tính toán là giá cho phiên giao dịch và update lại cho đơn hàng
        } else { // Trạng thái còn lại: Đang giao hàng, Đã giao hàng, Đã hủy
            return response()->json([
                'status' => 400,
                'message' => 'Không thể cập nhật phiên giao dịch',
            ]);
        }
    }

    public function destroy(Request $request)
    {
        // Kiểm tra hiện của phiên giao dịch
        $transaction = DB::table('transactions')->where('id', $request->id);
        if ($transaction->value('status') === 'WAITTOPAY') { // Nếu trạng thái của phiên giao dịch là chờ thanh toán thì có thể xóa phiên giao dịch
            // hủy phiên bản
            $transaction->update(['status' => 'CANCELLED']);
            $order_id = $transaction->value('order_id');
            $price_total = DB::table('transactions')->where('order_id', $order_id)->where('status', 'WAITTOPAY')->sum('price_total');

            // Kiểm tra xem đơn hàng có mã giảm giá áp dụng không?
            $order_discount = DB::table('order_discounts')->where('order_id', $order_id)->get();
            if ($order_discount->count() > 0) {
                // Tính toán sau khi có mã giảm giá thì giá đơn hàng sẽ bao nhiêu?
                $order_discounts = DB::table('order_discounts')->where('order_id', $order_id)->value('discount_id');
                $discount_total = DB::table('discounts')->where('id', $order_discounts)->value('discount');
                // Tính toán giá đơn hàng sau khi áp dụng mã giảm giá
                $price_total -= $discount_total;
            }

            // Update lại giá cho đơn hàng
            DB::table('orders')->where('id', $order_id)->update(['total_amount' => $price_total]);
            // Kiểm tra nếu phiên giao dịch của đơn hàng bị hủy hết thì đơn hàng đó sẽ chuyển trạng thái = CANCELED
            // Lấy số lượng phiên giao dịch có trong đơn hàng
            $transactionCount = DB::table('transactions')->where('order_id', $order_id)->count();
            // Lấy số lượng transactions có trạng thái CANCELED
            $transactionCancelled = DB::table('transactions')->where('order_id', $order_id)->where('status', 'CANCELLED')->count();
            if ($transactionCount == $transactionCancelled) {
                DB::table('orders')->where('id', $order_id)->update(['status' => 'CANCELED']);
                return response()->json([
                    'status' => 200,
                    'message' => 'Đơn hàng đã bị hủy',
                ]);
            }
            // Cập lại giá cho đơn hàng
            return response()->json([
                'status' => 200,
                'message' => 'Hủy phiên giao dịch thành công',
            ]);
        } else { // Trạng thái còn lại: Đang giao hàng, Đã giao hàng
            return response()->json([
                'status' => 400,
                'message' => 'Không thể hủy phiên giao dịch',
            ]);
        }
    }
}


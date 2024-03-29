<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transaction = DB::table('transactions')->where('order_id', $this->id)->get();
        foreach($transaction as $key => $value) {
            $transaction = new TransactionResource($value);
            $transactionResource[] = $transaction;
        }
        $order_discounts = DB::table('order_discounts')->where('order_id', $this->id)->first();
        $discount = null;
        if($order_discounts){
            $discount = DB::table('discounts')->where('id', $order_discounts->discount_id)->first();
            $discount = new DiscountResource($discount);
        }
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'book_address_id' => $this->book_address_id,
            'order_code' => $this->order_code,
            'note' => $this->note,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'payment_method' => $this->payment_method,
            'shipping_method' => $this->shipping_method,
            'shipping_fee' => $this->shipping_fee,
            'discount' => $discount,
            'transaction' => $transactionResource ?? [],
        ];
    }
}

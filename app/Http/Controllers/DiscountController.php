<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountRequest;
use App\Http\Resources\DiscountResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = DB::table('discounts')->get();
        foreach ($discounts as $key => $discount) {
            $discounts = new DiscountResource($discount);
            $discountResource[] = $discounts;
        }
        if (count($discountResource) > 0) {
            return $this->resDiscount(Response::HTTP_OK, 'Lấy danh sách giảm giá thành công', ['data' => count($discountResource) > 1 ? $discountResource : $discountResource[0]]);
        }
        return $this->resDiscount(Response::HTTP_NOT_FOUND, 'Không tìm thấy giảm giá');
    }

    public function show(Request $request)
    {
        $discount = DB::table('discounts')->find($request->id);
        $discountResource = new DiscountResource($discount);
        # kiểm tra $discount có mã giảm giá nào không
        if ($discount) {
            return $this->resDiscount(Response::HTTP_OK, 'Lấy giảm giá thành công', ['data' => $discountResource]);
        }
        return $this->resDiscount(Response::HTTP_NOT_FOUND, 'Không tìm thấy giảm giá');
    }

    public function store(DiscountRequest $request)
    {
        $input = $request->all();
        $discount = DB::table('discounts')->insert($input);
        // Lấy ra id của mã giảm giá vừa tạo
        $discount_query = DB::table('discounts')->where('code', $input['code'])->get();
        if ($discount) {
            return $this->resDiscount(Response::HTTP_OK, 'Tạo giảm giá thành công', ['data' => $discount_query]);
        }
        return $this->resDiscount(Response::HTTP_INTERNAL_SERVER_ERROR, 'Tạo giảm giá thất bại');
    }

    public function update(DiscountRequest $request)
    {
        $input = $request->all();
        $discount = DB::table('discounts')->where('id', $request->id)->update($input);
        if ($discount) {
            return $this->resDiscount(Response::HTTP_OK, 'Cập nhật giảm giá thành công', ['data' => $input]);
        }
        return $this->resDiscount(Response::HTTP_INTERNAL_SERVER_ERROR, 'Cập nhật giảm giá thất bại');
    }

    public function destroy(Request $request)
    {
        $discount = DB::table('discounts')->where('id', $request->id)->delete();
        if ($discount) {
            return $this->resDiscount(Response::HTTP_OK, 'Xóa giảm giá thành công');
        }
        return $this->resDiscount(Response::HTTP_INTERNAL_SERVER_ERROR, 'Xóa giảm giá thất bại');
    }

    public function createDiscountShare(Request $request)
    {
        $input = $request->all();
        // Kiểm tra xem mã giảm giá có tồn tại hay không? Nó phải ở trạng thái active và còn hạn sử dụng
        $discount = DB::table('discounts')
            ->where('code', $input['code'])
            ->where('status', 'ACTIVE')
            ->where('expiry', '>', date('Y-m-d'))
            ->get();
        if (count($discount) > 0) {
            // Kiểm tra xem giảm giá này đã được chia sẻ hay chưa?
            $discountShare = DB::table('share_discounts')
                ->where('discount_id', $discount[0]->id)
                ->where('user_id', $input['user_id'])
                ->get();
            if (count($discountShare) === 0) {
                DB::table('share_discounts')->insert([
                    'discount_id' => $discount[0]->id,
                    'user_id' => $input['user_id']
                ]);
                return $this->resDiscount(Response::HTTP_OK, 'Chia sẻ giảm giá thành công');
            }
            return $this->resDiscount(Response::HTTP_BAD_REQUEST, 'Mã giảm giá này đã được chia sẻ');
        }
        return $this->resDiscount(Response::HTTP_INTERNAL_SERVER_ERROR, 'Chia sẻ giảm giá thất bại');
    }

    public function deleteDiscountShare($id)
    {
        $discountShare = DB::table('share_discounts')->where('id', $id)->delete();
        if ($discountShare) {
            return $this->resDiscount(Response::HTTP_OK, 'Xóa chia sẻ giảm giá thành công');
        }
        return $this->resDiscount(Response::HTTP_INTERNAL_SERVER_ERROR, 'Xóa chia sẻ giảm giá thất bại');
    }

    protected function resDiscount(int $status, string $message, ?array $resource = []): Response
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

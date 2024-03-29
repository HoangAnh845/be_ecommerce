<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function show(Request $request)
    {
        $queryReview = DB::table('reviews')->where('product_id', $request->id)->get();
        foreach ($queryReview as $review) {
            $review = new ReviewResource($review);
            $reviewResource[] = $review;
        }
        if (count($reviewResource) > 0) {
            return $this->resReview(Response::HTTP_OK, 'Lấy danh sách đánh giá thành công', [
                'data' => count($reviewResource) === 1 ? $reviewResource[0] : $reviewResource,
            ]);
        }
        return $this->resReview(Response::HTTP_NOT_FOUND, 'Không tìm thấy đánh giá');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        // Mỗi sản phẩm thì chỉ có một đánh giá với một người dùng
        // Kiểm tra user đã có reivew sản phẩm đó chưa?
        $reviewCheck = DB::table('reviews')
            ->where('user_id', $input['user_id'])
            ->where('product_id', $input['product_id'])
            ->get();
        if (count($reviewCheck) === 0) {
            DB::table('reviews')->insert($input);
            return $this->resReview(Response::HTTP_OK, 'Thêm đánh giá thành công',['data' => $input]);
        }
        return $this->resReview(Response::HTTP_BAD_REQUEST, 'Người dùng đã đánh giá sản phẩm này');
    }

    public function destroy(Request $request)
    {
        $queryReview = DB::table('reviews')
            ->where('id', $request->id)
            ->delete();
        if ($queryReview) {
            return $this->resReview(Response::HTTP_OK, 'Xóa đánh giá thành công');
        }
        return $this->resReview(Response::HTTP_BAD_REQUEST, 'Xóa đánh giá thất bại');
    }

    protected function resReview(int $status, string $message, ?array $resource = []): Response
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

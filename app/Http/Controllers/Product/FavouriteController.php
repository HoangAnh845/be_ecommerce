<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavouriteResource;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class FavouriteController extends Controller
{
    public function show(Request $request)
    {
        $queryFavorite = DB::table('favourites')
            ->where('user_id', $request->id)
            ->where('reference_type', 'products')
            ->get();

        $productIds = array_map(function ($object) {
            return $object->reference_id;
        }, $queryFavorite->toArray());
        $products = DB::table('products')->whereIn('id', $productIds)->get();
        if (count($products) > 0){
            return $this->resFavourite(Response::HTTP_OK, 'Lấy danh sách sản phẩm yêu thích thành công', [
                'data' => count($products) > 1 ? $products : $products[0],
            ]);
        }
        return $this->resFavourite(Response::HTTP_NOT_FOUND, 'Không tìm thấy sản phẩm yêu thích');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        // Check xem sản phẩm đã được thêm vào yêu thích chưa
        $favorite_product = DB::table('favourites')
        ->where('user_id', $input['user_id'])
        ->where('reference_id', $input['reference_id'])
        ->where('reference_type', 'products')->get();
        if(count($favorite_product) === 0){ // Chưa có sản phẩm trong danh sách yêu thích
            $queryFavorite = DB::table('favourites')->insert($input);
            $products = DB::table('products')->where('id', $input['reference_id'])->get();            
            if ($queryFavorite) {
                return $this->resFavourite(Response::HTTP_OK, 'Thêm sản phẩm yêu thích thành công', [
                    'data' => $products[0],
                ]);
            }
        }else{
            return $this->resFavourite(Response::HTTP_BAD_REQUEST, 'Sản phẩm đã tồn tại trong danh sách yêu thích');
        }
    }

    public function destroy(Request $request)
    {
        $queryFavorite = DB::table('favourites')
            ->where('reference_id', $request->id)
            ->where('reference_type', 'products')
            ->delete();
        if ($queryFavorite) {
            return $this->resFavourite(Response::HTTP_OK, 'Xóa sản phẩm yêu thích thành công');
        }
        return $this->resFavourite(Response::HTTP_BAD_REQUEST, 'Xóa sản phẩm yêu thích thất bại');
    }

    protected function resFavourite(int $status, string $message, ?array $resource = []): Response
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

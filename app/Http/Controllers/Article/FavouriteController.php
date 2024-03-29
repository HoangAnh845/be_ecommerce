<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavouriteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class FavouriteController extends Controller
{
    public function store(Request $request): Response
    {
        $input = $request->all();
        // Check xem bài viết đã được thêm vào yêu thích đối với người dùng này chưa?
        $favorite_article = DB::table('favourites')
            ->where('user_id', $input['user_id'])
            ->where('reference_id', $input['reference_id'])
            ->where('reference_type', 'articles')->get();

        if (count($favorite_article) === 0) { // Chưa có
            $queryFavourite = DB::table('favourites')->insert($input);
            if ($queryFavourite) {
                return $this->resFavourites(Response::HTTP_OK, 'Thêm bài viết yêu thích thành công', [
                    'data' => $input,
                ]);
            }
        } else {
            return $this->resFavourites(Response::HTTP_BAD_REQUEST, 'Bài viết đã tồn tại trong danh sách yêu thích đối với người dùng này');
        }
    }

    public function show(Request $request)
    {
        $favourite = DB::table('favourites')
            ->where('reference_type', 'articles')
            ->where('user_id', $request->id)->get();

        $articleIds = array_map(function ($object) {
            return $object->reference_id;
        }, $favourite->toArray());
        $articles = DB::table('articles')->whereIn('id', $articleIds)->get();

        $favourite = FavouriteResource::collection($favourite);
        if ($favourite) {
            return $this->resFavourites(Response::HTTP_OK, 'Lấy bài viết yêu thích thành công', [
                'data' => count($articles) > 1 ? $articles : $articles[0],
            ]);
        }
        return $this->resFavourites(Response::HTTP_NOT_FOUND, 'Không tìm thấy bài viết yêu thích');
    }

    public function destroy(Request $request)
    {
        $queryFavorite = DB::table('favourites')
            ->where('reference_id', $request->id)
            ->where('reference_type', 'articles')
            ->delete();
        if ($queryFavorite) {
            return $this->resFavourites(Response::HTTP_OK, 'Xóa bài viết yêu thích thành công');
        }
        return $this->resFavourites(Response::HTTP_BAD_REQUEST, 'Xóa bài viết yêu thích thất bại');
    }

    protected function resFavourites(int $status, string $message, ?array $resource = []): Response
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

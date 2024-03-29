<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{

    public function store(Request $request)
    {
        $input = $request->all();
        $queryComment = DB::table('comments')->insert($input);
        if ($queryComment) {
            return $this->resComment(Response::HTTP_OK, 'Thêm bình luận thành công', [
                'data' => $input,
            ]);
        }
        return $this->resComment(Response::HTTP_BAD_REQUEST, 'Thêm bình luận thất bại');
    }

    public function show(Request $request)
    {
        $comment = DB::table('comments')->where('article_id', $request->id)->get();
        if (count($comment) > 0) {
            return $this->resComment(Response::HTTP_OK, 'Lấy bình luận thành công', [
                'data' => $comment,
            ]);
        }
        return $this->resComment(Response::HTTP_NOT_FOUND, 'Không tìm thấy bình luận');
    }

    public function destroy(Request $request)
    {
        $queryComment = DB::table('comments')->where('id', $request->id)->delete();
        if ($queryComment) {
            return $this->resComment(Response::HTTP_OK, 'Xóa bình luận thành công');
        }
        return $this->resComment(Response::HTTP_BAD_REQUEST, 'Xóa bình luận thất bại');
    }

    protected function resComment(int $status, string $message, ?array $resource = []): Response
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

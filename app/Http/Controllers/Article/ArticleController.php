<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function index()
    {
        $articles  = DB::table('Articles')->where('status', 'PUBLISHED')->get();
        if (count($articles) > 0) {
            return $this->resArticle(Response::HTTP_OK, 'Lấy danh sách bài viết xuất bản thành công', [
                'data' => $articles,
            ]);
        }
        return $this->resArticle(Response::HTTP_NOT_FOUND, 'Không tìm thấy danh sách  bài viết xuất bản');
    }

    public function show(Request $request)
    {
        $article = DB::table('articles')->where('id', $request->id)->where('status', 'PUBLISHED')->get();
        if (count($article) > 0) {
            // $article = new ArticleResource($article->toArray());
            return $this->resArticle(Response::HTTP_OK, 'Lấy thông tin bài viết xuất bản thành công', ['data' => $article]);
        }
        return $this->resArticle(Response::HTTP_NOT_FOUND, 'Không tìm thấy thông tin bài viết xuất bản');
    }

    public function store(ArticleRequest $request)
    {
        $input = $request->all();
        $queryArticle = DB::table('articles')->insert($input);
        if ($queryArticle) {
            return $this->resArticle(Response::HTTP_OK, 'Thêm bài viết thành công', [
                'data' => $input,
            ]);
        }
        return $this->resArticle(Response::HTTP_BAD_REQUEST, 'Thêm bài viết thất bại');
    }

    public function update(ArticleRequest $request)
    {
        $input = $request->except('status');
        $status = $request->status;
        $queryArticle = DB::table('articles')->where('id', $request->id)->update($input);
        
        // Xuất bản bài viết 
        if($status === 'PUBLISHED') {
            $query = DB::table('articles')->where('id', $request->id)->update(['status' => 'PUBLISHED']);
            if ($query) {
                return $this->resArticle(Response::HTTP_OK, 'Xuất bản bài viết thành công');
            }
            return $this->resArticle(Response::HTTP_BAD_REQUEST, 'Xuất bản bài viết thất bại');
        }

        if ($queryArticle) {
            return $this->resArticle(Response::HTTP_OK, 'Cập nhật bài viết thành công', [
                'data' => $input,
            ]);
        }
        return $this->resArticle(Response::HTTP_BAD_REQUEST, 'Cập nhật bài viết thất bại');
    }

    public function destroy(Request $request)
    {
        $queryArticle = DB::table('articles')
            ->where('id', $request->id)
            ->delete();
        if ($queryArticle) {
            return $this->resArticle(Response::HTTP_OK, 'Xóa bài viết thành công');
        }
        return $this->resArticle(Response::HTTP_BAD_REQUEST, 'Xóa bài viết thất bại');
    }

    
    protected function resArticle(int $status, string $message, ?array $resource = []): Response
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

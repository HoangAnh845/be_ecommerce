<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeywordRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Keyword;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\KeywordResource;
use Illuminate\Support\Facades\Validator;

class KeywordController extends Controller
{
    public function index()
    {
        $keywords = DB::table('keywords')->get();
        $dataKeyWord = [];
        foreach ($keywords->toArray() as $key => $value) {
            $keywords = new KeywordResource($value);
            $dataKeyWord[] = $keywords;
        }

        if ($keywords) {
            return $this->resKeyword(Response::HTTP_OK, 'Lấy danh sách từ khóa thành công', ['data' => $dataKeyWord]);
        }
        return $this->resKeyword(Response::HTTP_NOT_FOUND, 'Không tìm thấy từ khóa');
    }

    public function store(KeywordRequest $request)
    {
        $inputKeyWord = $request->except('category_id');

        $category_id = $request->category_id;
        $keyword = Keyword::create($inputKeyWord); // Tạo từ khóa
        if ($keyword) {
            $keyword_id = $keyword->id;
            // Tạo mối quan hệ giữa từ khóa và danh mục
            DB::table('keyword_categorys')->insert([
                'keyword_id' => $keyword_id,
                'category_id' => $category_id
            ]);
            return $this->resKeyword(Response::HTTP_OK, 'Tạo từ khóa thành công', ['data' => $keyword]);
        }
        return $this->resKeyword(Response::HTTP_BAD_REQUEST, 'Tạo từ khóa thất bại');
    }

    public function update(KeywordRequest $request)
    {
        $input = $request->all();
        $keyword = Keyword::find($request->id);
        if ($keyword) {
            $keyword->update($input);
            return $this->resKeyword(Response::HTTP_OK, 'Cập nhật từ khóa thành công', ['data' => $keyword]);
        }
        return $this->resKeyword(Response::HTTP_BAD_REQUEST, 'Cập nhật từ khóa thất bại');
    }

    public function destroy(Request $request)
    {
        $keyword = Keyword::find($request->id);
        if ($keyword) {
            $keyword->delete();
            return $this->resKeyword(Response::HTTP_OK, 'Xóa từ khóa thành công');
        }
        return $this->resKeyword(Response::HTTP_BAD_REQUEST, 'Xóa từ khóa thất bại');
    }

    public function filter(Request $request)
    {
        // Tìm kiếm danh mục sẽ có bao gồm các từ khóa nào
        $category_id = $request->category_id;
        $keywords = DB::table('keyword_categorys')
            ->where('category_id', $category_id)
            ->join('keywords', 'keyword_categorys.keyword_id', '=', 'keywords.id') // join bảng keywords
            ->select('keywords.id', 'keywords.keyword', 'keywords.postion'); // Lấy ra các trường cần thiết
        if ($keywords->count() > 0) {
            return $this->resKeyword(Response::HTTP_OK, 'Lấy danh sách keyword theo category thành công', ['data' => $keywords->get()]);
        }
        return $this->resKeyword(Response::HTTP_NOT_FOUND, 'Không tìm thấy keyword theo category');
    }

    public function resKeyword(int $status, string $message, ?array $resource = []): Response
    {
        $result = [
            'status' => $status,
            'message' => $message
        ];
        if (count($resource)) {
            $result = array_merge(
                $result,
                $resource
            );
        }
        return response($result, $status);
    }
}

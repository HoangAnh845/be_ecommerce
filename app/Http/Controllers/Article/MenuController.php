<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Menu;
use Illuminate\Support\Facades\Validator;


class MenuController extends Controller
{
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|unique:menus',
        ], [
            'name.required' => 'Tên thể loại không được để trống',
            'name.unique' => 'Tên thể loại đã tồn tại',
        ]);
        if ($validator->fails()) {
            return $this->resMenu(Response::HTTP_BAD_REQUEST, $validator->errors()->first());
        }
        $category = Menu::create($input);
        if ($category) {
            return $this->resMenu(Response::HTTP_OK, 'Tạo thể loại của blog thành công', ['data' => $category]);
        }
        return $this->resMenu(Response::HTTP_BAD_REQUEST, 'Tạo thể loại của blog thất bại');
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|unique:menus',
        ], [
            'name.required' => 'Tên thể loại không được để trống',
            'name.unique' => 'Tên thể loại đã tồn tại',
        ]);
        if ($validator->fails()) {
            return $this->resMenu(Response::HTTP_BAD_REQUEST, $validator->errors()->first());
        }
        $category = Menu::find($request->id);
        if ($category) {
            $category->update($input);
            return $this->resMenu(Response::HTTP_OK, 'Cập nhật thể loại của blog thành công', ['data' => $category]);
        }
        return $this->resMenu(Response::HTTP_NOT_FOUND, 'Không tìm thấy thể loại của blog');
    }

    public function destroy(Request $request)
    {
        $category = Menu::find($request->id);
        if ($category) {
            $category->delete();
            return $this->resMenu(Response::HTTP_OK, 'Xóa thể loại của blog thành công');
        }
        return $this->resMenu(Response::HTTP_NOT_FOUND, 'Không tìm thấy thể loại của blog');
    }

    protected function resMenu(int $status, string $message, ?array $resource = []): Response
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

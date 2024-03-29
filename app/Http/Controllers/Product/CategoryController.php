<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categorys = DB::table('categorys')->where('parent_id', 0)->get();
        foreach ($categorys as $key => $category) {
            $category = new CategoryResource($category);
            $categoryResouce[] = $category;
        }
        if (count($categoryResouce) > 0) {
            return $this->resCategory(Response::HTTP_OK, 'Lấy danh sách danh mục thành công', ['data' => $categoryResouce]);
        }
        return $this->resCategory(Response::HTTP_BAD_REQUEST, 'Không có danh mục nào');
    }

    public function show($id)
    {
        $categorys = DB::table('categorys')->where('parent_id', $id)->get();
        foreach ($categorys as $key => $category) {
            $category = new CategoryResource($category);
            $categoryResouce[] = $category;
        }
        if (count($categoryResouce) > 0) {
            return $this->resCategory(Response::HTTP_OK, 'Lấy danh sách danh mục thành công', ['data' => $categoryResouce]);
        }
        return $this->resCategory(Response::HTTP_BAD_REQUEST, 'Không có danh mục nào');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'parent_id' => 'required',
        ], [
            'name.required' => 'Tên danh mục không được để trống',
            'parent_id.required' => 'Danh mục cha không được để trống',
        ]);
        if ($validator->fails()) {
            return $this->resCategory(Response::HTTP_BAD_REQUEST, $validator->errors()->first());
        }
        $category = DB::table('categorys')->insert($input);
        if ($category) {
            $category = DB::table('categorys')->where('name', $input['name'])->get();
            return $this->resCategory(Response::HTTP_OK, 'Tạo danh mục thành công', ['data' => $category]);
        }
        return $this->resCategory(Response::HTTP_BAD_REQUEST, 'Tạo danh mục thất bại');
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'parent_id' => 'required',
        ], [
            'name.required' => 'Tên danh mục không được để trống',
            'parent_id.required' => 'Danh mục cha không được để trống',
        ]);
        if ($validator->fails()) {
            return $this->resCategory(Response::HTTP_BAD_REQUEST, $validator->errors()->first());
        }
        $category = DB::table('categorys')->where('id', $request->id);
        if ($category) {
            $category->update($input);
            return $this->resCategory(Response::HTTP_OK, 'Cập nhật danh mục thành công', ['data' => $category->get()]);
        }
        return $this->resCategory(Response::HTTP_BAD_REQUEST, 'Cập nhật danh mục thất bại');
    }

    public function destroy(Request $request)
    {
        $category = DB::table('categorys')->where('id', $request->id);
        if ($category) {
            $category->delete();
            return $this->resCategory(Response::HTTP_OK, 'Xóa danh mục thành công');
        }
        return $this->resCategory(Response::HTTP_BAD_REQUEST, 'Xóa danh mục thất bại');
    }

    public function filter(Request $request)
    {
        // Tìm kiếm sản phẩm cho danh mục
        $listProducts = DB::table('products')->where('category_id', $request->id)->get();
        foreach ($listProducts as $key => $product) {
            $listProducts = new ProductResource($product);
            $productResource[] = $listProducts;
        }
        if ($productResource) {
            return $this->resCategory(Response::HTTP_OK, 'Tìm kiếm sản phẩm theo danh mục thành công', ['data' => $productResource]);
        }
        return $this->resCategory(Response::HTTP_BAD_REQUEST, 'Không thấy sản phẩm theo danh mục');
    }

    protected function resCategory(int $status, string $message, ?array $resource = []): Response
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

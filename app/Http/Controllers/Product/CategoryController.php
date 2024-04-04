<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
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
        
        $categoryParent = DB::table('categorys')->where('id', $id)->get();
        $categoryResouce = [];
        $categorysChildren = DB::table('categorys')->where('parent_id', $id)->get();
        foreach ($categorysChildren as $key => $category) {
            $category = new CategoryResource($category);
            $categoryResouce[] = $category;
        }
        // Hợp nhất nhất category cha và category con
        $categoryResouce = array_merge($categoryParent->toArray(), $categoryResouce);


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
        $tiki_best = $request->input('tiki_best') || 0;
        $genuine = $request->input('genuine') || 0;
        // làm thế nào để tìm category cha của các category con trong bảng product
        // Bước 1: Tìm ra tất cả các category có parent_id bằng với $parentId
        // Khi ra được các category con thì tiếp lấy category con đó để tìm ra các category con của nó
        // Lặp lại cho đến khi không còn category con nào nữa
        // $categoryIds = [];
        // $parentId = $request->id;


        $categoryIds = DB::table('categorys')->where('parent_id', $request->id)->pluck('id');
        $mergedArray = collect([]); // Create an empty collection


        $mergedArray = $mergedArray->merge($categoryIds);
        foreach ($categoryIds as $category) {
            // Hợp nhất các category con và category hiện tại
            $mergedArray = $mergedArray->merge(DB::table('categorys')->where('parent_id', $category)->pluck('id'));
        }

        // Tìm ra tất cả các category con của category hiện tại
        $categoryChildren = $mergedArray->toArray();
        if (count($categoryIds) === 0) { // count($categoryChildren) == 0 || 
            $categoryChildren[] = $request->id;
        }
        // dd($categoryChildren[]);
        // Bước 2: Sử dụng các id của các category tìm được để tìm ra các sản phẩm trong bảng product với tiki_best
        $listProducts = Product::whereIn('category_id', $categoryChildren)
            // ->where('genuine', $genuine)
            // ->where('tiki_best', $tiki_best)
            ->get();


        if (count($listProducts) > 0) {
            foreach ($listProducts as $key => $product) {
                $listProducts = new ProductResource($product);
                $productResource[] = $listProducts;
            }
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


// {"name":"Hướng dẫn bảo quản", "value":"Nơi khô ráo thoáng mát, tránh ánh nắng trực tiếp. Sử dụng trong vòng 4 tuần sau khi mở hộp. Đậy kín nắp hộp sau mỗi lần sử dụng."}

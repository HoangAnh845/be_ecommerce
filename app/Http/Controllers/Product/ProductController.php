<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\PropertieResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Product;
use App\Models\Properties;
use App\Models\propertiesProducts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    public function index()
    {
        $products = DB::table('products')->paginate(10);
        $productResource = [];
        foreach ($products as $key => $value) {
            $product = new ProductResource($value);
            $productResource[] = $product;
        }
        if ($productResource) {
            return $this->resProduct(Response::HTTP_OK, 'Lấy danh sách sản phẩm thành công', ['data' => $productResource], ['pagination' => [
                'total' => $products->total(),
                'perPage' => $products->perPage(),
                'currentPage' => $products->currentPage(),
                'lastPage' => $products->lastPage(),
            ]]);
        }
        return $this->resProduct(Response::HTTP_NOT_FOUND, 'Không tìm thấy sản phẩm');
    }

    public function show(Request $request)
    {
        $product = DB::table('products')->where('id', $request->id)->first();

        if ($product) {
            // Tìm kiếm tổng số reivew của sản phẩm
            $queryReview = DB::table('reviews')->where('product_id', $request->id)->count();
            // Cập nhật số lượng review của sản phẩm
            DB::table('products')->where('id', $request->id)->update(['reviews_total' => $queryReview]);
            $product = new ProductResource($product);
            return $this->resProduct(Response::HTTP_OK, 'Lấy sản phẩm thành công', ['data' => $product]);
        }
        return $this->resProduct(Response::HTTP_NOT_FOUND, 'Không tìm thấy sản phẩm');
    }

    public function store(ProductRequest $request)
    {
        $productInput = $request->except('properties');
        $propertiesInput = $request->only('properties');
        $json = str_replace("'", "\"", $propertiesInput['properties']); // Thay thế dấu nháy đơn bằng dấu nháy kép để đảm bảo định dạng JSON hợp lệ
        $array = json_decode($json, true);

        $product = Product::create($productInput);
        foreach ($array as $key => $value) {
            $propertiesItems[] = [
                'product_id' => $product->id,
                'name' => $value['name'],
                'value' => $value['value']
            ];
        }
        // Tạo thuộc tính sản phẩm
        DB::table('properties')->insert($propertiesItems);
        $propertiesDate = DB::table('properties')->where('product_id', $product->id)->get();
        $propertiesProduct = [];
        foreach ($propertiesDate as $key => $value) {
            $propertiesDate = new PropertieResource($value);
            array_push($propertiesProduct, $propertiesDate);
        }
        $product['properties'] = $propertiesProduct;
        if ($product) {
            return $this->resProduct(Response::HTTP_OK, 'Tạo sản phẩm thành công', ['data' => $product], ['pagination' => []]);
        }
    }

    public function update(ProductRequest $request)
    {
        $product = Product::find($request->id);

        if (!$product) {
            return $this->resProduct(Response::HTTP_NOT_FOUND, 'Không tìm thấy sản phẩm');
        }

        $productInput = $request->except('properties');
        $product->update($productInput);

        $propertiesInput = $request->only('properties');
        $json = str_replace("'", "\"", $propertiesInput['properties']); // Thay thế dấu nháy đơn bằng dấu nháy kép để đảm bảo định dạng JSON hợp lệ
        $propertiesNow = json_decode($json);
        $propertiesOld = DB::table('properties')->where("product_id", $request->id)->get()->toArray();

        // array_udiff là hàm loại bỏ các phần tử trùng nhau trong 2 mảng
        // Mảng chứa các đối tượng từ $propertiesNow không xuất hiện trong $propertiesOld dựa trên giá trị của name và value trùng nhau.
        $differenceNow = array_udiff($propertiesNow, $propertiesOld, function ($obj_a, $obj_b) {
            return $obj_a->name . '|' . $obj_a->value <=> $obj_b->name . '|' . $obj_b->value;
        });
        // Mảng chứa các đối tượng từ $propertiesOld không xuất hiện trong $propertiesNow dựa trên giá trị của name và value trùng nhau.
        $differenceOld = array_udiff($propertiesOld, $propertiesNow, function ($obj_a, $obj_b) {
            return $obj_a->name . '|' . $obj_a->value <=> $obj_b->name . '|' . $obj_b->value;
        });


        // Loại bỏ thuộc tính cũ khi thuộc tính hiện tại truyền vào không còn tồn tại so với thuộc tính cũ trong bảng 
        foreach ($differenceOld as $key => $value) {
            // Kiểm tra xem thuộc tính 'name' có tồn tại trong bảng properties không
            $properties = DB::table('properties')->where('product_id', $request->id)->where('name', $value->name);
            if ($properties->count() > 0) { // Xóa thuộc tính cũ khi thuộc tính hiện tại truyền vào không còn tồn tại so với thuộc tính cũ trong bảng 
                $properties->delete();
            }
        }
        foreach ($differenceNow as $key => $value) {
            // Kiểm tra xem thuộc tính 'name' có tồn tại trong bảng properties không
            $properties = DB::table('properties')->where('product_id', $request->id)->where('name', $value->name);
            if ($properties->count() > 0) { // Cập nhật thuộc tính khi value của nó bị thay đổi
                $properties->where('name', $value->name)->update(['value' => $value->value]);
            } else if ($properties->count() === 0) { // Thêm mới thuộc tính khi thuộc tính mới thêm vào chưa tồn tại trong bảng properties
                DB::table('properties')->insert([
                    'product_id' => $request->id,
                    'name' => $value->name,
                    'value' => $value->value
                ]);
            }
        }



        return $this->resProduct(Response::HTTP_OK, 'Cập nhật sản phẩm thành công', ['data' => $product]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return $this->resProduct(Response::HTTP_OK, 'Xóa sản phẩm thành công');
        }
        return $this->resProduct(Response::HTTP_NOT_FOUND, 'Không tìm thấy sản phẩm');
    }

    public function filter(Request $request)
    {
        $queryProduct = DB::table('products');

        // Tìm kiếm theo tên
        if ($request->has('name')) {
            $queryProduct->where('name', 'like', '%' . $request->name . '%');
        }

        // Sắp sếp mới nhất, cũ nhất, tăng giảm theo giá
        if ($request->has('sort_by')) {
            switch ($request->sort_by) {
                case 'newest':
                    $queryProduct->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $queryProduct->orderBy('created_at', 'asc');
                    break;
                case 'price_desc':
                    $queryProduct->orderBy('price', 'desc');
                    break;
                case 'price_asc':
                    $queryProduct->orderBy('price', 'asc');
                    break;
            }
        }

        // Tìm kiếm theo khoảng giá sản phẩm
        if ($request->has('price')) {
            $queryProduct // 
                ->where('price', '>=', json_decode($request->price, true)[0])
                ->where('price', '<=', json_decode($request->price, true)[1]);
        }

        $products = $queryProduct->paginate(10);
        $productResource = [];
        foreach ($products as $key => $value) {
            $product = new ProductResource($value);
            $productResource[] = $product;
        }

        if (count($productResource) > 0) {
            return $this->resProduct(
                Response::HTTP_OK,
                'Lấy sản phẩm thành công',
                ['data' => count($productResource) === 1 ? $productResource[0] : $productResource],
                [
                    'pagination' => [
                        'total' => $products->total(),
                        'perPage' => $products->perPage(),
                        'currentPage' => $products->currentPage(),
                        'lastPage' => $products->lastPage()
                    ]
                ]
            );
        }
        return $this->resProduct(Response::HTTP_NOT_FOUND, 'Không tìm thấy sản phẩm');
    }

    protected function resProduct(int $status, string $message, ?array $resource = [], $pagination = []): Response
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
                    'pagination' => isset($pagination['pagination']) ? $pagination['pagination'] : []
                ]
            );
        }
        return Response($result, $status);
    }
}

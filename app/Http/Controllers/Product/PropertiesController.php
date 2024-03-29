<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Properties;
use Illuminate\Support\Facades\DB;

class PropertiesController extends Controller
{
    public function getPropertiesByKeywrod(Request $request)
    {
        // Tìm kiếm thuộc tính theo từ khóa
    }

    public function createProperties(Request $request)
    {
        $input = $request->all();
        // array_push($valueArray, $input['value']);
        $input['value'] = '["' . $input['value'] . '"]'; 
        $properties = Properties::create($input);
        if ($properties) {
            return $this->resProperties(Response::HTTP_OK, 'Tạo thuộc tính thành công', ['data' => $properties]);
        }
    }

    public function updateProperties(Request $request)
    {
        $input = $request->all();
        $isKeyword = DB::table('properties')->where('id', $request->id)->first();
        // Kiểm tra xem keyword_id có tồn tại không? 
        if ($isKeyword) {
            // Nếu có thì thêm thuộc tính theo keyword_id
            $valueArray = json_decode($isKeyword->value);
            array_push($valueArray, $input['value']);
            $input['value'] = '["' . implode('", "', $valueArray) . '"]';
            $properties = Properties::find($request->id);
            if ($properties) {
                $properties->update($input);
                return $this->resProperties(Response::HTTP_OK, 'Cập nhật thuộc tính thành công', ['data' => $properties]);
            }
        }
        return $this->resProperties(Response::HTTP_BAD_REQUEST, 'Cập nhật thuộc tính thất bại');
    }

    public function deleteProperties(Request $request)
    {
        $properties = Properties::find($request->id);
        if ($properties) {
            $properties->delete();
            return $this->resProperties(Response::HTTP_OK, 'Xóa thuộc tính thành công');
        }
        return $this->resProperties(Response::HTTP_BAD_REQUEST, 'Xóa thuộc tính thất bại');
    }

    public function resProperties(int $status, string $message, ?array $resource = [])
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

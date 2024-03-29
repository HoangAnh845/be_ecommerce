<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookAddressRequest;
use App\Http\Resources\BookAddressResource;
use App\Models\BookAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BookAddressController extends Controller
{
    public function show()
    {
        $query = DB::table('book_address')->get();
        $bookAddressResource = [];
        foreach ($query->toArray() as $key => $value) {
            $query = new BookAddressResource([$value]);
            $bookAddressResource[] = $query;
        } 
        if ($bookAddressResource) {
            return $this->resBookAddress(Response::HTTP_OK, 'Lấy danh sách địa chỉ sách thành công', ['data' => $bookAddressResource]);
        }
        return $this->resBookAddress(Response::HTTP_NOT_FOUND, 'Không tìm thấy địa chỉ sách');
    }

    public function showAccompany(Request $request)
    {
        $input = $request->all();
        $accompany = [];
        if (isset($input['province'])) { // Nếu có truyền vào tỉnh thành thực hiện tìm quận huyện
            $province = DB::table('provinces')->where('province_id', $input['province'])->get();
            $district = DB::table('districts')->where('province_id', $input['province'])->get();
            $accompany = [
                'province' => $province,
                'district' => $district
            ];
        }
        if (isset($input['district'])) { // Nếu có truyền vào quận huyện thực hiện tìm xã phường
            $ward = DB::table('wards')->where('district_id', $input['district'])->get();
            array_push($accompany, ['ward' => $ward]);
        }
        dd($accompany);

        return $this->resBookAddress(Response::HTTP_OK, 'Lấy danh sách địa chỉ đi kèm thành công', ['data' => ""]);
    }

    public function store(BookAddressRequest $request)
    {
        $input = $request->all();
        $bookAddress_insert = DB::table('book_address')->insert($input);
        // Lấy ra id vừa thêm vào
        $bookAddress_id = DB::getPdo()->lastInsertId();
        if ($bookAddress_id) {
            $bookAddress = DB::table('book_address')->where('id', $bookAddress_id)->get();
            $bookAddress = new BookAddressResource($bookAddress);
            return $this->resBookAddress(Response::HTTP_OK, 'Tạo địa chỉ sách thành công', ['data' => $bookAddress]);
        }
        return $this->resBookAddress(Response::HTTP_INTERNAL_SERVER_ERROR, 'Tạo địa chỉ sách thất bại');
    }

    public function update(BookAddressRequest $request)
    {
        $input = $request->all();
        $bookAddress_update = DB::table('book_address')->where('id', $request->id)->update($input);
        if ($bookAddress_update > 0) {
            $bookAddress = DB::table('book_address')->where('id', $request->id)->get();
            $bookAddress = new BookAddressResource($bookAddress);
            return $this->resBookAddress(Response::HTTP_OK, 'Cập nhật địa chỉ sách thành công', ['data' => $bookAddress]);
        }
        return $this->resBookAddress(Response::HTTP_INTERNAL_SERVER_ERROR, 'Cập nhật địa chỉ sách thất bại');
    }

    public function destroy($id)
    {
        $query = DB::table('book_address')->where('id', $id)->delete();
        if ($query) {
            return $this->resBookAddress(Response::HTTP_OK, 'Xóa địa chỉ sách thành công');
        }
        return $this->resBookAddress(Response::HTTP_INTERNAL_SERVER_ERROR, 'Xóa địa chỉ sách thất bại');
    }

    protected function resBookAddress(int $status, string $message, ?array $resource = []): Response
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

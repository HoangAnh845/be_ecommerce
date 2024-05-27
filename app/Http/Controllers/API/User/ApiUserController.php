<?php

namespace App\Http\Controllers\API\User;

use App\Core\ResponseService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Service\UserService;
use Faker\Core\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiUserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = UserService::getAll($request->all());
            list($users, $meta) = UserService::getLengthAwarePaginatorData($data);

            return ResponseService::success([
                'users' => $users,
                'meta' => $meta
            ], 'Lấy danh sách người dùng thành công');
        } catch (\Exception $exception) {
            Log::error("=======[ApiUserController@index] File: "
                . $exception->getFile()
                . " Line: " . $exception->getLine()
                . " Message: " . $exception->getMessage());
            $error_code = $exception->getCode() == '42S22' ? 'Câu truy vấn SQL bị lỗi' : $exception->getMessage();
            return ResponseService::error(__('response.exception'), ["error_code" => $error_code]);
        }
    }

    public function show(Request $request)
    {
        try {
            $data = UserService::getById($request->id);
            return ResponseService::success([
                'users' => $data
            ], 'Lấy thông tin người dùng thành công');
        } catch (\Exception $exception) {
            Log::error("=======[ApiUserController@show] File: "
                . $exception->getFile()
                . " Line: " . $exception->getLine()
                . " Message: " . $exception->getMessage());
            $error_code = $exception->getCode() == '42S22' ? 'Câu truy vấn SQL bị lỗi' : $exception->getMessage();
            return ResponseService::error(__('response.exception'), ["error_code" => $error_code]);
        }
    }

    public function store(UserRequest $request)
    {
        try {
            dd(1);
            $user = UserService::insert($request->all());
            return ResponseService::success([
                'users' => $user,
            ], 'Thêm người dùng thành công');
        } catch (\Exception $exception) {
            Log::error("=======[ApiUserController@store] File: "
                . $exception->getFile()
                . " Line: " . $exception->getLine()
                . " Message: " . $exception->getMessage());
            $error_code = $exception->getCode() == '42S22' ? 'Câu truy vấn SQL bị lỗi' : $exception->getMessage();
            return ResponseService::error(__('response.exception'), ["error_code" => $error_code]);
        }
    }

    public function update(UserRequest $request)
    {
        try {
            $user = UserService::update($request->all(), $request->id);
            return ResponseService::success([
                'users' => $user
            ], 'Cập nhật người dùng thành công');
        } catch (\Exception $exception) {
            Log::error("=======[ApiUserController@update] File: "
                . $exception->getFile()
                . " Line: " . $exception->getLine()
                . " Message: " . $exception->getMessage());
            $error_code = $exception->getCode() == '42S22' ? 'Câu truy vấn SQL bị lỗi' : $exception->getMessage();
            return ResponseService::error(__('response.exception'), ["error_code" => $error_code]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = UserService::delete($request->id);
            return ResponseService::success([
                'users' => $user,
            ], 'Xóa người dùng thành công');
        } catch (\Exception $exception) {
            Log::error("=======[ApiUserController@destroy] File: "
                . $exception->getFile()
                . " Line: " . $exception->getLine()
                . " Message: " . $exception->getMessage());
            $error_code = $exception->getCode() == '42S22' ? 'Câu truy vấn SQL bị lỗi' : $exception->getMessage();
            return ResponseService::error(__('response.exception'), ["error_code" => $error_code]);
        }
    }
}

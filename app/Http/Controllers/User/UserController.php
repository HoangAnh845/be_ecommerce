<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmailRegistration;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\RegistrationSuccessful;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private const CREATE_USER = 'Bạn đã đăng ký thông tin tài khoản thành công',
        EDIT_USER = 'Bạn đã chỉnh sửa thông tin tài khoản thành công',
        DELETE_USER = 'Bạn đã xóa thông tin tài khoản thành công';

    public function index(): Response
    {
        $users = User::all();
        $userResource = [];
        foreach ($users->toArray() as $key => $value) {
            $user = new UserResource(json_decode(json_encode($value)));
            $userResource[] = $user;
        }
        if ($users) {
            return $this->resUser(Response::HTTP_OK, 'Lấy danh sách user thành công', ['data' => $userResource]);
        }
        return $this->resUser(Response::HTTP_NOT_FOUND, 'Không tìm thấy user');
    }

    public function show(Request $request): Response
    {
        $user = DB::table('users')->where('id', $request->id);
        if ($user->count() > 0) {
            $user = new UserResource($user->get()[0]);
            return $this->resUser(Response::HTTP_OK, 'Lấy thông tin user thành công', ['data' => $user]);
        }
        return $this->resUser(Response::HTTP_NOT_FOUND, 'Không tìm thấy user');
    }

    public function store(Request $request): Response // UserRequest
    {
        $input = $request->all();
        $user = User::create($input);
        if ($user) {
            $emailJob = new SendEmailRegistration($user); // tạo một job để gửi email
            dispatch($emailJob); // dispatch là phương thức gửi email
            return $this->resUser(Response::HTTP_OK, self::CREATE_USER, ['data' => $user]);
        }
        return $this->resUser(Response::HTTP_INTERNAL_SERVER_ERROR, 'Có lỗi xảy ra');
    }

    public function update(UserRequest $request): Response
    {
        $user = User::find($request->id);
        $user->update($request->all());
        if ($user) {
            return $this->resUser(Response::HTTP_OK, self::EDIT_USER, ['data' => $user]);
        }
        return $this->resUser(Response::HTTP_INTERNAL_SERVER_ERROR, 'Có lỗi xảy ra');
    }

    public function destroy(Request $request): Response
    {
        $user = User::find($request->id);
        $user->delete();
        return $this->resUser(Response::HTTP_OK, self::DELETE_USER);
    }

    public function filter(Request $request)
    {
        $users = DB::table('users');
        $userResource = [];
        
        // Tìm kiếm theo tên 
        if ($request->has('name')) {
            $users->where('username', 'LIKE', '%' . $request->name . '%');
        }

        // Tìm kiếm user thời gian sinh nhật trong tháng
        if ($request->has('birthday')) {
            $users->whereMonth('birthday', date('m', strtotime($request->birthday))); 
        }

        if ($request->has('sort')) {
            // Sắp xếp theo cũ nhất
            $request->sort === 'asc' ? $users->orderBy('created_at', 'asc') : 
            // Sắp xếp theo mới nhất
            $users->orderBy('created_at', 'desc');
        }

        // Lọc theo role user | member
        if ($request->has('role')) {
            $users->where('role', $request->role);
        }


        foreach($users->get() as $key => $value){
            $userResource[] = new UserResource($value);
        }
        if($userResource){
            return $this->resUser(Response::HTTP_OK, 'Lấy danh sách user thành công', ['data' => $userResource]);
        }
        return $this->resUser(Response::HTTP_NOT_FOUND, 'Không tìm thấy user');
    }

    protected function resUser(int $status, string $message, ?array $resource = []): Response
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

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoticeRequest;
use App\Http\Resources\NoticeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Notice;

class NoticeController extends Controller
{
    public function index()
    {
        $notice = DB::table('notices')->get();
        foreach ($notice->toArray() as $key => $value) {
            $notice = new NoticeResource($value);
            $noticeResource[] = $notice;
        }
        if (count($noticeResource) > 0) {
            return $this->resNotice(Response::HTTP_OK, 'Lấy danh sách thông báo thành công', ['data' => count($noticeResource) === 1 ? $noticeResource[0] : $noticeResource]);
        }
        return $this->resNotice(Response::HTTP_OK, 'Không có thông báo nào');
    }

    public function store(NoticeRequest $request): Response
    {
        $input = $request->all();
        $notice = Notice::create($input);
        if ($notice) {
            return $this->resNotice(Response::HTTP_OK, 'Create notice successfully', ['data' => $notice]);
        }
        return $this->resNotice(Response::HTTP_INTERNAL_SERVER_ERROR, 'Create notice failed');
    }

    public function update(NoticeRequest $request): Response
    {
        $input = $request->all();
        $notice = DB::table('notices')->where('id', $request->id)->update($input);
        if ($notice) {
            return $this->resNotice(Response::HTTP_OK, 'Cập nhật thông báo thành công');
        }
        return $this->resNotice(Response::HTTP_INTERNAL_SERVER_ERROR, 'Cập nhật thông báo thất bại');
    }

    public function destroy(Request $request): Response
    {
        $notice = DB::table('notices')->where('id', $request->id)->delete();
        if ($notice) {
            return $this->resNotice(Response::HTTP_OK, 'Xóa thông báo thành công');
        }
        return $this->resNotice(Response::HTTP_INTERNAL_SERVER_ERROR, 'Xóa thông báo thất bại');
    }

    public function sender(Request $request) // : Response
    {
        $input = $request->all();
        $list_sender  = json_decode($input['users']);
        $notice_items = [];

        // Kiểm tra thông báo gửi có tồn tại hay không?
        $notice = DB::table('notices')->where('id', $input['notice_id'])->first();

        if ($notice) {
            foreach ($list_sender as $key => $value) {
                $notice_items[] = [
                    'notice_id' => $input['notice_id'],
                    'user_id' => $value
                ];
            }

            // Thực hiện gửi notive tới danh sác người dùng truyền vào
            $notice_sender = DB::table('notice_users')->insert($notice_items);

            if ($notice_sender) {
                return $this->resNotice(Response::HTTP_OK, 'Gửi thông báo thành công');
            }

            return $this->resNotice(Response::HTTP_INTERNAL_SERVER_ERROR, 'Gửi thông báo thất bại');
        } else {
            return $this->resNotice(Response::HTTP_INTERNAL_SERVER_ERROR, 'Thông báo không tồn tại');
        }
    }

    public function filter(Request $request)
    {
        // Tìm kiếm thông báo theo tiêu chí id user
        $notice_users = DB::table('notice_users')->where('user_id', $request->id)->get();
        
        if(count($notice_users) === 0) {
            return $this->resNotice(Response::HTTP_OK, 'Không có thông báo nào');
        }else{
            foreach ($notice_users->toArray() as $key => $value) {
                $notice = DB::table('notices')->where('id', $value->notice_id)->get();
                // dd($notice);
                $notice = new NoticeResource($notice[0]);
                $noticeResource[] = $notice;
            }
            if (count($noticeResource) > 0) {
                return $this->resNotice(Response::HTTP_OK, 'Lấy thông báo thành công', ['data' => $noticeResource]);
            }

        }
    }

    protected function resNotice(int $status, string $message, ?array $resource = []): Response
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

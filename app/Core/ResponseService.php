<?php

namespace App\Core;

use Illuminate\Support\Facades\Response;

class ResponseService
{

    /**
     * @param $message
     * @param array $option
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message, $option = [])
    {
        return Response::json([
            'status'     => data_get($option, "status", "error"),
            'error_code' => data_get($option, "error_code", 1),
            'message'    => $message,
            'data'       => data_get($option, "data", [])
        ], data_get($option, "status_code", 500));
    }

    /**
     * @param array $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = [], $message = "success")
    {

        return Response::json(
            [
                'status'  => "success",
                'message' => $message,
                'data'    => $data
            ],
            200
        );
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * 成功响应方法，默认状态码为 200
     * @param $code
     * @param $message
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($statusCode  = 200, $msg = 'Success', $data = [])
    {
        return response()->json([
            'code' => $statusCode ,
            'msg' => $msg,
            'data' => $data
        ], $statusCode);
    }

    /**
     * 失败响应方法，默认状态码为 400
     * @param $code
     * @param $message
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($statusCode = 400, $msg = 'Error', $data = [])
    {
        return response()->json([
            'code' => $statusCode,
            'msg' => $msg,
            'data' => $data
        ], $statusCode);
    }
}

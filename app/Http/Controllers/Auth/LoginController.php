<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends BaseController
{
    /**
     * 登录
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        // 获取请求中的数据
        $request = request(['email', 'password']);

        // 通过jwt来验证请求数据，并且得到token
        if (!$token = auth('api') -> attempt($request)) {
            // 没有通过验证
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::ERROR_UNAUTHORIZED, null);
        }
        // 检查用户的状态
        $user = auth('api')->user();
        if ($user->status === 0) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::LOGIN_FAILED_ACCOUNT_DISABLED, null);
        }
        // 通过验证就直接将token携带在data中
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::LOGIN_SUCCESS, [
            'access_token' => $token,
            "token_type" => 'Bearer',
            'expires_in' => 360000
        ]);
    }


    /**
     * 退出登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::LOGOUT_SUCCESS, null);
    }

    /**
     * 刷新token
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->successResponse(CodeController::SUCCESS_CREATED, MsgController::USER_TOKEN_REFRESH,[
            'access_token' => auth('api')->refresh(),
            "token_type" => 'Bearer',
            'expires_in' => 360000
        ]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\LoginCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        $credentials = $request->only(['email', 'password']);
        $code = $request->input('code');
        $type = $request->input('type');

        // 通过 jwt 来验证请求数据，并且得到 token
        if (!$token = auth('api')->attempt($credentials)) {
            // 没有通过验证
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::LOGIN_ERROR, null);
        }

        // 检查用户的状态
        $user = auth('api')->user();
        if ($user->status === 0) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::LOGIN_FAILED_ACCOUNT_DISABLED, null);
        }

        // 验证 code 是否正确
        if (cache('login_code'. $request->input('email'))!= $code) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::EMAIL_CODE_ERROR, null);
        }
        // 验证是否为管理员
        if ($type === 1 && auth('api')->user()->is_admin === 0) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_FORBIDDEN, MsgController::ERROR_UNAUTHORIZED, null);
        }

        // 邮箱验证码验证过后就报废
        Cache::delete('login_code'. $request->input('email'));

        // 设置过期时间为 一周 后
        $expiresAt = Carbon::now()->addWeek()->timestamp;
        // 创建自定义有效负载
        $payload = [
            'sub' => $user->id,
            'exp' => $expiresAt
        ];
        // 生成新的 token
        $token = JWTAuth::claims($payload)->attempt($credentials);

        // 通过验证就直接将 token 携带在 data 中
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::LOGIN_SUCCESS, [
            'access_token' => $token,
            "token_type" => 'Bearer',
            'expires_in' => 60*24*7
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
            'expires_in' => 60*24*7
        ]);
    }

    /**
     * 发送验证码
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function code(Request $request)
    {
        $email = $request->input('email');

        // 缓存键名
        $code_interval = 'login_email_interval'.$email;

        // 检查是否有该缓存
        if (Cache::has($code_interval)) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_CONFLICT, MsgController::EMAIL_CODE_INTERVAL,null);
        }

        // 发送邮件
        Mail::to($email)->send(new LoginCode());
        // 设置60S的缓存
        Cache::put($code_interval, true, 60);

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::EMAIL_SEND_SUCCESS , null);
    }

    /**
     * 校验是否为管理员
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAdmin()
    {
        $isAdmin = auth('api')->user()->is_admin;
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_FETCHED_SUCCESS, $isAdmin);

    }
}

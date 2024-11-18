<?php

namespace App\Http\Middleware;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\MsgController;
use App\Http\Requests\Auth\LoginRequest;
use Closure;

class ApiAuthMiddleware
{
    /**
     * 认证用户
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        // 检查请求中的认证信息，例如令牌
        if ($this->isAuthenticated($request)) {
            return $next($request);
        } else {
            return response()->json([
                'code' => CodeController::CLIENT_ERROR_UNAUTHORIZED,
                'msg' => MsgController::ERROR_UNAUTHORIZED,
                'data' => null
            ]);
        }
    }

    private function isAuthenticated($request)
    {
        try {
            // 从请求头中获取令牌
            $token = $request->header('Authorization');
            if (!$token) {
                return false;
            }
            $token = str_replace('Bearer ', '', $token);
            // 验证令牌
            if (!app('tymon.jwt.auth')->setToken($token)->check()) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Mail\EmailCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class BindController extends BaseController
{
    /**
     * 发送邮箱校验的验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function codeEmail()
    {
        // 获取当前用户邮箱
        $email = auth('api')->user()->email;

        // 缓存键名
        $code_interval = 'set_email_interval'.$email;

        // 检查是否有该缓存
        if (Cache::has($code_interval)) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_CONFLICT, MsgController::EMAIL_CODE_INTERVAL,null);
        }

        // 发送邮件
        Mail::to($email)->send(new EmailCode());
        // 设置60S的缓存
        Cache::put($code_interval, true, 60);

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::EMAIL_SEND_SUCCESS , null);
    }

    /**
     * 更新邮箱
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'code' => 'required'
            ],[
                'name.required' => '邮箱是必填项。',
                'code.required' => '验证码是必填项。',
            ]);

            // 获取当前用户邮箱
            $email = auth('api')->user()->email;

            // 验证code是否正确
            if (cache('update_email'.$email) != $request->input('code')) {
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::EMAIL_CODE_ERROR, null);
            }

            // 邮箱验证码验证一次后就报废
            Cache::delete('update_email'.$email);
            // 更新邮箱
            $user = auth('api')->user();
            $user->email = $request->input('email');
            $user->save();

            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_UPDATED_SUCCESS, null);

        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }
    }

    /**
     * 更新用户的信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            // 验证请求数据（可选，但推荐）
            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'avatar' => 'nullable|string',
                'gender' => 'nullable|in:男,女',
                'birthday' => 'nullable|date',
            ]);

            // 获取当前认证用户ID
            $userId = auth('api')->user()->id;

            // 构建要更新的数据数组
            $updateData = array_filter($validatedData, function ($value) {
                return !is_null($value);
            });

            // 如果没有任何数据需要更新，则返回或处理相应逻辑
            if (empty($updateData)) {
                return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_UPDATED_SUCCESS, null);
            }

            // 更新用户信息
            User::where('id', $userId)->update($updateData);

            // 返回成功响应
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_UPDATED_SUCCESS, null);
        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::USER_INFO_UPDATED_FAILED, null);
        }

    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Mail\EmailCode;
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
        // 发送邮件
        Mail::to($email)->send(new EmailCode());

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
}

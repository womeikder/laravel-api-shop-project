<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\MsgController;
use Illuminate\Http\Request;

class PasswordController extends BaseController
{
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'old_password' => 'required',
                'password' => 'required|confirmed',
            ], [
                'password.required' => '旧密码是必填项。',
                'password.confirmed' => '确认密码有误。',
                'old_password.required' => '新密码密码是必填项。',

            ]);

            $old_password = $request->input('old_password');
            $user = auth('api')->user();

            if (!password_verify($old_password, $user->password)) {
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, '旧密码不正确。', null);
            }
            $user->password = bcrypt($request->input('password'));
            $user->save();

            return $this->successResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::USER_INFO_UPDATED_SUCCESS,null);

        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }

    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends BaseController
{
    /**
     * 用户的详情
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfo()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_FETCHED_SUCCESS, $user);
    }

    /**
     * 更新用户的数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userUpdate(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|max:16|min:4',
                'phone' => 'max:11|min:11|regex:/^1[3-9]\d{9}$/',
                'gender' => 'in:男,女,保密',
            ], [
                'name.required' => '姓名是必填项。',
                'name.max' => '姓名长度不能超过16个字符。',
                'name.min' => '姓名长度不能少于4个字符。',
                'phone.max' => '格式不正确手机号必须是11位',
                'phone.min' => '格式不正确手机号必须是11位',
                'phone.regex' => '手机号格式不正确',
                'gender.in' => '格式不正确(男,女,保密)',
            ]);

            $phone = $request->input('phone');
            $avatar = $request->input('avatar');
            $gender = $request->input('gender');
            $birthday = $request->input('birthday');

            $user = auth('api')->user();
            $user->name = $request->input('name');
            if ($phone) {
                $user->phone = $phone;
            }
            if ($avatar) {
                $user->avatar = $avatar;
            }
            if ($gender) {
                $user->gender = $gender;
            }
            if ($birthday) {
                $user->birthday = $birthday;
            }
            $user->save();
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_UPDATED_SUCCESS, null);

        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }

    }

}

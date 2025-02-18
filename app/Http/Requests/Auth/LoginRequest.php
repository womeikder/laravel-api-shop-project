<?php

namespace App\Http\Requests\Auth;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\CodeController;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends BaseRequest
{

    /**
     * 登录的表单验证规则
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:6|regex:/^(?=.*[a-z])(?=.*\d).+$/',
            'code' => 'required',
            'type' => 'required'
        ];
    }

    /**
     * 自定义错误消息
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => '邮箱是必填项。',
            'email.email' => '邮箱格式不正确，请重新输入。',
            'password.required' => '密码是必填项。',
            'password.min' => '密码长度不能少于6个字符。',
            'password.max' => '密码长度不能超过16个字符。',
            'password.regex' => '密码必须包含小写字母和数字。',
            'code.required' => '验证码是必填项。',
            'type.required' => '登录类型是必填项。',
        ];
    }

    /**
     * 处理验证失败
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        $errorCodes = [
            'email.unique' => CodeController::CLIENT_ERROR_CONFLICT,
        ];

        throw new CustomValidationException($validator, $errorCodes, $this->messages());
    }
}

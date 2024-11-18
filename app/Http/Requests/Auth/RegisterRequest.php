<?php

namespace App\Http\Requests\Auth;

use App\Exceptions\CustomValidationException;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Arr;

class RegisterRequest extends BaseRequest
{
    /**
     * 注册的表单验证规则
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:16|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:16|regex:/^(?=.*[a-z])(?=.*\d).+$/',
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
            'name.required' => '姓名是必填项。',
            'name.max' => '姓名长度不能超过16个字符。',
            'name.min' => '姓名长度不能少于4个字符。',
            'email.required' => '邮箱是必填项。',
            'email.email' => '邮箱格式不正确，请重新输入。',
            'email.unique' => '该邮箱已被使用，请更换邮箱。',
            'password.required' => '密码是必填项。',
            'password.min' => '密码长度不能少于6个字符。',
            'password.max' => '密码长度不能超过16个字符。',
            'password.regex' => '密码必须包含小写字母和数字。',
        ];
    }

    /**
     * 处理验证失败
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        $errorCodes = [];

        throw new CustomValidationException($validator, $errorCodes, $this->messages());
    }
}

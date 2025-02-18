<?php

namespace App\Http\Requests\Web;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends BaseRequest
{
    /**
     * 表单验证规则
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'city_id' => 'required|exists:city,id',
            'detail' => 'required',
            'phone' => 'required|regex:/^1[3-9]\d{9}$/',
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
            'name.required' => '名称是必填项。',
            'city_id.required' => '城市是必填项。',
            'detail.required' => '详细地址为必填。',
            'phone.required' => '手机号为必填。',
            'phone.regex' => '手机号格式不符合。',
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
        $errorCodes = [];
        throw new CustomValidationException($validator, $errorCodes, $this->messages());
    }
}

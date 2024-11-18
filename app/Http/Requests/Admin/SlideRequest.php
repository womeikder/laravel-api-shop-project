<?php

namespace App\Http\Requests\Admin;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\CodeController;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SlideRequest extends BaseRequest
{
    /**
     * 表单验证规则
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'img' => 'required',
            'status' => 'integer|in:0,1',
            'url' => 'string',
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
            'title.required' => '名称是必填项。',
            'img.required' => '图片地址是必填项。',
            'status.integer' => '状态码只包含(0,1)',
            'status.in' => '状态码只包含(0,1)',
            'url.string' => '图片地址必须是字符串。'
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

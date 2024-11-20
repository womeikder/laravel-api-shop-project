<?php

namespace App\Http\Requests\Admin;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\CodeController;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class InformRequest extends BaseRequest
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
            'content' => 'required',
            'status' => 'integer|in:1,2',
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
            'title.required' => '标题是必填项。',
            'content.required' => '内容是必填项。',
            'status.in' => '状态码只包含(1,2)'
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

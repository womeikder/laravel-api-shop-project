<?php

namespace App\Http\Requests\Admin;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\CodeController;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends BaseRequest
{
    /**
     * 表单验证规则
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:16|unique:categories',
            'pid' => 'integer',
            'status' => 'integer|in:0,1',
            'level' => 'integer',
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
            'name.max' => '密码长度不能超过16个字符。',
            'name.unique' => '该分类已经被创建。',
            'status.in:0,1' => '状态码只包含(0,1)'
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
            'name.unique' => CodeController::CLIENT_ERROR_CONFLICT,
        ];
        throw new CustomValidationException($validator, $errorCodes, $this->messages());
    }
}

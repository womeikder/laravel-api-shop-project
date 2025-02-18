<?php

namespace App\Http\Requests\Web;

use App\Exceptions\CustomValidationException;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends BaseRequest
{
    /**
     * 表单验证规则
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'goods_id' => 'required|integer|exists:goods,id',
            'comment' => 'required|string',
            'star' => 'required|integer|between:1,5',
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
            'goods_id.required' => '商品ID为必传参数',
            'goods_id.integer' => '商品ID必须为整数',
            'goods_id.exists' => '商品ID不存在',
            'content.required' => '评论内容为必传参数',
            'content.string' => '评论内容必须为字符串',
            'star.required' => '星级为必传参数',
            'star.integer' => '星级必须为整数',
            'star.between' => '星级必须在1-5之间',
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

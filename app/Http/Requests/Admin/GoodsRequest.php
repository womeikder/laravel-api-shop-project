<?php

namespace App\Http\Requests\Admin;

use App\Exceptions\CustomValidationException;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class GoodsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required',
            'goods_name' => 'required|max:20',
            'title' => 'required|max:50',
            'description' => 'required|max:255',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'cover' => 'required',
            'pics' => 'required|array',
            'status' => 'integer|in:0,1',
            'recommend' => 'integer|in:0,1',
            'detail' => 'required',
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
            'goods_name.required' => '商品名称是必填项。',
            'title.required' => '商品标题是必填项。',
            'category_id.required' => '分类ID是必填项。',
            'description.required' => '描述是必填项。',
            'price.required' => '价格是必填项。',
            'stock.required' => '库存是必填项。',
            'cover.required' => '封面图是必填项。',
            'pics.required' => '图片集合是必填项。',
            'detail.required' => '详情是必填项。',
            'description.max' => '描述长度不能超过255个字符。',
            'goods_name.max' => '描述长度不能超过20个字符。',
            'title.max' => '描述长度不能超过50个字符。',
            'price.integer' => '价格必须是数字。',
            'stock.integer' => '库存必须是数字。',
            'price.min:0' => '价格最少为0.',
            'stock.min:0' => '库存最少为0.',
            'pics.array' => '图片集必须是数组。',
            'status.in:0,1' => '销售状态只能包含0、1',
            'recommend.in:0,1' => '推荐状态只能包含0、1',
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

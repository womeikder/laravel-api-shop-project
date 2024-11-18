<?php

namespace App\Exceptions;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException as BaseValidationException;
use Symfony\Component\HttpFoundation\Response;

class CustomValidationException extends BaseValidationException
{
    protected $errorCode;

    /**
     * 构造函数，主要初始化，并且给到一些默认值
     * @param $validator
     * @param $errorCodes
     * @param $messages
     * @param $status
     * @param $headers
     */
    public function __construct($validator, $errorCodes = [], $messages = [], $status = Response::HTTP_BAD_REQUEST, $headers = [])
    {
        $this->errorCodes = $errorCodes;
        $this->messages = $messages;
        // 获取第一个错误的验证规则
        $firstError = $validator->errors()->first();
        $errorKey = array_search($firstError, $this->messages);
        // 寻找对应的信息，没有就直接给默认值
        $this->message = $errorKey? $this->messages[$errorKey] : 'Validation Error';
        parent::__construct($validator, '', $status, $headers);
    }

    // 获取状态码
    public function getErrorCode()
    {
        // 获取第一个错误的验证规则
        $firstError = $this->validator->errors()->first();
        // 根据对应的情况搜索信息
        $errorKey = array_search($firstError, $this->messages);
        // 如果对应的信息存在，返回对应的状态码
        if ($errorKey) {
            return Arr::get($this->errorCodes, $errorKey, Response::HTTP_BAD_REQUEST);
        }
        return Response::HTTP_BAD_REQUEST;
    }

    /**
     * 返回数据的方法
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        // 根据对应的情况返回code
        $errorCode = $this->getErrorCode();
        return response()->json([
            'code' => $errorCode,
            'msg' => $this->message,
            'data' => null,
        ], $this->status);
    }
}

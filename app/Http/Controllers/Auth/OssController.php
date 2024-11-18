<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OssController extends BaseController
{
    /**
     * 生成oss上传token
     * @return \Illuminate\Http\JsonResponse
     */
    public function token()
    {
        $disk = Storage::disk('oss');
        $config = $disk->getAdapter()->signatureConfig($prefix = '/', $callBackUrl = '', $customData = [], $expire = 300);
        // 将数据转换为json
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_TOKEN_REFRESH, json_decode($config));
    }
}

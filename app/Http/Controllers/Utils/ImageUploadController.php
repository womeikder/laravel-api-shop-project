<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\MsgController;
use Illuminate\Http\Request;

class ImageUploadController extends BaseController
{
    public function upload(Request $request)
    {
        try {
            // 验证请求中的图像数据
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
                'name' => 'required'
            ], [
                'image.required' => '必须上传一个图像文件',
                'name.required' => '必须上传唯一的文件名',
                'image.image' => '上传的文件必须是图像',
                'image.mimes' => '图像文件格式必须是jpeg、png、jpg、gif（或其他支持的格式）',
                'image.max' => '图像文件大小不能超过10MB'
            ]);

            $name = $request->input('name');

            // 将图片保存到公共目录下的 images 文件夹中
            if ($request->hasFile('image')) {
                $path = $request->file('image')->storeAs('images', $name,'public');
                // 返回成功的 JSON 响应
                return $this->successResponse(CodeController::SUCCESS_OK, MsgController::IMAGE_UPLOAD_SUCCESS ,null);
            }
        } catch (\Exception $e) {
            // 如果没有文件或验证失败，则返回错误信息
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }

    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Http\Requests\Admin\SlideRequest;
use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends BaseController
{
    /**
     * 轮播图列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $size = $request->input('size');
        $res = Slide::when($size, function ($query) use ($size) {
            $query->limit($size);
        })
            ->where('status', 1)
            ->orderBy('seq', 'asc')
            ->get();

        if ($res->isEmpty()) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST, null);
        }
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::SLIDE_QUERY_SUCCESS, $res);
    }

    /**
     * 添加轮播图
     * @param SlideRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SlideRequest $request)
    {
        // 查询最大seq排序
        $seq = Slide::max('seq') ?? 0;
        $seq++;

        $request->offsetSet('seq', $seq);
        $request->offsetSet('url', $seq);
        Slide::create($request->all());

        return $this->successResponse(CodeController::SUCCESS_CREATED, MsgController::SLIDE_CREATED, $request);
    }

    /**
     * 轮播图详情
     * @param Slide $slide
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Slide $slide)
    {
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::SLIDE_QUERY_SUCCESS, $slide);
    }

    /**
     * 轮播图更新
     * @param SlideRequest $request
     * @param Slide $slide
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SlideRequest $request, Slide $slide)
    {
        $slide->update($request->all());
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::SLIDE_UPDATE_SUCCESS, null);
    }

    /**
     * 删除轮播图
     * @param Slide $slide
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Slide $slide)
    {
        $slide->delete();
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::SLIDE_DELETED_SUCCESS, null);
    }

    /**
     * 更改排序位次
     * @param Request $request
     * @param Slide $slide
     * @return \Illuminate\Http\JsonResponse
     */
        public function seq(Request $request)
    {
        // 从前端请求中获取 id
        $id = $request->input('id');

        // 验证 id 是否存在
        if (!$id) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST, 'ID is required');
        }

        // 查找对应的 Slide 记录
        $slide = Slide::find($id);
        if (!$slide) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST);
        }

        // 找出当前前五名的 Slide 记录中最大的 seq 值
        $topFiveMaxSeq = Slide::where('id', '!=', $id)
            ->orderBy('seq', 'asc')
            ->offset(5)
            ->value('seq');

        $newSeq = $topFiveMaxSeq? $topFiveMaxSeq - 1 : 1;

        // 更新 Slide 的 seq 属性
        $slide->update(['seq' => $newSeq]);

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::SLIDE_UPDATE_SUCCESS, null);
    }
}

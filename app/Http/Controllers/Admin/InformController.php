<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Http\Requests\Admin\InformRequest;
use App\Models\Inform;
use Illuminate\Http\Request;

class InformController extends BaseController
{
    /**
     * 获取通知列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $inform = Inform::where('status',1)->get();
        $res = $inform->map(function ($inform) {
            return [
                'id' => $inform->id,
                'title' => $inform->title,
                'time' => $inform->update_time
            ];
        });
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::INFORM_QUERY_SUCCESS, $res);
    }

    /**
     * 创建通知
     * @param InformRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(InformRequest $request)
    {
        $user_id = auth('api')->user()->id;
        $title = $request->input('title');
        $content = $request->input('content');
        $status = $request->input('status', 1);

        $inform = new Inform();
        $inform->user_id = $user_id;
        $inform->title = $title;
        $inform->content = $content;
        $inform->status = $status;
        $inform->save();

        return $this->successResponse(CodeController::SUCCESS_CREATED, MsgController::INFORM_CREATE_SUCCESS, null);
    }

    /**
     * 展示具体的消息
     */
    public function show(Inform $inform)
    {
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::INFORM_QUERY_SUCCESS, $inform);
    }

    /**
     * 更新消息
     */
    public function update(InformRequest $request, Inform $inform)
    {
        $user_id = auth('api')->user()->id;
        if ($user_id !== $inform->user_id) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_FORBIDDEN, MsgController::INFORM_UNAUTHORIZED, null);
        }

        $title = $request->input('title');
        $content = $request->input('content');
        $status = $request->input('status', 1);

        $inform->title = $title;
        $inform->content = $content;
        $inform->status = $status;
        $inform->save();

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::INFORM_UPDATED, null);
    }

    /**
     * 将消息删除--将状态记为删除状态
     * @param Inform $inform
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Inform $inform)
    {
        $user_id = auth('api')->user()->id;
        if ($user_id !== $inform->user_id) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_FORBIDDEN, MsgController::INFORM_UNAUTHORIZED, null);
        }
        $inform->status = 2;
        $inform->save();
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::INFORM_DELETED_SUCCESS, null);
    }
}

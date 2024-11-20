<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\MsgController;
use App\Mail\OrderPost;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class OrderController extends BaseController
{
    /**
     * 订单列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 获取分页的参数，设置合理的默认值
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);

        // 获取筛选条件参数
        $status = $request->input('status');
        $user_id = $request->input('user_id');

//        dd($status);

        // 构建基础的查询构建器实例
        $query = Order::query();

        // 根据状态条件筛选
        if ($status!== null) {
            $query->where('status', $status);
        }

        // 根据用户ID条件筛选
        if ($user_id!== null) {
            $query->where('user_id', $user_id);
        }

        try {
            // 执行分页查询，捕获可能出现的数据库查询异常
            $orderPaginator = $query->paginate($perPage, ['*'], 'page', $page);
            // 处理分页查询结果中的每一条数据，提取需要返回的字段信息
            $res = $orderPaginator->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'user_id' => $order->user_id,
                    'user' => $order->user,
                    'amount' => $order->amount,
                    'status' => $order->status,
                    'address' => $order->address,
                    'express_type' => $order->express_type,
                    'express_no' => $order->express_no,
                    'pay_time' => $order->pay_time,
                    'pay_type' => $order->pay_type,
                    'trade_no' => $order->trade_no,
                    'detail' => $order->orderDetails,
                    'create_time' => $order->create_time,
                    'update_time' => $order->update_time,
                ];
            });

            // 返回成功响应，包含分页相关信息和处理后的订单数据
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::ORDER_QUERIED_SUCCESS, [
                'current_page' => $orderPaginator->currentPage(),
                'total' => $orderPaginator->total(),
                'per_page' => $orderPaginator->perPage(),
                'last_page' => $orderPaginator->lastPage(),
                'data' => $res
            ]);
        } catch (\Exception $e) {
            // 如果出现异常，记录日志（这里假设使用 Laravel 自带的日志功能，可根据实际情况调整）
            \Log::error('订单分页查询出现异常: '. $e->getMessage());
            // 返回错误响应，可根据实际情况自定义错误状态码和消息
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, '订单查询出现错误，请稍后再试');
        }
    }

    /**
     * 订单详情
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        $res = $order->where('id', $order->id)->with('user','orderDetail.goods')->get();

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::ORDER_QUERIED_SUCCESS,$res);
    }

    /**
     * 快递发货
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function post(Request $request, Order $order)
    {
        try {
            // 验证提交的参数
            $request->validate([
                'express_type' => 'required|in:SF,YT,YD',
                'express_no' => 'required'
            ], [
                'express_type.required' => '快递公司必填',
                'express_type.in' => '快递类型只能是:SF,YT,YD',
                'express_no.required' => '快递单号必填'
            ]);

            $order->express_type = $request->input('express_type');
            $order->express_no = $request->input('express_no');
            $order->status = 3;
            $order->save();

            // 发货后邮件提醒
            Mail::to($order->user)->queue(new OrderPost($order));

            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::ORDER_POST_SUCCESS,null);

        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }
    }
}

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
        // 获取分页的参数
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status');
        $user_id = $request->input('user_id');

        // 条件分页查
        $order = Order::when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
            ->when($user_id, function ($query)  use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->paginate($perPage);

        // 设置返回的的参数
        $res = $order->map(function ($order) {
            return [
                'id' => $order->id,
                'order_no' => $order->name,
                'user_id' => $order->email,
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
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::ORDER_QUERIED_SUCCESS, $res);
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

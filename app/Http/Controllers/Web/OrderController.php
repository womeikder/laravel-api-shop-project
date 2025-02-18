<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Models\Cart;
use App\Models\Comment;
use App\Models\Goods;
use App\Models\Order;
use App\Models\OrderDetails;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use function Laravel\Prompts\error;

class OrderController extends BaseController
{

    /**
     * 订单列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $orderBy = $request->input('order_by', 'desc');
        $status = $request->input('status');

        $commentQuery = Order::where('user_id', auth('api')->id())
            ->when(!is_null($orderBy), function ($query) use ($orderBy) {
                $query->orderBy('create_time', $orderBy);
            })
            ->when(!is_null($status), function ($query) use ($status) {
                $query->where('status', $status);
            });
        $commentPaginator = $commentQuery->paginate($perPage, ['*'], 'page', $page);
        // 如果请求的每页数量大于等于总记录数，则设置为总记录数
        if ($perPage >= $commentPaginator->total()) {
            $commentPaginator = $commentQuery->paginate($commentPaginator->total(), ['*'], 'page', 1);
        }

        $res = $commentPaginator->map(function ($order) {
            $detail = [];
            foreach ($order->orderDetail as $key) {
                $detail[] = [
                    'goods_id' => $key->goods_id,
                    'price' => $key->price,
                    'number' => $key->number,
                    'goods' => $key->goods,
                ];
            }

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
                'detail' => $detail,
                'create_time' => $order->create_time,
                'update_time' => $order->update_time,
            ];
        });
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::ORDER_QUERIED_SUCCESS, [
            'current_page' => $commentPaginator->currentPage(),
            'total' => $commentPaginator->total(),
            'per_page' => $commentPaginator->perPage(),
            'last_page' => $commentPaginator->lastPage(),
            'data' => $res
        ]);
    }



    /**
     * 购物车结算前预览订单商品
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview()
    {
        // 购物车数据
        $carts = Cart::where('user_id', auth('api')->user()->id)
            ->where('checked', 1)
            ->with('goods')
            ->get();

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::ORDER_QUERIED_SUCCESS, $carts);
    }

    /**
     *  提交订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'address_id' => 'required',
            ], [
                'address_id.required' => '收货地址必填',
            ]);

            $user_id = auth('api')->id();
            $order_no = str_replace('-', '', 'womeik-' . Uuid::uuid7()->toString()) ;
            $amount = 0;

            // 订单详情数据
            $order_detail_data = [];

            $goods_id = $request->input('goods_id');
            $count = intval($request->input('count'));


            $cartsQuery = null;
            $carts = null;

            if ($goods_id && $count) {
                $goods = Goods::where('id', $goods_id)->first();
                if (!$goods) {
                    return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST, null);
                }
                if ($goods->stock < $count) {
                    return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $goods->title. '，库存不足请重新选择', null);
                }
                $order_detail_data[] = [
                    'goods_id' => $goods_id,
                    'price' => $goods->price,
                    'number' => $count
                ];
                $amount += $goods->price * $count;
            } else {
                // 查询器
                $cartsQuery = Cart::where('user_id', $user_id)
                    ->where('checked', 1)
                    ->with('goods:id,price,title,stock');

                $carts = $cartsQuery->get();

                foreach ($carts as $key => $cart) {
                    // 如果有商品库存不足
                    if ($cart->goods->stock < $cart->count) {
                        return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $cart->goods->title . '，库存不足请重新选择', null);
                    }

                    $order_detail_data[] = [
                        'goods_id' => $cart->goods_id,
                        'price' => $cart->goods->price,
                        'number' => $cart->count
                    ];

                    $amount += $cart->goods->price * $cart->count;
                }
            }

            try {
                // 开启事务
                DB::beginTransaction();
                // 生成订单
                $order = Order::create([
                    'user_id' => $user_id,
                    'order_no' => $order_no,
                    'address' => $request->input('address_id'),
                    'amount' => $amount,
                    'expire_time' => Carbon::now()->addHour()->toDateTimeString()
                ]);

                // 生成订单的详情
                $order->orderDetail()->createMany($order_detail_data);

                if (!$goods_id && $count) {
                    // 删除已经结算的购物车数据
                    $cartsQuery->delete();
                }
                // 减去对应商品的库存
                if ($goods_id && $count) {
                    Goods::where('id', $goods_id)->decrement('stock', $count);
                } else {
                    foreach ($carts as $cart) {
                        Goods::where('id', $cart->goods_id)->decrement('stock', $cart->count);
                    }
                }
                // 数据提交
                DB::commit();

                return $this->successResponse(CodeController::SUCCESS_CREATED, MsgController::ORDER_CREATED_SUCCESS, ['id' => $order->id]);
            } catch (\Exception $exception) {
                DB::rollBack();
                throw $exception;
            }

        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }
    }

    /**
     * 返回订单详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required'
            ], [
                'id.required' => '订单编号不能为空'
            ]);

            $res = Goods::where('id', $request->input('id'))->get();
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::ORDER_QUERIED_SUCCESS, $res);
        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }


    }


    /**
     * 订单支付
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function payment(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'type' => 'required|in:aliyun,wechat,unionpay'
            ], [
                'id.required' => '订单编号不能为空',
                'type.required' => '支付类型不能为空',
                'type.in' => '支付类型只能是微信或者支付宝'
            ]);

            $payName = '微信';
            if ($request->input('type') === 'aliyun') {
                $payName = '支付宝';
            }
            $trade = str_replace('-', '', 'trade-' . Uuid::uuid7()->toString()) ;


            // 首先通过 where 找到要更新的订单，并使用 first() 方法执行查询，得到 Order 实例
            $order = Order::where('id', $request->input('id'))->first();

            if ($order->status !== 1) {
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::ORDER_PAID_ERROR, null);
            }
            if (Carbon::parse($order->expire_time) < Carbon::now()) {
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::ORDER_EXPIRE, null);
            }

            if ($order) {
                // 对订单实例的属性进行修改
                $order->pay_time = Carbon::now()->toDateTimeString();
                $order->pay_type = $payName;
                $order->trade_no = $trade;
                $order->status = 2;
                // 调用 save 方法将修改保存到数据库
                $order->save();
            } else {
                // 可以添加错误处理，例如抛出异常或记录日志
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST, null);
            }

            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::ORDER_PAID_SUCCESS, null);
        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }
    }

    /**
     * 查询物流信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function express(Request $request) {
        try {
            $request->validate([
                'id' =>'required'
            ],[
                'id.required' => '订单编号不能为空'
            ]);
            $order = Order::where('id', $request->input('id'))->where('status', 3)->first();
            if (!$order) {
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST, null);
            }
            $res = [
                'express_no' => $order->express_no,
                'express_type' => $order->express_type,
                'express_status' => '功能未开发',
            ];
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::ORDER_QUERIED_SUCCESS, $res);
        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }
    }

    /**
     * 确认收货
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function receive(Request $request) {
        try {
            $request->validate([
                'id' =>'required'
            ], [
                'id.required' => '订单编号不能为空'
            ]);
            $order = Order::where('id', $request->input('id'))->where('status', 3)->first();
            if (!$order) {
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST, null);
            }
            $order->status = 4;
            $order->save();
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::ORDER_POST_SUCCESS, null);
        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }
    }


    /**
     * 立即购买
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buy(Request $request) {
        try {
            request()->validate([
                'goods_id' =>'required',
                'number' => 'required|integer|min:1',
            ], [
                'goods_id.required' => '商品编号不能为空',
                'number.required' => '商品数量不能为空',
                'number.integer' => '商品数量必须是整数',
                'number.min' => '商品数量不能小于1',
            ]);

            $goods = Goods::where('id', $request->input('goods_id'))->first();
            if (!$goods) {
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST, null);
            }
            if ($goods->stock < $request->input('number')) {
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::PRODUCT_SEARCH_FAILED, null);
            }
            $res = [
                'id' => $goods->id,
                'count' => intval($request->input('number')) ,
                'goods' => $goods,
            ];
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_SEARCH_SUCCESS, [$res]);
        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }
    }

    /**
     * 删除订单或者取消订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required'
            ], [
                'id.required' => '订单编号不能为空'
            ]);

            DB::beginTransaction();

            $order = Order::where('id', $request->input('id'))->first();

            if (!$order) {
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST, null);
            }

            switch ($order->status) {
                case 1: // 待支付
                case 2: // 已支付
                    $order->update(['status' => 6]);
                    $message = '订单已取消';
                    break;
                case 3: // 已发货
                    return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, '订单已发货，无法取消', null);
                case 4: // 已完成
                    $order->delete();
                    OrderDetails::where('order_id', $order->id)->delete();
                    $message = '订单已删除';
                    break;
                default:
                    return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, '无效的订单状态', null);
            }

            DB::commit();

            return $this->successResponse(CodeController::SUCCESS_OK, $message, null);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('订单删除或取消失败: ' . $e->getMessage());
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, '操作失败，请稍后重试', null);
        }
    }


    /**
     * 订单状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function status(Request $request) {
        try {
            $request->validate([
                'id' =>'required|exists:orders,id'
            ], [
                'id.required' => '订单编号不能为空',
                'id.exists' => '订单不存在'
            ]);
            $order = Order::where('id', $request->input('id'))->first();

            if ($order->expire_time < Carbon::now()->toDateTimeString()) {
                Order::where('id', $request->input('id'))->update(['status' => 6]);
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, '订单已过期', null);
            }
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::ORDER_QUERIED_SUCCESS, $order);

        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }
    }
}

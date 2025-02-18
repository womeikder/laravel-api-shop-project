<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Models\Cart;
use App\Models\Goods;
use Illuminate\Http\Request;

class CartController extends BaseController
{
    /**
     * 购物车列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $carts = Cart::where('user_id', auth('api')->id())->get();

        $data = $carts->map(function ($cart) {
           return [
               'id' => $cart->id,
               'user_id' => $cart->user_id,
               'goods_id' => $cart->goods_id,
               'count' => $cart->count,
               'checked' => $cart->checked,
               'goods' => $cart->goods
           ];
        });
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::CART_INFO_FETCH_SUCCESS, $data);
    }

    /**
     * 添加商品到购物车
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'goods_id' => 'required|exists:goods,id',
                'number' => [
                    function ($attribute, $value, $fail) use ($request) {
                        $goods = Goods::find($request->goods_id);
                        if ($value > $goods->stock) {
                            $fail("数量不能超过商品库存");
                        }
                    }
                ]
            ], [
                'goods_id.required' => '商品Id 不能为空',
                'goods_id.exists' => '商品不存在'
            ]);

            // 判断当前购物车的数据是否已经存在，如果有就直接更新数量
            $cart = Cart::where('user_id', auth('api')->id())
                ->where('goods_id', $request->input('goods_id'))
                ->first();

            if (!empty($cart)) {
                $cart->count = $request->input('count', 1);
                $cart->save();
                return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_ADDED_TO_CART_SUCCESS, null);
            }


            // 创建购物车
            Cart::create([
               'user_id' => auth('api')->id(),
               'goods_id' => $request->input('goods_id'),
                'count' => $request->input('count', 1)
            ]);

            return $this->successResponse(CodeController::SUCCESS_CREATED, MsgController::PRODUCT_ADDED_TO_CART_SUCCESS, null);
        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }
    }

    /**
     * 更新数量
     * @param Request $request
     * @param Cart $cart
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Cart $cart)
    {
        try {
            $request->validate([
                'count' => [
                    'required',
                    'gte:1',
                    function ($attribute, $value, $fail) use ($cart) {
                        if ($value > $cart->goods->stock) {
                            $fail('数量不能超过最大库存');
                        }
                    }
                ]
            ], [
                'count.required' => '商品数量 不能为空',
                'count.gte' => '商品数量 最小是1'
            ]);

            $cart->count = $request->input('count');
            $cart->save();

            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_UPDATE_SUCCESS, null);
        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }
    }

    /**
     * 移除购物车
     * @param Cart $cart
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_DELETE_SUCCESS, null);
    }

    /**
     * 购物车商品选中状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request) {
        $ids = explode(',', $request->input('ids'));
        $checked = explode(',', $request->input('checked'));

        if (count($ids) !== count($checked)) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, 'ids和checked参数数量不匹配', null);
        }

        for ($i = 0; $i < count($ids); $i++) {
            Cart::where('id', $ids[$i])->update(['checked' => $checked[$i]]);
        }

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::CART_ITEM_QUANTITY_UPDATED_SUCCESS, null);
    }

}

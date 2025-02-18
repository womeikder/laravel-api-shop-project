<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Goods;
use App\Models\Order;
use App\Models\Slide;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    /**
     * 返回主页的详情数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

//        dd(Carbon::now()->addHour());
        // 轮播图
        $slides = Slide::where('status', 1)
            ->orderBy('seq')
            ->limit(6)
            ->get();

        // 分类商品
        $categories = Category::where('level',1)
            ->where('status', 1)
            ->with('children.children')
            ->get();

        // 推荐商品
        $goods = Goods::where('status', 1)
            ->where('recommend', 1)
            ->get();

        // 当前用户购物车数量


        // 当前用户订单状态
        $order = null;
        $cart = [];
        $cartCount = 0;
        $allStatus = ['1', '2', '3', '4', '5'];
        $finalStatusCounts = [];
        $statusCounts = [];
        if (auth('api')->id()) {
            $order = Order::select('status')->where('user_id', auth('api')->id());
                $statusCounts = $order->groupBy('status')
                    ->selectRaw('status, COUNT(*) as count')
                    ->get();

            $cart = Cart::where('user_id', auth('api')->id())->get();

        }


        // 遍历购物车中的商品数量
        foreach ($cart as $item) {
            $cartCount += $item->count;
        }

        // 遍历结果，将结果存储在关联数组中
        foreach ($statusCounts as $statusCount) {
            $finalStatusCounts[$statusCount->status] = $statusCount->count;
        }

        // 对于未出现的状态，设置其数量为 0
        foreach ($allStatus as $status) {
            if (!isset($finalStatusCounts[$status])) {
                $finalStatusCounts[$status] = 0;
            }
        }

        // 将购物车数据放入数组中
        $finalStatusCounts[5] = $cartCount;

        // 处理数据
        $data = [
            'slides' => $slides,
            'categories' => $categories,
            'goods' => $goods,
            'orders' => $finalStatusCounts
        ];

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::MENU_QUERY_SUCCESS, $data);
    }

    public function user() {
        // 推荐商品
        $goods = Goods::where('status', 1)
            ->where('recommend', 1)
            ->limit(4)
            ->get();

        // 当前用户购物车数量


        // 当前用户订单状态
        $order = null;
        $cart = [];
        $cartCount = 0;
        $allStatus = ['1', '2', '3', '4', '5'];
        $finalStatusCounts = [];
        $statusCounts = [];
        if (auth('api')->id()) {
            $order = Order::select('status')->where('user_id', auth('api')->id());
            $statusCounts = $order->groupBy('status')
                ->selectRaw('status, COUNT(*) as count')
                ->get();

            $cart = Cart::where('user_id', auth('api')->id())->get();

        }


        // 遍历购物车中的商品数量
        foreach ($cart as $item) {
            $cartCount += $item->count;
        }

        // 遍历结果，将结果存储在关联数组中
        foreach ($statusCounts as $statusCount) {
            $finalStatusCounts[$statusCount->status] = $statusCount->count;
        }

        // 对于未出现的状态，设置其数量为 0
        foreach ($allStatus as $status) {
            if (!isset($finalStatusCounts[$status])) {
                $finalStatusCounts[$status] = 0;
            }
        }

        // 将购物车数据放入数组中
        $finalStatusCounts[5] = $cartCount;

        // 处理数据
        $data = [
            'goods' => $goods,
            'orders' => $finalStatusCounts
        ];

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::MENU_QUERY_SUCCESS, $data);
    }


}

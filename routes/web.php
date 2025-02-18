<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::group(['prefix' => 'web'], function () {

    // 首页的数据
    Route::get('index', [\App\Http\Controllers\Web\IndexController::class, 'index']);

    // 商品详情
    Route::get('goods/{id}', [\App\Http\Controllers\Web\GoodsController::class, 'show']);

    // 商品列表
    Route::get('goods', [\App\Http\Controllers\Web\GoodsController::class, 'index']);
    Route::get('recommend/goods', [\App\Http\Controllers\Web\GoodsController::class, 'recommend']);

    // 需要登陆验证的路由组
    Route::group(['middleware' => 'api.auth'], function () {
        // 个人中心
        Route::get('user', [\App\Http\Controllers\Web\UserController::class, 'userInfo']);
        Route::put('user', [\App\Http\Controllers\Web\UserController::class, 'userUpdate']);

        // 购物车
        Route::put('carts/checked', [\App\Http\Controllers\Web\CartController::class, 'check']);
        Route::apiResource('carts', \App\Http\Controllers\Web\CartController::class)->except('show');

        // 立即购买
        Route::get('buy', [\App\Http\Controllers\Web\OrderController::class, 'buy']);

        // 订单确定页
        Route::get('order/preview', [\App\Http\Controllers\Web\OrderController::class, 'preview']);

        // 订单提交
        Route::post('order/post', [\App\Http\Controllers\Web\OrderController::class, 'store']);

        // 支付结算订单详情
        Route::get('payment/show', [\App\Http\Controllers\Web\OrderController::class, 'show']);

        // 订单支付
        Route::post('payment', [\App\Http\Controllers\Web\OrderController::class, 'payment']);

        // 订单列表
        Route::get('orders', [\App\Http\Controllers\Web\OrderController::class, 'index']);

        // 物流信息
        Route::get('order/express', [\App\Http\Controllers\Web\OrderController::class, 'express']);

        // 确认收货
        Route::put('order/receive', [\App\Http\Controllers\Web\OrderController::class, 'receive']);

        // 删除订单
        Route::delete('order/cancel', [\App\Http\Controllers\Web\OrderController::class,'destroy']);

        // 订单状态
        Route::get('order/status', [\App\Http\Controllers\Web\OrderController::class,'status']);
        // 地址
        Route::get('city', [\App\Http\Controllers\Web\CityController::class, 'index']);

        // 设置默认地址
        Route::put('default/{address}', [\App\Http\Controllers\Web\AddressController::class, 'default']);

        // 地址管理
        Route::apiResource('address', \App\Http\Controllers\Web\AddressController::class);

        // 订单评价
        Route::apiResource('comment', \App\Http\Controllers\Web\CommentController::class);

        // 用户中心
        Route::get('user/index', [\App\Http\Controllers\Web\IndexController::class, 'user']);
    });
});

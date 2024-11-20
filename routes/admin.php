<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * 后台管理
 */
Route::group(['prefix' => 'admin', 'middleware' => ['api.auth', 'bindings']], function () {

    // 用户相关路由
    Route::put('user/{user}/lock', [\App\Http\Controllers\Admin\UserController::class, 'lock']);
    Route::apiResource('user', \App\Http\Controllers\Admin\UserController::class)->only('index','show');

    // 分类相关路由
    Route::put('category/{category}/status', [\App\Http\Controllers\Admin\CategoryController::class, 'status']);
    Route::apiResource('category', \App\Http\Controllers\Admin\CategoryController::class)->except('destroy');

    // 商品相关路由
    Route::put('goods/{goods}/status', [\App\Http\Controllers\Admin\GoodsController::class, 'status']);
    Route::put('goods/{goods}/recommend', [\App\Http\Controllers\Admin\GoodsController::class, 'recommend']);
    Route::apiResource('goods', \App\Http\Controllers\Admin\GoodsController::class)->except('destroy');

    // 评论相关商品
    Route::get('comments', [\App\Http\Controllers\Admin\CommentController::class, 'index']);
    Route::get('comments/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'show']);
    Route::put('comments/{comment}/reply', [\App\Http\Controllers\Admin\CommentController::class, 'reply']);

    // 订单管理
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index']);
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show']);
    Route::put('orders/{order}/post', [\App\Http\Controllers\Admin\OrderController::class, 'post']);

    // 轮播图管理
    Route::put('slides/{slide}/seq', [\App\Http\Controllers\Admin\SlideController::class, 'seq']);
    Route::apiResource('slides', \App\Http\Controllers\Admin\SlideController::class);

    // 菜单管理
    Route::apiResource('menus', \App\Http\Controllers\Admin\MenuController::class)->except('show','destroy');
});

<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * 后台管理
 */
Route::group(['prefix' => 'admin', 'middleware' => ['api.auth', 'bindings']], function () {

    // 用户相关路由
    Route::get('user/info', [\App\Http\Controllers\Admin\UserController::class, 'info'])->name('users.info');
    Route::put('user/{user}/lock', [\App\Http\Controllers\Admin\UserController::class, 'lock'])->name('users.lock');
    Route::apiResource('user', \App\Http\Controllers\Admin\UserController::class)->only('index','show', 'destroy');

    // 分类相关路由
    Route::put('category/{category}/status', [\App\Http\Controllers\Admin\CategoryController::class, 'status']);
    Route::get('category/list', [\App\Http\Controllers\Admin\CategoryController::class, 'list']);
    Route::apiResource('category', \App\Http\Controllers\Admin\CategoryController::class)->except('destroy');

    // 商品相关路由
    Route::apiResource('goods', \App\Http\Controllers\Admin\GoodsController::class);

    // 评论相关商品
    Route::get('comments', [\App\Http\Controllers\Admin\CommentController::class, 'index']);
    Route::get('comments/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'show']);
    Route::put('comments/{comment}/reply', [\App\Http\Controllers\Admin\CommentController::class, 'reply']);

    // 订单管理
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index']);
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show']);
    Route::put('orders/{order}/post', [\App\Http\Controllers\Admin\OrderController::class, 'post']);

    // 轮播图管理
    Route::put('slides/seq', [\App\Http\Controllers\Admin\SlideController::class, 'seq']);
    Route::apiResource('slides', \App\Http\Controllers\Admin\SlideController::class);

    // 菜单管理
    Route::apiResource('menus', \App\Http\Controllers\Admin\MenuController::class)->except('show','destroy');

    // 后台通知
    Route::apiResource('inform', \App\Http\Controllers\Admin\InformController::class);

    // 系统统计
    Route::get('statistics/index', [\App\Http\Controllers\Admin\StatisticsController::class, 'index']);
    Route::post('statistics/view', [\App\Http\Controllers\Admin\StatisticsController::class, 'view']);


});

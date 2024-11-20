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

    // 需要登陆验证的路由组
    Route::group(['middleware' => 'api.auth'], function () {
        // 个人中心
        Route::get('user', [\App\Http\Controllers\Web\UserController::class, 'userInfo']);
        Route::put('user', [\App\Http\Controllers\Web\UserController::class, 'userUpdate']);
    });
});

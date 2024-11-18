<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth'], function () {
    // 注册
    Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'store']);
    // 登录
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);

    // 需要登陆验证的路由组
    Route::group(['middleware' => 'api.auth'], function () {
        // 退出
        Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
        // 刷新token
        Route::get('refresh', [\App\Http\Controllers\Auth\LoginController::class, 'refresh']);
        // 阿里云OSS Token
        Route::get('oss/token', [\App\Http\Controllers\Auth\OssController::class, 'token']);
    });

});

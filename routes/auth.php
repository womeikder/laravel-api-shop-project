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
    // 获取验证码
    Route::post('login/code', [\App\Http\Controllers\Auth\LoginController::class, 'code']);

    // 需要登陆验证的路由组
    Route::group(['middleware' => 'api.auth'], function () {
        // 退出
        Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
        // 刷新token
        Route::get('refresh', [\App\Http\Controllers\Auth\LoginController::class, 'refresh']);
        // 阿里云OSS Token
        Route::get('oss/token', [\App\Http\Controllers\Auth\OssController::class, 'token']);
        // 修改密码
        Route::put('password/update', [\App\Http\Controllers\Auth\PasswordController::class, 'updatePassword']);
        // 发送邮箱验证码
        Route::get('email/code',[\App\Http\Controllers\Auth\BindController::class, 'codeEmail']);
        // 更新邮箱
        Route::put('email/update',[\App\Http\Controllers\Auth\BindController::class, 'updateEmail']);
        // 更新当前用户信息
        Route::put('current/update',[\App\Http\Controllers\Auth\BindController::class, 'update']);
        // 判断当前用户是否为管理员
        Route::get('check/admin', [\App\Http\Controllers\Auth\LoginController::class, 'checkAdmin']);
    });

});

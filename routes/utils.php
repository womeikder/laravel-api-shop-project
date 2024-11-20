<?php

use App\Http\Controllers\Utils\ImageUploadController;
use Illuminate\Support\Facades\Route;



Route::group(['prefix' => 'utils'], function () {

    // 需要登陆验证的路由组
    Route::group(['middleware' => 'api.auth'], function () {
        // 个人中心
        Route::post('/image/upload', [ImageUploadController::class, 'upload']);
    });
});

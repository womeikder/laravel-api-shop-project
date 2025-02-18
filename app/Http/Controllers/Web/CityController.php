<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use function PHPUnit\Framework\isEmpty;

class CityController extends BaseController
{
    /**
     * 获取省市县的地址
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $REDIS_PROVINCE_KEY = "laravel::province::key";
        $pid = $request->input('pid');
        if ($pid === null) {
            $pid = 1;
        }

        $city = null;

        // 省
        if ($pid === 1) {
            if (Cache::has($REDIS_PROVINCE_KEY)) {
                $city = Cache::get($REDIS_PROVINCE_KEY);
            }
            $city = City::where('pid', $pid)->get();
            Cache::put($REDIS_PROVINCE_KEY, $city);
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_ADDRESS, $city);
        }
        $city = City::where('pid', $pid)->get();
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_ADDRESS, $city);
    }
}

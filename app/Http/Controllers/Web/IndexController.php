<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Models\Category;
use App\Models\Goods;
use App\Models\Slide;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    /**
     * 返回主页的详情数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // 轮播图
        $slides = Slide::where('status', 1)
            ->orderBy('seq')
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


        // 处理数据
        $data = [
            'slides' => $slides,
            'categories' => $categories,
            'goods' => $goods
        ];

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::MENU_QUERY_SUCCESS, $data);




    }

}

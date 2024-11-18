<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\MsgController;
use App\Http\Requests\Admin\GoodsRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Goods;
use Illuminate\Http\Request;

class GoodsController extends BaseController
{
    /**
     * 分页条件查询
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 获取分页的参数
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status', false);
        $recommend = $request->input('recommend', false);
        $category_id = $request->input('category_id');
        $goods_name = $request->input('goods_name');

        // 条件分页查
        $goods = Goods::when($status !== false, function ($query) use ($status) {
            $query->where('status', $status);
        })
            ->when($recommend !== false, function ($query)  use ($recommend) {
            $query->where('recommend', $recommend);
        })
            ->when($category_id, function ($query) use ($category_id) {
            $query->where('category_id', $category_id);
        })
            ->when($goods_name, function ($query) use ($goods_name) {
            $query->where('goods_name', 'like', '%'.$goods_name.'%');
        })
            ->paginate($perPage);

        // 设置返回的的参数样式
        $res = $goods->map(function ($goods) {

            $pics_url = [];
            foreach ($goods->pics as $pic) {
                array_push($pics_url, oss_url($pic));
            }

            $content_list = [];
            $comment = Comment::where('goods_id',$goods->id)->get();
            foreach ($comment as $item) {
                array_push($content_list, $item);
            }
            return [
                'id' => $goods->id,
                'goods_name' => $goods->goods_name,
                'title' => $goods->title,
                'category_id' => $goods->category_id,
                'category_name' => Category::find($goods->category_id)->name,
                'description' => $goods->description,
                'price' => $goods->price,
                'stock' => $goods->stock,
                'cover' => $goods->cover,
                'cover_url' => oss_url($goods->cover),
                'pics' => $goods->pics,
                'pics_url' => $pics_url,
                'detail' => $goods->detail,
                'status' => $goods->status,
                'recommend' => $goods->recommend,
                'comments' => $content_list,
                'create_time' => $goods->create_time,
                'update_time' => $goods->update_time,
            ];
        });

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_SEARCH_SUCCESS, $res);
    }

    /**
     * 创建商品
     * @param GoodsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(GoodsRequest $request)
    {
        // 校验分类的错误
        $category = Category::find($request->category_id);
        if (!$category) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_NOT_FOUND, MsgController::CATEGORY_NOT_EXIST, null);
        }
        if ($category->status === 0) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_CONFLICT, MsgController::CATEGORY_NOT_EXIST, null);
        }

        // 获取token中的用户数据
        $user_id = auth('api')->id();
       // 将用户id添加到请求数据中
        $request->offsetSet('user_id', $user_id);
       // 创建数据
        Goods::create($request->all());

        return $this->successResponse(CodeController::SUCCESS_CREATED, MsgController::PRODUCT_CREATE_SUCCESS, null);
    }

    /**
     * 商品详情
     * @param Goods $good
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Goods $good)
    {
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_SEARCH_SUCCESS,$good);
    }

    /**
     * 更新商品
     * @param GoodsRequest $request
     * @param Goods $good
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(GoodsRequest $request, Goods $good)
    {
        // 校验分类的错误
        $category = Category::find($request->category_id);
        if (!$category) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_NOT_FOUND, MsgController::CATEGORY_NOT_EXIST, null);
        }
        if ($category->status === 0) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_CONFLICT, MsgController::CATEGORY_NOT_EXIST, null);
        }

        $good->update($request->all());

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_UPDATE_SUCCESS, null);

    }

    /**
     * 商品销售状态
     * @param Goods $goods
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Goods $goods)
    {
        $goods->status = $goods->status === 0 ? 1 : 0;
        $goods->save();

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_UPDATE_SUCCESS, null);
    }

    /**
     * 商品是否条件
     * @param Goods $goods
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommend(Goods $goods)
    {
        $goods->recommend = $goods->recommend === 0 ? 1 : 0;
        $goods->save();

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_UPDATE_SUCCESS, null);
    }
}

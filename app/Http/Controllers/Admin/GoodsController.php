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
        // 获取分页参数
        $page = $request->input('page', 1); // 默认第一页
        $perPage = $request->input('per_page', 10); // 每页默认10条记录
        $status = $request->input('status');
        $recommend = $request->input('recommend');
        $category_id = $request->input('category_id');
        $goods_name = $request->input('goods_name');

        // 构建查询
        $goodsQuery = Goods::with(['comments', 'category'])
            ->when(!is_null($status), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when(!is_null($recommend), function ($query) use ($recommend) {
                $query->where('recommend', $recommend);
            })
            ->when(!is_null($category_id), function ($query) use ($category_id) {
                $query->where('category_id', $category_id);
            })
            ->when(!is_null($goods_name), function ($query) use ($goods_name) {
                $query->where('goods_name', 'like', '%' . $goods_name . '%');
            });

        // 执行分页查询
        $goodsPaginator = $goodsQuery->paginate($perPage, ['*'], 'page', $page);

        // 如果请求的每页数量大于等于总记录数，则设置为总记录数
        if ($perPage >= $goodsPaginator->total()) {
            $goodsPaginator = $goodsQuery->paginate($goodsPaginator->total(), ['*'], 'page', 1);
        }

        // 设置返回的数据样式并保留分页信息
        $formattedData = $goodsPaginator->getCollection()->transform(function ($goods) {
            return [
                'id' => $goods->id,
                'goods_name' => $goods->goods_name,
                'title' => $goods->title,
                'category_id' => $goods->category_id,
                'category_name' => optional($goods->category)->name ?? '未分类',
                'description' => $goods->description,
                'price' => $goods->price,
                'stock' => $goods->stock,
                'cover' => $goods->cover,
                'cover_url' => $goods->cover,
                'pics' => $goods->pics,
                'pics_url' => $goods->pics,
                'detail' => $goods->detail,
                'status' => $goods->status,
                'recommend' => $goods->recommend,
                'comments' => $goods->comments, // 直接使用预加载的 comments 关联
                'create_time' => $goods->create_time,
                'update_time' => $goods->update_time,
            ];
        });

        // 返回分页信息和转换后的商品列表
        return $this->successResponse(
            CodeController::SUCCESS_OK,
            MsgController::PRODUCT_SEARCH_SUCCESS,
            [
                'current_page' => $goodsPaginator->currentPage(),
                'total' => $goodsPaginator->total(),
                'per_page' => $goodsPaginator->perPage(),
                'last_page' => $goodsPaginator->lastPage(),
                'data' => $formattedData
            ]
        );
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

    public function destroy(Goods $good)
    {
        $good->delete();
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_DELETE_SUCCESS, null);
    }
}

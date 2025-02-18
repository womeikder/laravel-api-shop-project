<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Models\Category;
use App\Models\Goods;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class GoodsController extends BaseController
{

    /**
     * 商品分类列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 商品分页数据
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);
        $sale= $request->input('sale',false);
        $comment = $request->input('comments_count', false);
        $price = $request->input('price', false);
        $category = (int) $request->input('category');
        $low_price = $request->input('low_price');
        $high_price = $request->input('high_price');
        $goods_name = $request->input('goods_name');




        $query = Goods::query();

        // 根据 category 筛选
        if ($category > 0) {
            $query->where('category_id', $category);
        }

        // 根据 商品名称 筛选
        if ($goods_name !== null) {
            $query->where('goods_name', 'like', "%{$goods_name}%");
        }

        // 根据价格范围筛选
        if ($low_price!== null && $high_price!== null) {
            $query->whereBetween('price', [$low_price, $high_price]);
        } elseif ($low_price!== null) {
            $query->where('price', '>=', $low_price);
        } elseif ($high_price!== null) {
            $query->where('price', '<=', $high_price);
        }
        $query->where('status', 1);


        // 根据 sale 筛选
        if ($sale) {
            $query->orderBy('sales', 'desc');
        }
        if ($comment) {
            $query->withCount('comments');
            $query->orderBy('comments_count', 'desc');
        }
        if ($price) {
            $query->orderBy('price', 'desc');
        }

        // 执行分页
        $products = $query->paginate($perPage, ['*'], 'page', $page);

        if ($perPage >= $products->total()) {
            $products = $query->paginate($products->total(), ['*'], 'page', 1);
        }

        // 设置返回的数据样式并保留分页信息
        $formattedData = $products->getCollection()->transform(function ($goods) {
            return [
                'id' => $goods->id,
                'goods_name' => $goods->goods_name,
                'title' => $goods->title,
                'comments_count' => $goods->comments->count(),
                'price' => $goods->price,
                'stock' => $goods->stock,
                'cover' => $goods->cover,
            ];
        });

        // 返回分页信息和转换后的商品列表
        return $this->successResponse(
            CodeController::SUCCESS_OK,
            MsgController::PRODUCT_SEARCH_SUCCESS,
            [
                'current_page' => $products->currentPage(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
                'data' => $formattedData
            ]
        );
    }




    /**
     * 查询商品详情
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $goods = Goods::where('id',$id)
            ->with([
                'comments.user' => function ($query) {
                    $query->select('id', 'name', 'avatar');
                }
            ])
            ->first();

        if ($goods == null) {
            return $this->successResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::PRODUCT_SEARCH_FAILED, null);
        }


        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_SEARCH_SUCCESS,$goods);
    }

    /**
     * 获取十条推荐商品
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommend()
    {
        $goods = Goods::take(10)->where('recommend', 1)->where('status', 1)->get();

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::PRODUCT_SEARCH_SUCCESS, $goods);
    }
}

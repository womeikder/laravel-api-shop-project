<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Http\Requests\Web\CommentRequest;
use App\Models\Comment;
use App\Models\Goods;
use App\Models\Order;
use Illuminate\Http\Request;

class CommentController extends BaseController
{
    /**
     * 获取评论列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 获取分页的参数
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $rate = $request->input('rate');
        $goods_name = $request->input('goods_name');
        $goods_id = $request->input('goods_id');

        // 条件分页查
        $commentQuery = Comment::where('user_id', auth('api')->id())
            ->when(!is_null($rate), function ($query) use ($rate) {
                $query->where('rate', $rate);
            })
            ->when(!is_null($goods_id), function ($query) use ($goods_id) {
                $query->where('goods_id', $goods_id);
            })
            ->when(!is_null($goods_name), function ($query) use ($goods_name) {
                $goods_ids = Goods::where('goods_name', 'like', '%'.$goods_name.'%');
                $query->where('goods_id', $goods_ids);
            });
        $commentPaginator = $commentQuery->paginate($perPage, ['*'], 'page', $page);
        // 如果请求的每页数量大于等于总记录数，则设置为总记录数
        if ($perPage >= $commentPaginator->total()) {
            $commentPaginator = $commentQuery->paginate($commentPaginator->total(), ['*'], 'page', 1);
        }

        // 设置返回的数据样式并保留分页信息
        $formattedData = $commentPaginator->map(function ($comment) {
            return [
                'id' => $comment->id,
                'user' => $comment->user,
                'goods' => $comment->goods,
                'rate' => $comment->rate,
                'content' => $comment->content,
                'reply' => $comment->reply,
                'pics' => $comment->pics,
                'create_time' => $comment->create_time,
                'update_time' => $comment->update_time,
            ];
        });

        // 返回分页信息和转换后的商品列表
        return $this->successResponse(
            CodeController::SUCCESS_OK,
            MsgController::PRODUCT_SEARCH_SUCCESS,
            [
                'current_page' => $commentPaginator->currentPage(),
                'total' => $commentPaginator->total(),
                'per_page' => $commentPaginator->perPage(),
                'last_page' => $commentPaginator->lastPage(),
                'data' => $formattedData
            ]
        );
    }

    /**
     * 添加评论
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CommentRequest $request)
    {
        $first = Order::where('user_id', auth('api')->id())
            ->where('status', 4)->first();
        if ($first == null) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, '当前订单商品状态异常，无法评论', null);
        }

        $comment = new Comment();
        $comment->user_id = auth('api')->id();
        $comment->goods_id = $request->goods_id;
        $comment->content = $request->comment;
        $comment->rate = $request->star;

        $comment->pics = $request->pics;
        $comment->save();

        $first->status = 5;
        $first->save();
        return $this->successResponse(CodeController::SUCCESS_CREATED, '评论成功', null);

    }

    /**
     * 查询单条评论
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Comment $comment)
    {
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::COMMENT_QUERY_SUCCESS, $comment);
    }


    /**
     * 修改评论
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        $first = Order::where('user_id', auth('api')->id())
            ->where('goods_id', $request->goods_id)
            ->where('status', 4)->first();
        if ($first == null) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, '当前订单商品状态异常，无法评论', null);
        }

        $comment->user_id = auth('api')->id();
        $comment->goods_id = $request->goods_id;
        $comment->comment = $request->comment;
        $comment->star = $request->star;
        if ($request->has('pics')) {
            $comment->pics = json_encode($request->pics);
        }
        $comment->save();
        return $this->successResponse(CodeController::SUCCESS_CREATED, '评论成功', null);

    }



    /**
     * 删除评论
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment)
    {
        comment::destroy($comment->id);
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::COMMENT_UPDATE_SUCCESS, null);
    }
}

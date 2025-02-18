<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\MsgController;
use App\Models\Comment;
use App\Models\Goods;
use Illuminate\Http\Request;

class  CommentController extends BaseController
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
        $user_id = $request->input('user_id');
        $goods_name = $request->input('goods_name');

//        dd(Comment::all());
        // 条件分页查
        $commentQuery = Comment::when(!is_null($rate), function ($query) use ($rate) {
                $query->where('rate', $rate);
            })
            ->when(!is_null($user_id) !== false, function ($query)  use ($user_id) {
                $query->where('user_id', $user_id);
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
     * 查询单条评论
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Comment $comment)
    {
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::COMMENT_QUERY_SUCCESS, $comment);
    }

    public function reply(Request $request, Comment $comment)
    {
        try {
            $request->validate([
                'reply' => 'required|max:255'
            ], [
                'reply.required' => '回复不能为空。',
                'reply.max' => '不能超过255个字符。'
            ]);
            // 如果验证通过，这里执行后续正常的业务逻辑，比如保存回复数据到数据库等操作
            $comment->reply = $request->input('reply');
            $comment->save();
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::COMMENT_UPDATE_SUCCESS, null);

        } catch (\Exception $e) {
            // 这里可以对异常进行处理，比如记录错误日志，返回错误信息给客户端等操作
            // 返回错误信息给客户端示例
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, $e->getMessage(), null);
        }

    }
}

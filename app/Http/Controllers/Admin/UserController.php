<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends BaseController
{

    /**
     * 用户列表
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 获取用户查询的参数
        $name = $request->input('name');
        $email = $request->input('email');
        $perPage = $request->input('per_page',10);
        $page = $request->input('page',1);

        // 首先进行模糊查询，然后进行分页的处理
        $usersQuery = User::when(!is_null($name), function ($query) use ($name) {
            $query->where('name', 'like','%'.$name.'%');
        })
            ->when(!is_null($email), function ($query) use ($email) {
                $query->where('email', 'like', '%'.$email.'%');
            });
        $usersPaginator = $usersQuery->paginate($perPage, ['*'], 'page', $page);
        // 如果请求的每页数量大于等于总记录数，则设置为总记录数
        if ($perPage >= $usersPaginator->total()) {
            $usersPaginator = $usersQuery->paginate($usersPaginator->total(), ['*'], 'page', 1);
        }

        // 设置返回的的参数
        $res = $usersPaginator->map(function ($user) {
           return [
               'id' => $user->id,
               'name' => $user->name,
               'email' => $user->email,
               'status' => $user->status,
               'phone' => $user->phone,
               'avatar' => $user->avatar,
               'gender' => $user->gender,
               'create_time' => $user->create_time,
               'update_time' => $user->update_time,

           ];
        });

        // 返回分页信息和转换后的商品列表
        return $this->successResponse(
            CodeController::SUCCESS_OK,
            MsgController::USER_INFO_FETCHED_SUCCESS,
            [
                'current_page' => $usersPaginator->currentPage(),
                'total' => $usersPaginator->total(),
                'per_page' => $usersPaginator->perPage(),
                'last_page' => $usersPaginator->lastPage(),
                'data' => $res
            ]
        );
    }


    /**
     * 个人用户详情
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($user)
    {
        // 查询用户信息
        $users = User::where('id',$user)
            ->get();

        if ($users->isEmpty()) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::USER_NOT_EXIST,null);
        }
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_FETCHED_SUCCESS,$users);
    }

    /**
     * 切换用户的锁定状态
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function lock($user)
    {
        //
        $users = User::find($user);

        // 判断非空
        if ($users === null) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::USER_NOT_EXIST,null);
        }
        // 更新状态
        $users->status = $users->status == 1 ? 0 : 1;
        $users->save();
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_UPDATED_SUCCESS,null);

    }


    public function info()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_FETCHED_SUCCESS, $user);

    }

    public function destroy(User $user)
    {
       $user->delete();

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_UPDATED_SUCCESS, null);


    }
}

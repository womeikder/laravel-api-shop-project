<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

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

        // 首先进行模糊查询，然后进行分页的处理
        $users = User::when($name, function ($query) use ($name) {
            $query->where('name', 'like','%'.$name.'%');
        })
            ->when($email, function ($query) use ($email) {
                $query->where('email', 'like', '%'.$email.'%');
            })
            ->paginate($perPage);

        // 设置返回的的参数
        $res = $users->map(function ($user) {
           return [
             'id' => $user->id,
             'name' => $user->name,
             'email' => $user->email,
             'create_time' => $user->create_time,
             'update_time' => $user->update_time,
             'status' => $user->status
           ];
        });

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_FETCHED_SUCCESS, $res);
    }


    /**
     * 个人用户详情
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($user)
    {
        // 查询用户信息
        $users = User::select('id','name','email','create_time','update_time', 'status')
            ->where('id',$user)
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
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\MsgController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController extends BaseController
{
    public function store(RegisterRequest $request)
    {
        $user = new User();
        $user -> status = $request -> input('status', 1);
        $user -> name = $request -> input('name');
        $user -> email = $request -> input('email');
        $user -> password = bcrypt($request -> input('password'));
        $user -> save();
        // 提交成功信息
        return $this -> successResponse(CodeController::SUCCESS_OK, MsgController::REGISTER_SUCCESS, null);
    }
}

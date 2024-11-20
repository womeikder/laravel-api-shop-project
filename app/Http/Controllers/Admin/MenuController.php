<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Requests\Admin\MenuRequest;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends BaseController
{
    /**
     * 返回菜单列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $menu = Menu::where('level',1)
            ->with('children.children')
            ->get();

        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::MENU_QUERY_SUCCESS, $menu);
    }

    /**
     * 创建分类
     * @param CategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MenuRequest $request)
    {
        // 提取请求数据并转换为数组
        $data = $request->validated();

        // 如果pid存在就将较于父类level+1
        if (isset($data['pid']) && $data['pid'] !== null) {
            try {
                $filteredData['level'] = Menu::find($data['pid'])->level + 1;
            } catch (\Exception) {
                return $this -> errorResponse(CodeController::CLIENT_ERROR_NOT_FOUND, MsgController::LOGIN_FAILED_ACCOUNT_NOT_EXIST, null);
            }
            // 判断分类级数 <= 3
            if ($filteredData['level'] > 3) {
                return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::CATEGORY_LEVEL_ERROR,null);
            }
        }

        // 过滤掉所有空值
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && $value !== '';
        });

        // 创建并保存新记录
        Menu::create($filteredData);
        return $this -> successResponse(CodeController::SUCCESS_OK, MsgController::MENU_CREATE_SUCCESS, null);
    }

    /**
     * 更新分类数据
     * @param Request $request
     * @param $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $menu)
    {
        // 提取请求数据并转换为数组
        $data = $request->all();

        // 如果pid存在就将较于父类level+1
        if (isset($data['pid']) && $data['pid'] !== null) {
            try {
                $filteredData['level'] = Menu::find($data['pid'])->level + 1;
            } catch (\Exception) {
                return $this -> errorResponse(CodeController::CLIENT_ERROR_NOT_FOUND, MsgController::LOGIN_FAILED_ACCOUNT_NOT_EXIST, null);
            }
        }

        // 判断分类级数 <= 3
        if ($filteredData['level'] > 3) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::CATEGORY_LEVEL_ERROR,null);
        }


        // 过滤掉所有空值
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && $value !== '';
        });

        // 创建并保存新记录
        Menu::where('id',$menu)->update($filteredData);
        return $this -> successResponse(CodeController::SUCCESS_OK, MsgController::MENU_UPDATED, null);
    }
}

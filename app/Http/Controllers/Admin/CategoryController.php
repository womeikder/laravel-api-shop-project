<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\MsgController;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends BaseController
{
    /**
     * 返回分类列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::where('level',1)
            ->with('children.children')
            ->get();
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::CATEGORY_QUERY_SUCCESS, $categories);
    }

    /**
     * 创建分类
     * @param CategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        // 提取请求数据并转换为数组
        $data = $request->validated();

        // 如果pid存在就将较于父类level+1
        if (isset($data['pid']) && $data['pid'] !== null) {
            try {
                $filteredData['level'] = Category::find($data['pid'])->level + 1;
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
        Category::create($filteredData);
        return $this -> successResponse(CodeController::SUCCESS_OK, MsgController::CATEGORY_CREATE_SUCCESS, null);
    }

    /**
     * 查询单个分类
     * @param $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($category)
    {
        $data = Category::select()->where('id', $category)->get();
        if ($data->isEmpty()) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::CATEGORY_NOT_EXIST,null);
        }
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::CATEGORY_QUERY_SUCCESS, $data);
    }

    /**
     * 更新分类数据
     * @param Request $request
     * @param $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $category)
    {
        // 提取请求数据并转换为数组
        $data = $request->all();

        // 如果pid存在就将较于父类level+1
        if (isset($data['pid']) && $data['pid'] !== null) {
            try {
                $filteredData['level'] = Category::find($data['pid'])->level + 1;
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
        Category::where('id',$category)->update($filteredData);
        return $this -> successResponse(CodeController::SUCCESS_OK, MsgController::CATEGORY_UPDATED, null);
    }

    /**
     * 启用禁用
     * @param $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($category)
    {
        $data = Category::find($category);
        // 判断非空
        if ($data === null) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::CATEGORY_NOT_EXIST,null);
        }
        // 更新状态
        $data->status = $data->status === 1 ? 0 : 1;
        $data->save();
        return $this->successResponse(CodeController::SUCCESS_OK, MsgController::CATEGORY_UPDATED,null);
    }

}

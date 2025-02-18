<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MsgController;
use App\Http\Requests\Web\AddressRequest;
use App\Models\Address;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use Mockery\Exception;

class AddressController extends BaseController
{
    /**
     * 显示所有的数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // 获取当前认证用户的地址信息
            $addresses = Address::where('user_id', auth('api')->id())->get();

            // 存储格式化后的数据
            $formattedData = [];

            // 遍历每个地址
            foreach ($addresses as $address) {
                // 根据地址的 city_id 获取对应的城市信息，并加载其父级和祖父级城市信息
                $city = City::where('id', $address->city_id)->with('parent.parent')->first();

                // 构建格式化后的数据数组
                $formattedData[] = [
                    'id' => $address->id,
                    'name' => $address->name,
                    'city' =>
                        $city->parent->parent->cityname . ' '.
                        $city->parent->cityname . ' '.
                        $city->cityname,
                    'detail' => $address->detail,
                    'phone' => $address->phone,
                    'is_default' => $address->is_default,
                    'city_id' => $address->city_id,
                ];
            }
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_FETCHED_SUCCESS, $formattedData);
        } catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST, $e->getMessage());
        }

    }

    /**
     * 添加用户收货地址
     * @param AddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AddressRequest $request)
    {
        $request->offsetSet('user_id', auth('api')->id());
        $address = Address::create($request->all());
        return $this->successResponse(CodeController::SUCCESS_CREATED, MsgController::USER_INFO_UPDATED_SUCCESS, ['id' => $address->id]);

    }

    /**
     * 查询详细
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        try {
            $address = Address::where('user_id', auth('api')->id())->get();
            $id = $request->input('id');
            // 根据地址的 city_id 获取对应的城市信息，并加载其父级和祖父级城市信息
            $city = City::where('id', $id)->with('parent.parent')->first();

            // 构建格式化后的数据数组
            $formattedData[] = [
                'id' => $address->id,
                'name' => $address->name,
                'city' =>
                    $city->parent->parent->cityname . ' '.
                    $city->parent->cityname . ' '.
                    $city->cityname,
                'detail' => $address->detail,
                'phone' => $address->phone,
            ];
            return $this->successResponse(CodeController::SUCCESS_OK, MsgController::USER_INFO_FETCHED_SUCCESS, $formattedData);
        }  catch (\Exception $e) {
            return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST, $e->getMessage());
        }

    }


    /**
     * 更新用户地址
     * @param AddressRequest $request
     * @param Address $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AddressRequest $request, Address $address)
    {
        $address->update($request->all());
        return $this->successResponse(CodeController::SUCCESS_CREATED, MsgController::USER_INFO_UPDATED_SUCCESS, ['id' => $address->id]);
    }

    /**
     * 删除
     * @param Address $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Address $address)
    {
        $address->delete();
        return $this->successResponse(CodeController::SUCCESS_CREATED, MsgController::USER_INFO_UPDATED_SUCCESS, null);
    }

    /**
     * 设置默认地址
     * @param Address $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function default(Address $address) {

         try {
//             dd($address);
             db::beginTransaction();
             Address::where('user_id', auth('api')->id())->update(['is_default' => 0]);
             $address->is_default = 1;
             $address->save();

             db::commit();
             return $this->successResponse(CodeController::SUCCESS_CREATED, MsgController::USER_INFO_UPDATED_SUCCESS, null);
         } catch (\Exception $e) {
             return $this->errorResponse(CodeController::CLIENT_ERROR_BAD_REQUEST, MsgController::DATA_NOT_EXIST, $e->getMessage());
         }

    }

}

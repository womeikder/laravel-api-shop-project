<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CodeController extends Controller
{
    // 成功相关
    // HTTP 200，表示请求成功，一般用于获取操作成功，如获取商品列表、用户信息等
    public const SUCCESS_OK = 200;
    // HTTP 201，表示创建资源成功，常用于创建新用户、新订单等操作
    public const SUCCESS_CREATED = 201;

    // 客户端错误相关
    // HTTP 400，表示客户端请求数据格式错误，例如注册时参数格式不对（邮箱、密码等不符合要求）或查询参数格式异常
    public const CLIENT_ERROR_BAD_REQUEST = 400;
    // HTTP 401，表示用户未授权，如未提供有效登录凭证（如 Token 缺失或无效）或尝试访问需要特定权限的接口但未授权
    public const CLIENT_ERROR_UNAUTHORIZED = 401;
    // HTTP 403，表示用户经过身份验证，但不具有访问特定资源的权限，比如普通用户访问管理员功能或不符合商品访问限制条件
    public const CLIENT_ERROR_FORBIDDEN = 403;
    // HTTP 404，表示请求的资源不存在，比如查询不存在的商品、订单、用户信息或访问不存在的 API 端点
    public const CLIENT_ERROR_NOT_FOUND = 404;
    // HTTP 405，表示客户端使用了不被允许的请求方法，例如 API 只接受 GET 请求，而客户端使用了 POST 请求
    public const CLIENT_ERROR_METHOD_NOT_ALLOWED = 405;
    // HTTP 409，表示请求与服务器当前状态冲突，例如创建用户时用户名已存在，或对订单进行不允许的操作（如已支付订单再次支付）
    public const CLIENT_ERROR_CONFLICT = 409;

    // 服务器错误相关
    // HTTP 500，表示服务器内部出现未知错误，如数据库连接问题、服务器端代码异常（未捕获的异常）
    public const SERVER_ERROR_INTERNAL_SERVER_ERROR = 500;
    // HTTP 502，表示服务器作为网关或代理，从上游服务器收到无效响应，例如依赖的第三方支付接口返回错误信息
    public const SERVER_ERROR_BAD_GATEWAY = 502;
    // HTTP 503，表示服务器当前无法处理请求，通常是服务器过载或正在维护，如商城大促时服务器压力过大
    public const SERVER_ERROR_SERVICE_UNAVAILABLE = 503;

    // 自定义状态码（业务逻辑相关）
    // 表示用户登录成功
    public const CUSTOM_CODE_LOGIN_SUCCESS = 10001;
    // 表示登录失败，原因是账号在系统中不存在
    public const CUSTOM_CODE_LOGIN_FAILED_ACCOUNT_NOT_EXIST = 10002;
    // 表示登录失败，原因是密码错误
    public const CUSTOM_CODE_LOGIN_FAILED_PASSWORD_ERROR = 10003;
    // 表示用户注册成功
    public const CUSTOM_CODE_REGISTER_SUCCESS = 10004;
    // 表示注册失败，原因是账号已存在
    public const CUSTOM_CODE_REGISTER_FAILED_ACCOUNT_EXISTS = 10005;
    // 表示注册失败，原因是提交的注册数据不符合要求（如密码不符合规则、其他参数格式错误等）
    public const CUSTOM_CODE_REGISTER_FAILED_INVALID_DATA = 10006;
    // 表示商品搜索成功
    public const CUSTOM_CODE_PRODUCT_SEARCH_SUCCESS = 10007;
    // 表示商品搜索失败
    public const CUSTOM_CODE_PRODUCT_SEARCH_FAILED = 10008;
    // 表示商品添加到购物车成功
    public const CUSTOM_CODE_PRODUCT_ADDED_TO_CART_SUCCESS = 10009;
    // 表示商品添加到购物车失败，原因是库存不足
    public const CUSTOM_CODE_PRODUCT_ADDED_TO_CART_FAILED_INVENTORY = 10010;
    // 表示商品添加到购物车失败，其他一般性原因（如网络问题、系统错误等）
    public const CUSTOM_CODE_PRODUCT_ADDED_TO_CART_FAILED_GENERAL = 10011;
    // 表示获取购物车信息成功
    public const CUSTOM_CODE_CART_INFO_FETCH_SUCCESS = 10012;
    // 表示获取购物车信息失败
    public const CUSTOM_CODE_CART_INFO_FETCH_FAILED = 10013;
    // 表示更新购物车商品数量成功
    public const CUSTOM_CODE_CART_ITEM_QUANTITY_UPDATED_SUCCESS = 10014;
    // 表示更新购物车商品数量失败
    public const CUSTOM_CODE_CART_ITEM_QUANTITY_UPDATED_FAILED = 10015;
    // 表示从购物车中删除商品成功
    public const CUSTOM_CODE_CART_ITEM_DELETED_SUCCESS = 10016;
    // 表示从购物车中删除商品失败
    public const CUSTOM_CODE_CART_ITEM_DELETED_FAILED = 10017;
    // 表示订单创建成功
    public const CUSTOM_CODE_ORDER_CREATED_SUCCESS = 10018;
    // 表示订单创建失败，原因是库存不足
    public const CUSTOM_CODE_ORDER_CREATED_FAILED_INVENTORY = 10019;
    // 表示订单创建失败，其他一般性原因（如参数错误、系统问题等）
    public const CUSTOM_CODE_ORDER_CREATED_FAILED_GENERAL = 10020;
    // 表示订单查询成功
    public const CUSTOM_CODE_ORDER_QUERIED_SUCCESS = 10021;
    // 表示订单查询失败
    public const CUSTOM_CODE_ORDER_QUERIED_FAILED = 10022;
    // 表示订单支付成功
    public const CUSTOM_CODE_ORDER_PAID_SUCCESS = 10023;
    // 表示订单支付失败
    public const CUSTOM_CODE_ORDER_PAID_FAILED = 10024;
    // 表示获取用户信息成功
    public const CUSTOM_CODE_USER_INFO_FETCH_SUCCESS = 10025;
    // 表示获取用户信息失败
    public const CUSTOM_CODE_USER_INFO_FETCH_FAILED = 10026;
    // 表示更新用户信息成功
    public const CUSTOM_CODE_USER_INFO_UPDATED_SUCCESS = 10027;
    // 表示更新用户信息失败
    public const CUSTOM_CODE_USER_INFO_UPDATED_FAILED = 10028;
    // 缺少必填参数
    public const LOSS_NEED_ARGUMENT = 10029;
}

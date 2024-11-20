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
}

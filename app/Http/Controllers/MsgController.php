<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MsgController extends Controller
{
    public const DATA_NOT_EXIST = '查询数据不存在。';
    public const EMAIL_SEND_SUCCESS = '邮件已发送';
    public const EMAIL_CODE_ERROR = '邮箱验证码错误';
    public const EMAIL_CODE_INTERVAL = '验证码发送过快,请等待上次发送60S后再试';
    public const IMAGE_UPLOAD_SUCCESS = '图片上传成功';
    public const IMAGE_UPLOAD_ERROR = '图片上传失败';
    // 验证相关
    public const ID_MUST_DELIVERY = '主键ID为必传参数';
    // 登录相关
    public const LOGIN_SUCCESS = '登录成功，欢迎回来！';
    public const LOGIN_FAILED_ACCOUNT_NOT_EXIST = '账号不存在，请检查后重新输入。';
    public const LOGIN_FAILED_ACCOUNT_DISABLED = '该账号已被停用，请联系客服。';
    public const LOGOUT_SUCCESS = '退出登录成功';
    public const ERROR_UNAUTHORIZED = '用户未授权，未提供有效登录凭证(token)';
    public const LOGIN_ERROR = '登录失败、请检查邮箱或密码后重试!';

    // 注册相关
    public const REGISTER_SUCCESS = '注册成功，您可以使用新账号登录了。';
    public const REGISTER_FAILED_ACCOUNT_EXISTS = '该账号已被注册，请重新选择一个账号。';
    public const REGISTER_FAILED_ACCOUNT_FORMAT_INVALID = '账号格式不正确，只能包含字母、数字和下划线，且长度在4-16内。';
    public const REGISTER_FAILED_PASSWORD_SECURITY_INVALID = '密码不符合安全要求，需包含大小写字母、数字和特殊字符。';
    public const REGISTER_FAILED_EMAIL_FORMAT_INVALID = '邮箱格式不正确，请重新输入。';
    public const REGISTER_FAILED_EMAIL_USED = '该邮箱已被使用，请更换邮箱。';
    public const REGISTER_FAILED_GENERAL_ERROR = '注册失败，请检查信息后重试。';

    // 商品相关
    public const PRODUCT_SEARCH_SUCCESS = '已找到相关商品。';
    public const PRODUCT_SEARCH_FAILED = '未找到符合条件的商品，请修改搜索关键词。';
    public const PRODUCT_ADDED_TO_CART_SUCCESS = '商品已成功添加到购物车。';
    public const PRODUCT_ADDED_TO_CART_FAILED = '商品添加到购物车失败，请检查商品库存或网络连接。';
    public const PRODUCT_CREATE_SUCCESS = '商品创建成功。';
    public const PRODUCT_UPDATE_SUCCESS = '商品更新成功。';
    public const PRODUCT_DELETE_SUCCESS = '商品删除成功。';

    // 购物车相关
    public const CART_INFO_FETCH_SUCCESS = '已成功获取您的购物车信息。';
    public const CART_INFO_FETCH_FAILED = '获取购物车信息失败，请稍后再试。';
    public const CART_ITEM_QUANTITY_UPDATED_SUCCESS = '购物车中商品的数量已更新。';
    public const CART_ITEM_QUANTITY_UPDATED_FAILED = '更新购物车商品数量失败，请检查输入值。';
    public const CART_ITEM_DELETED_SUCCESS = '商品已从购物车中删除。';
    public const CART_ITEM_DELETED_FAILED = '删除购物车商品失败，请稍后再试。';

    // 订单相关
    public const ORDER_CREATED_SUCCESS = '订单创建成功，您可以在订单列表中查看详情。';
    public const ORDER_CREATED_FAILED = '订单创建失败，请检查商品库存和收货信息。';
    public const ORDER_QUERIED_SUCCESS = '已成功获取您的订单信息。';
    public const ORDER_QUERIED_FAILED = '订单查询失败，请稍后再试。';
    public const ORDER_PAID_SUCCESS = '订单支付成功，感谢您的购买！';
    public const ORDER_POST_SUCCESS = '发货信息已更新';
    public const ORDER_PAID_ERROR = '订单支付失败，该订单已经支付，请勿重复支付。';
    public const ORDER_EXPIRE = '长时间未支付,订单已过期，请重新下单';

    // 用户信息相关
    public const USER_INFO_FETCHED_SUCCESS = '已成功获取用户信息。';
    public const USER_INFO_FETCHED_FAILED = '获取用户信息失败，请稍后再试。';
    public const USER_INFO_UPDATED_SUCCESS = '您的个人信息已更新成功。';
    public const USER_INFO_UPDATED_FAILED = '更新用户信息失败，请检查输入内容。';
    public const USER_TOKEN_REFRESH = 'Token信息已刷新';
    public const USER_NOT_EXIST = '用户不存在';
    public const USER_ADDRESS = '用户收货地址';

    // 分类相关
    public const CATEGORY_CREATE_SUCCESS = '分类创建成功.';
    public const CATEGORY_LEVEL_ERROR = '分类层级错误 (最多三层)';
    public const CATEGORY_QUERY_SUCCESS = '分类查询成功。';
    public const CATEGORY_NOT_EXIST = '该查询分类不存在.';
    public const CATEGORY_UPDATED = '分类数据已更新。';

    // 评论相关
    public const COMMENT_QUERY_SUCCESS = '商品评论查询成功';
    public const COMMENT_UPDATE_SUCCESS = '商品评论更新成功';

    // 轮播图
    public const SLIDE_CREATED = '轮播图创建成功。';
    public const SLIDE_QUERY_SUCCESS = '轮播图查询成功。';
    public const SLIDE_UPDATE_SUCCESS = '轮播图更新成功。';
    public const SLIDE_DELETED_SUCCESS = '轮播图已删除。';

    // 菜单相关
    public const MENU_QUERY_SUCCESS = '菜单查询成功。';
    public const MENU_CREATE_SUCCESS = '菜单创建成功.';
    public const MENU_UPDATED = '菜单数据已更新。';

    // 通知相关
    public const INFORM_QUERY_SUCCESS = '通知查询成功。';
    public const INFORM_CREATE_SUCCESS = '通知创建成功.';
    public const INFORM_UPDATED = '通知数据已更新。';
    public const INFORM_DELETED_SUCCESS = '通知已删除。';
    public const INFORM_UNAUTHORIZED = '用户未授权，不可修改他人发布的通知。';
}

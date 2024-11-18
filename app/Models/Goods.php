<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    use HasFactory;
    // 指定自定义的更新时间戳字段
    const UPDATED_AT = 'update_time';

    // 如果你还需要自定义创建时间戳字段，可以这样做
    const CREATED_AT = 'create_time';

    // 设置模型可填充的参数
    protected $fillable = ['user_id', 'goods_name', 'title', 'category_id', 'description', 'price', 'stock', 'cover', 'pics', 'status', 'recommend', 'detail'];

    /**
     * 强制转换数组
     * @var string[]
     */
    protected $casts = [
        'pics' => 'array'
    ];
}

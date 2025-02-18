<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // 指定自定义的更新时间戳字段
    const UPDATED_AT = 'update_time';

    // 如果你还需要自定义创建时间戳字段，可以这样做
    const CREATED_AT = 'create_time';

    /**
     * 强制转换数组
     * @var string[]
     */
    protected $casts = [
        'pics' => 'array'
    ];

    /**
     * 评论所属的用户
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 评论所属的商品
     */
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }
}

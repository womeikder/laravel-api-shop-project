<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // 指定自定义的更新时间戳字段
    const UPDATED_AT = 'update_time';

    // 如果你还需要自定义创建时间戳字段，可以这样做
    const CREATED_AT = 'create_time';



    protected $fillable = ['user_id', 'order_no', 'amount', 'address', 'expire_time', 'status'];

    /**
     * 关联用户表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 关联细节表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDetail()
    {
        return $this->hasMany(OrderDetails::class, 'order_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    use HasFactory;

    // 指定自定义的更新时间戳字段
    const UPDATED_AT = 'update_time';

    // 如果你还需要自定义创建时间戳字段，可以这样做
    const CREATED_AT = 'create_time';
}

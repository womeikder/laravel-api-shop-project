<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    // 指定自定义的更新时间戳字段
    const UPDATED_AT = 'update_time';

    // 如果你还需要自定义创建时间戳字段，可以这样做
    const CREATED_AT = 'create_time';

    // 定义全需要被添加的字段
    protected $fillable = ['name', 'pid', 'status', 'level'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            // 过滤掉所有空值
            foreach ($category->getAttributes() as $key => $value) {
                if (is_null($value) || $value === '') {
                    $category->unsetAttribute($key);
                }
            }
        });
    }

    /**
     * 分类的子类
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
  public function children()
  {
      // 一对多键关联
      return $this->hasMany(Category::class,'pid','id');
  }
}

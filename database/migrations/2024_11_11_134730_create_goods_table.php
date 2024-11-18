<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('创建者');
            $table->integer('category_id')->comment('分类');
            $table->string('description')->comment('描述');
            $table->integer('price')->comment('价格');
            $table->integer('stock')->comment('库存');
            $table->string('cover')->comment('封面图');
            $table->json('pics')->comment('图片集合');
            $table->tinyInteger('status')->default(0)->comment('销售状态 1在售 0 下架');
            $table->tinyInteger('recommend')->default(0)->comment('是否推荐 1推荐 0不推荐');
            $table->text('detail')->comment('详情');
            $table->timestamp('create_time');
            $table->timestamp('update_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};

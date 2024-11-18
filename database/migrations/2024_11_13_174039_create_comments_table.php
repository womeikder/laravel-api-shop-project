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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('评论的用户');
            $table->integer('goods_id')->comment('所属商品');
            $table->tinyInteger('rate')->comment('评论级别 (1 好评 2 中评 3 差评)');
            $table->string('content')->comment('评论的内容');
            $table->string('reply')->nullable()->comment('商家回复');
            $table->json('pics')->nullable()->comment('评价图片数组');
            $table->timestamp('create_time');
            $table->timestamp('update_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};

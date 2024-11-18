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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('分类名称');
            $table->integer('pid')->default(0)->comment('父级id');
            $table->tinyInteger('status')->default(1)->comment('状态1启用，0禁用');
            $table->tinyInteger('level')->default(1)->comment('层级 1 2 3');
            $table->timestamp('create_time');
            $table->timestamp('update_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

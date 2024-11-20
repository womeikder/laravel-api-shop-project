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
        Schema::create('informs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('发布人');
            $table->string('title')->comment('通知标题');
            $table->text('content')->comment('具体通知');
            $table->tinyInteger('status')->comment('通知状态 1发布, 2删除');
            $table->timestamp('create_time');
            $table->timestamp('update_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informs');
    }
};

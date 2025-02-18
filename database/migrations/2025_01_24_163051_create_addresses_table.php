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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->string('name')->comment('收货人');
            $table->integer('city_id')->comment('地址id');
            $table->string('detail')->comment('详细地址');
            $table->string('phone')->comment('手机号');
            $table->tinyInteger('is_default')->default(0)->comment('是否为默认地址');
            $table->timestamp('create_time');
            $table->timestamp('update_time');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

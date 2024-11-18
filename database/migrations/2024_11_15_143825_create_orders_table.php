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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('order_no')->comment('单号');
            $table->integer('user_id')->comment('下单的用户');
            $table->double('amount')->comment('金额');
            $table->integer('status')->default(1)->comment('1下单 2支付 3发货 4收货');
            $table->string('address')->comment('收货地址');
            $table->string('express_type')->comment('快递类型 SF YT YD');
            $table->string('express_no')->comment('快递单号');
            $table->timestamp('pay_time')->nullable()->comment('支付时间');
            $table->string('pay_type')->nullable()->comment('支付类型 支付宝 微信 银行卡');
            $table->string('trade_no')->comment('交易单号');
            $table->timestamp('create_time');
            $table->timestamp('update_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

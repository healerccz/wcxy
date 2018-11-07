<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('start_time', 1);  // 开始送水时间
            $table->string('end_time', 1);  // 结束送水时间
            $table->string('dormitory', 3);  // 宿舍
            $table->string('mobile', 11);  // 宿舍
            $table->string('note', 50); // 备注
            $table->string('status', 50); // 订单状态
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

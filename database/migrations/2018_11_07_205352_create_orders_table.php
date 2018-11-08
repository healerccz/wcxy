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
            $table->unsignedInteger('user_id');
            $table->string('time', 1);  // 送水时间段
            $table->string('dormitory', 2);  // 宿舍
            $table->string('room', 3);  // 房间号
            $table->string('mobile', 11);  // 宿舍
            $table->string('note', 50)->nullable()->default(''); // 备注
            $table->string('status', 50)->default(''); // 订单状态
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

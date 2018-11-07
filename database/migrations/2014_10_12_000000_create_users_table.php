<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid')->unique();
            $table->string('nick_name')->nullable()->default('');
            $table->string('city')->nullable()->default('');
            $table->string('province')->nullable()->default('');
            $table->string('avatar_url')->nullable()->default('');
            $table->string('permission')->default('0'); // 用户权限，管理员为1，普通用户为0
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
        Schema::dropIfExists('users');
    }
}

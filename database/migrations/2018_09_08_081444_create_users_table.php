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
            $table->string('name', 255)->comment("姓名");
            $table->string('user_name', 255)->comment("用户名");
            $table->string('password', 255)->comment("密码");
            $table->string('roles', 255)->comment("用户角色");
            $table->string('last_ip', 255)->comment("最后登录ip")->default('');
            $table->tinyInteger('status')->comment("状态：1：正常；2：锁定")->default('1');
            $table->tinyInteger('error_amount')->comment("密码输错次数")->default('0');
            $table->string('avatar', 500)->comment("头像");
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table("users")->insert([
            'name' => 'admin',
            'user_name' => 'admin',
            'password' => '85316397bfcb170c8590dc724b3919da',//默认密码12345678
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
	        'avatar' => '',
            'roles' => json_encode([1])
        ]);
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

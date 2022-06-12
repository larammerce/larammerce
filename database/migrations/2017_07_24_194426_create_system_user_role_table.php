<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemUserRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_user_system_role', function (Blueprint $table) {
            $table->integer('system_user_id')->unsigned();
            $table->foreign('system_user_id')->references('id')
                ->on('system_users')->onDelete('cascade');

            $table->integer('system_role_id')->unsigned();
            $table->foreign('system_role_id')->references('id')
                ->on('system_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('system_user_role');
    }
}

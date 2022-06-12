<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirectorySystemUserRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directory_system_role', function (Blueprint $table) {
            $table->integer('system_role_id')->unsigned();
            $table->foreign('system_role_id')->references('id')
                ->on('system_roles')->onDelete('cascade');

            $table->integer('directory_id')->unsigned();
            $table->foreign('directory_id')->references('id')
                ->on('directories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('directory_system_role');
    }
}

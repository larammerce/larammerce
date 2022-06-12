<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeWebFormMessagesDataToLongText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("web_form_messages", function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::connection()
                ->getPdo()->exec("alter table web_form_messages modify data longtext not null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("web_form_messages", function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::connection()
                ->getPdo()->exec("alter table web_form_messages modify data mediumtext not null");
        });
    }
}

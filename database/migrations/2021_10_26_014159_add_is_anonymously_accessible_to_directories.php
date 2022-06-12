<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsAnonymouslyAccessibleToDirectories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("directories", function(Blueprint $table){
            $table->boolean('is_anonymously_accessible')->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("directories", function(Blueprint $table){
            $table->dropColumn("is_anonymously_accessible");
        });
    }
}

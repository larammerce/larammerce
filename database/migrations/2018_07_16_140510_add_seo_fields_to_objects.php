<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSeoFieldsToObjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('seo_keywords')->nullable();
            $table->text('seo_description')->nullable();
        });

        Schema::table('web_pages', function (Blueprint $table) {
            $table->text('seo_keywords')->nullable();
            $table->text('seo_description')->nullable();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->text('seo_keywords')->nullable();
            $table->text('seo_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

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
        Schema::table('products', function (Blueprint $table) {
            $table->dropIfExists('seo_keywords');
            $table->dropIfExists('seo_description');
        });

        Schema::table('web_pages', function (Blueprint $table) {
            $table->dropIfExists('seo_keywords');
            $table->dropIfExists('seo_description');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropIfExists('seo_keywords');
            $table->dropIfExists('seo_description');
        });
    }
}

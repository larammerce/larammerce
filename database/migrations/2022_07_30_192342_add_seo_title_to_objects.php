<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeoTitleToObjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('seo_title')->nullable();
        });

        Schema::table('web_pages', function (Blueprint $table) {
            $table->text('seo_title')->nullable();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->text('seo_title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('objects', function (Blueprint $table) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIfExists('seo_title');
            });

            Schema::table('web_pages', function (Blueprint $table) {
                $table->dropIfExists('seo_title');
            });

            Schema::table('articles', function (Blueprint $table) {
                $table->dropIfExists('seo_title');
            });
        });
    }
}

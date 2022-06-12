<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArticleType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('directories', function (Blueprint $table) {
            $table->integer('article_type')->default(0);
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->integer('content_type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('directories', function (Blueprint $table) {
            $table->dropColumn('article_type');
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('content_type');
        });
    }
}

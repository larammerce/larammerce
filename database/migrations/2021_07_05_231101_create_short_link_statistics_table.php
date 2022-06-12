<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShortLinkStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_link_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('short_link_id')->nullable();
            $table->integer('views_count')->nullable();
            $table->longText('json_data')->nullable();

            $table->foreign('short_link_id')
            ->references('id')
            ->on('short_links')
            ->onDelete('cascade');
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
        Schema::dropIfExists('short_link_statistics');
    }
}

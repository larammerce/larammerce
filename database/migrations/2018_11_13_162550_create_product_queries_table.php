<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_queries', function (Blueprint $table) {
            $table->increments('id');

            $table->string('identifier')->unique()->index();
            $table->string('title');
            $table->text('data');
            $table->integer('skip_count')->default(0);
            $table->integer('take_count')->default(1);

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
        Schema::drop('product_queries');
    }
}

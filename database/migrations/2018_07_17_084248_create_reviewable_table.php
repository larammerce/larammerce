<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviewables', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('reviewable_id')->unsigned();
            $table->string('reviewable_type');

            $table->integer('edit_count');
            $table->boolean('needs_review')->default(true);

            $table->index(['reviewable_type', 'reviewable_id']);
            $table->unique(['reviewable_type', 'reviewable_id']);
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
        Schema::drop('reviewables');
    }
}

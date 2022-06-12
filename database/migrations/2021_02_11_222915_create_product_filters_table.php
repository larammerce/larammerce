<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("product_filters", function (Blueprint $table) {
            $table->increments("id");
            $table->string("title");
            $table->string("identifier")->unique();
            $table->text("data");
            $table->unsignedInteger("product_query_id")->nullable();
            $table->foreign("product_query_id")->references("id")->on("product_queries")
                ->onDelete("cascade");

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
        Schema::dropIfExists("product_filters");
    }
}

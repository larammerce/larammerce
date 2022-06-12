<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerMetaCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("customer_meta_categories", function (Blueprint $table) {
            $table->increments("id");

            $table->string("title");
            $table->boolean("needs_admin_confirmation")->default(false);
            $table->string("form_blade_name");
            $table->longText("data");

            $table->unsignedInteger("parent_id")->nullable();
            $table->foreign("parent_id")->references("id")->on("customer_meta_categories")
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
        Schema::dropIfExists("customer_meta_categories");
    }
}

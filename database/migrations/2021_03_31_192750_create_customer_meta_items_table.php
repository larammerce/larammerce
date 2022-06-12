<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerMetaItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("customer_meta_items", function (Blueprint $table) {
            $table->bigIncrements("id");

            $table->unsignedInteger("cmc_id");
            $table->foreign("cmc_id")->references("id")->on("customer_meta_categories")
                ->onDelete("cascade");

            $table->unsignedInteger("confirmed_by")->nullable();
            $table->foreign("confirmed_by")->references("id")->on("system_users")
                ->onDelete('set null');

            $table->unsignedInteger("customer_user_id");
            $table->foreign("customer_user_id")->references("id")->on("customer_users")
                ->onDelete("cascade");

            $table->longText("data");
            $table->timestamp("admin_viewed_at")->nullable();
            $table->boolean("is_confirmed")->default(false);
            $table->boolean("is_main")->default(false);
            $table->boolean("is_hidden")->default(false);

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
        Schema::dropIfExists("customer_meta_items");
    }
}

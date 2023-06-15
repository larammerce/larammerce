<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            $table->string("title");

            $table->unsignedInteger("customer_user_id");
            $table->foreign("customer_user_id")
                ->references("id")
                ->on("customer_users")
                ->cascadeOnDelete();

            $table->unsignedBigInteger("amount");
            $table->timestamp("used_at")->nullable();
            $table->timestamp("expire_at")->nullable();

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
        Schema::dropIfExists('coupons');
    }
}

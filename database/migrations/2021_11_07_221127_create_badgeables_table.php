<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBadgeablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badgeables', function (Blueprint $table) {
            $table->unsignedInteger('badgeable_id')->index();
            $table->string('badgeable_type')->index();

            $table->unsignedInteger('badge_id');
            $table->foreign('badge_id')
                ->references('id')
                ->on('badges')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('badgeables');
    }
}

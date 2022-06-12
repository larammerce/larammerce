<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirectoryLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("directory_location", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("directory_id");
            $table->foreign("directory_id")->references("id")->on("directories")
                ->onDelete("cascade");
            $table->unsignedInteger("state_id");
            $table->foreign("state_id")->references("id")->on("states")
                ->onDelete("cascade");
            $table->unsignedInteger("city_id")->nullable();
            $table->foreign("city_id")->references("id")->on("cities")
                ->onDelete("cascade");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("directory_location");
    }
}

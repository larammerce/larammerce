<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModalRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modal_routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('modal_id');
            $table->string('route')->default('/');
            $table->boolean('children_included')->default(false);
            $table->boolean('self_included')->default(true);

            $table->foreign('modal_id')
                ->references('id')
                ->on('modals')
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
        Schema::dropIfExists('modal_routes');
    }
}

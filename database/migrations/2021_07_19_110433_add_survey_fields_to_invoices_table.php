<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSurveyFieldsToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("invoices", function(Blueprint $table){
            $table->timestamp("survey_notified_at")->nullable();
            $table->timestamp("survey_viewed_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("invoices", function(Blueprint $table){
            $table->dropColumn("survey_notified_at");
            $table->dropColumn("survey_viewed_at");
        });
    }
}

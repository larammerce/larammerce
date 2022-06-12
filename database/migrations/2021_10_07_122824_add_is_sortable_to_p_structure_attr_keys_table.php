<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSortableToPStructureAttrKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('p_structure_attr_keys', function (Blueprint $table) {
            $table->boolean('is_sortable')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('p_structure_attr_keys', function (Blueprint $table) {
            $table->dropColumn('is_sortable');
        });
    }
}

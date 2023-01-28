<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use App\Models\Directory;

class AddDepthColumnToDirectoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('directories', function (Blueprint $table) {
            $table->integer("depth")->default(0);
        });

        Directory::chunk(500,
            /**
             * @param Collection|Directory[] $directories
             */
            function (Collection|array $directories) {
                foreach ($directories as $directory) {
                    $depth = count($directory->getParentsUrlFull()) - 1;
                    $directory->update([
                        "depth" => $depth
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('directories', function (Blueprint $table) {
            $table->dropColumn("depth");
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Product;
use Carbon\Carbon;

class ChangeIsImportantToImportantAtInProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("products", function (Blueprint $table) {
            $table->timestamp("important_at")->nullable();
        });

        foreach (Product::all() as $product) {
            if ($product->is_important) {
                $product->update([
                    "important_at" => Carbon::now()
                ]);
            } else {
                $product->update([
                    "important_at" => null
                ]);
            }
        }

        Schema::table("products", function (Blueprint $table) {
            $table->dropColumn("is_important");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("products", function (Blueprint $table) {
            $table->boolean("is_important")->nullable();
        });

        foreach (Product::all() as $product) {
            if ($product->important_at === null) {
                $product->update([
                    "is_important" => false
                ]);
            } else {
                $product->update([
                    "is_important" => true
                ]);
            }
        }

        Schema::table("products", function (Blueprint $table) {
            $table->dropColumn("important_at");
        });
    }
}

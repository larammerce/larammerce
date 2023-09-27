<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWatermarkUuidToProductsTable extends Migration
{
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            $table->string("watermark_uuid", 36)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn("products", "watermark_uuid")) {
                $table->dropColumn("watermark_uuid");
            }
        });
    }
}

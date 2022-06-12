<?php

use App\Models\GalleryItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameDisableFieldInGalleryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("gallery_items", function (Blueprint $table) {
            $table->boolean("disable")->default(true)->change();
        });

        Schema::table("gallery_items", function (Blueprint $table) {
            $table->renameColumn("disable", "is_active");
        });

        foreach (GalleryItem::all() as $gallery_item) {
            $gallery_item->update([
                "is_active" => !$gallery_item->is_active
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("gallery_items", function (Blueprint $table) {
            $table->boolean("is_active")->default(false)->change();
        });

        Schema::table("gallery_items", function (Blueprint $table) {
            $table->renameColumn("is_active", "disable");
        });

        foreach (GalleryItem::all() as $gallery_item) {
            $gallery_item->update([
                "is_active" => !$gallery_item->is_active
            ]);
        }
    }
}

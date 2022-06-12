<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSeoKeywordsFullTextSearchToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE products DROP INDEX products_fulltext_index");
        DB::statement("ALTER TABLE products ADD FULLTEXT products_fulltext_index (seo_keywords, title, description, code)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE products DROP INDEX products_fulltext_index");
    }
}

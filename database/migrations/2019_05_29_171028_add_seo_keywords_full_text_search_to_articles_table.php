<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSeoKeywordsFullTextSearchToArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE articles ADD FULLTEXT articles_fulltext_index (seo_keywords, title, short_content, full_text)");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE articles DROP INDEX articles_fulltext_index");
    }
}

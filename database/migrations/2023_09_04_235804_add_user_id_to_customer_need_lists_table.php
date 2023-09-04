<?php

use App\Models\NeedList;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AddUserIdToCustomerNeedListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('customer_need_lists', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade');
        });

        NeedList::query()->chunk(1000,
            /**
             * @param NeedList[]|Collection $need_lists
             */
            function (array|Collection $need_lists) {
                foreach ($need_lists as $need_list) {
                    $need_list->update([
                        "user_id" => $need_list->customer->user_id ?? null
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('customer_need_lists', function (Blueprint $table) {
            $table->dropForeign('customer_need_lists_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}

<?php

use App\Models\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AddStatusToPaymentsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedTinyInteger("status")->default(0);
        });

        Payment::query()->chunk(100,
            /**
             * @param Payment[]|Collection<Payment> $payments
             */
            function (array|Collection $payments) {
                foreach ($payments as $payment) {
                    $payment->update(['status' => $payment->getStatus()]);
                }
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn("status");
        });
    }
}

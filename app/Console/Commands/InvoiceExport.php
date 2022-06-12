<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class InvoiceExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:export {--payment-status=} {--time-diff=} {--shipment-status=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command disables specific invoice and deletes it from fin manager server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return integer
     */
    public function handle()
    {
        $timeDiff = $this->option('time-diff');
        $shipmentStatus = $this->option('shipment-status');
        $paymentStatuses = $this->option('payment-status');
        $paymentStatuses = explode(",", $paymentStatuses);

        $result = Invoice::where("is_active", true)->whereIn('payment_status', $paymentStatuses);
        if ($shipmentStatus != null)
            $result->where('shipment_status', $shipmentStatus);
        if ($timeDiff != null)
            $result->whereRaw("timestampdiff(minute,updated_at, now()) > {$timeDiff}");

        $this->output->write($result->get()->toJson());

        return 0;
    }
}

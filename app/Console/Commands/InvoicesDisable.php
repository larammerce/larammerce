<?php

namespace App\Console\Commands;

use App\Helpers\SMSHelper;
use App\Models\Invoice;
use Illuminate\Console\Command;

class InvoicesDisable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:disable {--id=}';

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
        $this->output->write("Disabling invoice [id: " . $this->option('id') . "] ... \t ", false);
        $invoice = Invoice::find($this->option('id'));
        if ($invoice != null) {
            if ($invoice->deleteFinManRelation()) {
                $this->output->writeln("[<fg=green>✔</>]");
                $this->notifyCustomer($invoice);
                return 0; // success.
            }else {
                $this->output->writeln("[<fg=red>✘</>]");
                return 2;
            }
        }
        $this->output->writeln("[<fg=red>✘</>]");
        return 1; //invoice not found in local database
    }

    /**
     * @param Invoice $invoice
     */
    public function notifyCustomer(Invoice $invoice)
    {
        SMSHelper::send(
            "sms-invoice-disabled",
            $invoice->customer->main_phone,
            [
                'invoiceTrackingCode' => $invoice->tracking_code,
            ],
            [
                'customerName' => $invoice->customer->user->name,
            ]);
    }
}

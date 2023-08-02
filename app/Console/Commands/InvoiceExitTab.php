<?php

namespace App\Console\Commands;

use App\Enums\Invoice\ShipmentStatus;
use App\Models\Invoice;
use App\Utils\Common\SMSService;
use App\Utils\FinancialManager\ConfigProvider;
use App\Utils\FinancialManager\Factory;
use App\Utils\FinancialManager\Provider;
use Illuminate\Console\Command;

class InvoiceExitTab extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:exit_tab {--id=} {--no-notify}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command checks the invoice exit tab in warehouse';

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
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        $id = $this->option('id');
        $this->output->write("Checking invoice [id: {$id}] ...\t");

        $invoice = Invoice::find($id);
        if ($invoice) {
            $exitTab = Factory::driver()->checkExitTab($invoice->fin_relation);
            if ($exitTab) {
                $this->updateShipmentStatus($invoice);
                $financial_driver = Provider::getEnabledDriver();
                if (strlen($financial_driver) > 0 and Provider::hasDriver($financial_driver)) {
                    $financial_driver_config = ConfigProvider::getConfig($financial_driver);
                    if ($financial_driver_config->check_exit_tab_sms_notification)
                        $this->notifyCustomer($invoice);
                }

                $this->output->writeln("[<fg=green>✔</>]");
                return 0;
            } else {
                $this->output->writeln("[<fg=red>✘</>]");
                return 2;
            }
        }
        $this->output->writeln("[<fg=red>✘</>]");
        return 1;
    }

    public function updateShipmentStatus($invoice)
    {
        $invoice->shipment_status = ShipmentStatus::WAREHOUSE_EXIT_TAB;
        $invoice->save();
    }

    /**
     * @param Invoice $invoice
     */
    public function notifyCustomer(Invoice $invoice)
    {
        if ($this->option('no-notify')) {
            $this->output->write("[<fg=yellow>no-notify</>]");
            return;
        }
        SMSService::send(
            'sms-invoice-exit-tab',
            $invoice->customer->main_phone,
            [
                'invoiceTrackingCode' => $invoice->tracking_code,
            ],
            [
                'customerName' => $invoice->customer->user->name,
            ]);
    }
}

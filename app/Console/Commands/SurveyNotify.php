<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Utils\CMS\Setting\Survey\SurveyService;
use App\Utils\Common\SMSService;
use App\Utils\SMSManager\ConfigProvider;
use Carbon\Carbon;
use Illuminate\Console\Command;
use function GuzzleHttp\Psr7\str;

class SurveyNotify extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'survey:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command notifies customers to fill our survey';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle() {
        $survey_config = SurveyService::getRecord();
        if (is_string($survey_config->getDefaultSurveyUrl()) and strlen($survey_config->getDefaultSurveyUrl()) > 0) {
            $un_notified_invoices = Invoice::where("is_active", true)->where("survey_notified_at", null)->get();
            $delay_days = $survey_config->getDefaultDelayDays();
            $delay_hours = $survey_config->getDefaultDelayHours();
            foreach ($un_notified_invoices as $invoice) {
                $str_state_id = "{$invoice->state_id}";
                if ($survey_config->hasCustomState($str_state_id)) {
                    $custom_state = $survey_config->getCustomState($str_state_id);
                    $delay_days = $custom_state->custom_delay_days;
                    $delay_hours = $custom_state->custom_delay_hours;
                }

                if ($invoice->created_at->addDays($delay_days)->addHours($delay_hours)->lt(Carbon::now())) {
                    $this->notifyCustomer($invoice);
                    $invoice->update([
                        "survey_notified_at" => Carbon::now()
                    ]);
                }
            }
        }
        return 0;
    }

    /**
     * @param Invoice $invoice
     */
    public function notifyCustomer(Invoice $invoice) {
        if (ConfigProvider::canSendSMSForInvoiceSurvey()) {
            SMSService::send("sms-invoice-survey", $invoice->customer->main_phone, [
                "tracking_code" => $invoice->tracking_code
            ], [
                "customer_name" => $invoice->customer->user->name,
                "url" => route("customer.invoice.survey.show", $invoice)
            ]);
        }
    }
}

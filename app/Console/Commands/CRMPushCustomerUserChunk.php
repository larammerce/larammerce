<?php

namespace App\Console\Commands;

use App\Models\CustomerUser;
use App\Utils\CRMManager\Exceptions\CRMDriverInvalidConfigurationException;
use App\Utils\CRMManager\Factory;
use Illuminate\Console\Command;

class CRMPushCustomerUserChunk extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:push-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle() {
        try {
            $driver = Factory::driver();
        } catch (CRMDriverInvalidConfigurationException $e) {
            return 1;
        }

        // Get 10 old CustomerUsers which have not crm_lead_id field
        /** @var CustomerUser[] $customerUsers */
        $customerUsers = CustomerUser::where('crm_lead_id', null)->orWhere('crm_lead_id', '')
            ->orderBy("id", "ASC")->limit(10)->get();

        // Push them to CRM
        foreach ($customerUsers as $customerUser) {
            $driver->createOrUpdateLead($customerUser);
        }
    }
}

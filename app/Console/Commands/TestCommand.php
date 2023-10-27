<?php

namespace App\Console\Commands;

use App\Models\CustomerUser;
use App\Utils\CRMManager\Factory;
use Illuminate\Console\Command;

class TestCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->info("Hello world !");

        $driver = Factory::driver();

        /** @var CustomerUser $customer_user */
//        $customer_user = CustomerUser::where("main_phone", "09399791134")->first();
        $customer_user = CustomerUser::where("main_phone", "09214141040")->first();
        $driver->searchLead($customer_user);
    }
}

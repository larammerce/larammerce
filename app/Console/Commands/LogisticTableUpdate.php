<?php

namespace App\Console\Commands;

use App\Utils\CMS\Setting\Logistic\LogisticService;
use Illuminate\Console\Command;

class LogisticTableUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logistic-table:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates the logistic setting data every day';

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
     */
    public function handle()
    {
        LogisticService::update();
    }
}

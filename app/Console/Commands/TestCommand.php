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

        echo "Salam kamran";
    }
}

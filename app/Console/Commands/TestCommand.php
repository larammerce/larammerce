<?php

namespace App\Console\Commands;

use App\Models\Enums\Gender;
use App\Models\User;
use App\Utils\FinancialManager\Factory;
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

        $user = User::find(20);
        $user->customerUser->fin_relation = "9350";
        $user->name = "Ali";
        $user->gender = Gender::MALE;
        $driver = Factory::driver("darik");
        dd($driver->editCustomer($user, false));
    }
}

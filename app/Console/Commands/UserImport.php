<?php

namespace App\Console\Commands;

use App\Enums\Customer\Gender;
use App\Models\User;
use Illuminate\Console\Command;

class UserImport extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import';

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
        $this->info("Import the list of users!");

        $file_path = base_path("data/import_users.json");
        $import_users = json_decode(file_get_contents($file_path));

        $full_count = count($import_users);
        foreach ($import_users as $index => $import_user) {
            $this->output->write("{$index} / {$full_count} - Creating user {$import_user->name} ...");

            if (!isset($import_user->phone)) {
                $this->output->writeln("Phone number not exist; skipping.");
                continue;
            }

            $user = User::where("username", $import_user->phone)->first();
            if($user === null){
                $user = User::create([
                    'name' => $import_user->name,
                    'family' => "-",
                    'username' => $import_user->phone,
                    'email' => $import_user->email ?? null,
                    'is_system_user' => false,
                    'is_customer_user' => true,
                    'gender' => (($import_user->sex ?? "-") === "زن" ? Gender::FEMALE : Gender::MALE),
                    'is_email_confirmed' => false
                ]);
            }

            if($user->customerUser === null){
                $user->customerUser()->create([
                    'main_phone' => $import_user->phone,
                    'is_legal_person' => false,
                    'national_code' => null,
                    'is_initiated' => true,
                    'is_active' => false,
                    'credit' => 0,
                    'is_cart_checked' => false
                ]);
            }

            $this->output->writeln("User saved on DB.");
        }

        return 0;
    }
}

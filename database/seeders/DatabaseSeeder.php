<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(ColorsTableSeeder::class);
        $this->call(ProvinceCitiesTableSeeder::class);
        $this->call(TehranDistrictsSeeder::class);
        $this->call(SystemLanguageSeeder::class);
    }
}

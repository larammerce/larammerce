<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProvinceCitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Log::info("ProvinceCitiesSeeder has been started! ");
        $contents = json_decode(file_get_contents(public_path() . '/primary_data/province-cities.json'));
        $province_id = 1;
        $important_cities = ["تهران"];
        $current_country = env('SITE_CURRENT_COUNTRY', 'iran');

        foreach ($contents[0]->iran as $content) {
            DB::table('states')->insert([
                "name" => $content->name
            ]);
            foreach ($content->Cities as $city) {
                DB::table('cities')->insert([
                    "name" => $city->name,
                    "state_id" => $province_id,
                    "has_district" => in_array($city->name, $important_cities)
                ]);
            }
            if ($province_id == 7)
                Log::info("25% has been completed! ");
            else if ($province_id == 15)
                Log::info("50% has been completed! ");
            else if ($province_id == 23)
                Log::info("75% has been completed! ");
            else if ($province_id == 31)
                Log::info("100% has been completed! ");

            $province_id++;
        }
    }
}

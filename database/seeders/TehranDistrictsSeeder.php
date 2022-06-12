<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TehranDistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Log::info("TehranDistrictsSeeder has been started! ");

        $contents = json_decode(file_get_contents(public_path() . '/primary_data/tehran-areas.json'));
        $tehran_id = DB::select('select id from cities where name = :name', ['name' => 'تهران'])[0]->id;
        $districts = collect();
        foreach ($contents as $content) {
            $districts = $districts->merge($content->areas);
        }
        $districts = $districts->sort()->unique();

        foreach ($districts as $district)
            DB::table('districts')->insert([
                'name' => $district,
                'city_id' => $tehran_id
            ]);
    }
}

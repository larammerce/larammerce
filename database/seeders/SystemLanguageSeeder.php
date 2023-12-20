<?php

namespace Database\Seeders;

use App\Models\SystemLanguage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Lang;

class SystemLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = Lang::get('language.id');
        foreach ($languages as $short_name => $name) {
            SystemLanguage::query()->create([
                'short_name' => $short_name,
                'name' => $name,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('colors')->insert([
            [
                "name" => "سفید", // WHITE
                "hex_code" => "#FFFFFF"
            ],
            [
                "name" => "نقره ای", // LIGHT GRAY
                "hex_code" => "#d3d3d3"
            ],
            [
                "name" => "توسی", // SILVER
                "hex_code" => "#C0C0C0"
            ],
            [
                "name" => "خاکستری", // GRAY
                "hex_code" => "#808080"
            ],
            [
                "name" => "سیاه", // BLACK
                "hex_code" => "#000000"
            ],
            [
                "name" => "قرمز", // RED
                "hex_code" => "#FF0000"
            ],
            [
                "name" => "آلبالویی", // MAROON
                "hex_code" => "#800000"
            ],
            [
                "name" => "زرد", // YELLOW
                "hex_code" => "#FFFF00"
            ],
            [
                "name" => "زیتونی", // OLIVE
                "hex_code" => "#808000"
            ],
            [
                "name" => "لیمویی", // LIME
                "hex_code" => "#00FF00"
            ],
            [
                "name" => "سبز", // GREEN
                "hex_code" => "#008000"
            ],
            [
                "name" => "فیروزه ای", // AQUA
                "hex_code" => "#00FFFF"
            ],
            [
                "name" => "سبز دودی", // TEAL
                "hex_code" => "#008080"
            ],
            [
                "name" => "آبی", // BLUE
                "hex_code" => "#0000FF"
            ],
            [
                "name" => "لاجوردی", // NAVY
                "hex_code" => "#000080"
            ],
            [
                "name" => "سرخابی", // FUCHSIA
                "hex_code" => "#FF00FF"
            ],
            [
                "name" => "بنفش", // PURPLE
                "hex_code" => "#800080"
            ]
        ]);
    }
}

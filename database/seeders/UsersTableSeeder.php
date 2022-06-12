<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Larammerce',
            'family' => 'Admin',
            'username' => 'admin',
            'email' => 'pr@larammerce.com',
            'password' => bcrypt('123456'),
            'is_system_user' => true,
            'is_customer_user' => true,
            'created_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('system_users')->insert([
            'user_id' => 1,
            'is_super_user' => 1,
            'main_image_path' => "/admin_dashboard/images/logo.png",
            'created_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('customer_users')->insert([
            'user_id' => 1,
            'main_phone' => '+98 9226523479',
            'created_at' => date("Y-m-d H:i:s")
        ]);
    }
}

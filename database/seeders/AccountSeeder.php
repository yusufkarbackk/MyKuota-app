<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts')->insert([
            'username' => 'admin',
            'password' => 'admin',
            'phone_number' => '08123456789',
            'status' => 'available',
            'quota' => 10,
            'chrome_profile' => 'Profile 1',
            'profile_path' => 'C:\Users\user\AppData\Local\Google\Chrome\User Data\Profile 1',
            'update_status' => 'success',
            'error_log' => '',
            'is_complete' => true,
        ]);
    }
}

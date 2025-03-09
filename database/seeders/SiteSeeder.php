<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sites')->insert([
            'site' => 'tes site',
            'company' => 'tes company',
            'account_id' => 1,
            'usage' => 1,
        ]);
    }
}
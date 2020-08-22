<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            [
                'key' => 'title',
                'value' => 'Dashboard',
                'type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'key' => 'email',
                'value' => 'gibrandev@gmail.com',
                'type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'key' => 'phone',
                'value' => '089601684864',
                'type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'key' => 'address',
                'value' => 'Jl. Margonda Raya No. 1',
                'type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}

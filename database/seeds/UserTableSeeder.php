<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => 'Rizal Fatahillah',
            'email' => 'rizalfatahillah77@gmail.com',
            'password' => Hash::make('12345678'),
            'active' => 1,
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now()
        ]);
        $admin->assignRole('admin');
    }
}

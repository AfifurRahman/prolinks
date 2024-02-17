<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admin_backend')->insert([
        	'superuser_id' => Str::uuid(4),
            'first_name' => "Superadmin",
            'last_name' => 'Prolinks',
            'email' => 'superadmin@gmail.com',
            'phone' => '082133330227',
            'dob' => 'Jakarta',
            'password' => Hash::make('secret'),
            'role' => 1,
        ]);
    }
}

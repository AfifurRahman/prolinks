<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $access = ["dashboard","list-pricing","add-pricing","edit-pricing","delete-pricing","list-client","add-client","edit-client","delete-client","send-email-client","list-role","add-role","edit-role","delete-role","list-monitoring","detail-monitoring","add-users","edit-users","delete-users","list-users"];
        
        DB::table('role')->insert([
        	'id' => 1,
        	'role_name' => 'ALL',
            'access' => json_encode($access),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserAccountsModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'username' => 'sysadmin',
                'password' => bcrypt('@sysadmin!'),
                'locked' => 0,
                'code' => Str::random(6),
                'pin' => randomNumbers(6),
                'created_at' => now(),
                'created_by' => 1,
            ],
        ]);

        DB::table('accounts')->insert([
            [
                'user_id' => 1,
                'account_type' => 1,
                'full_name' => 'System Administrator',
                'email' => 'system.administrator@gmail.com',
                'mobile_number' => '09123456789',
                'created_at' => now(),
                'created_by' => 1,
            ],
            
        ]);

        DB::table('slugs')->insert([
            [
                'slug_type' => 'App\\Models\\Account',
                'slug_id' => 1,
                'full' => 'sYsAdmIn-system-administrator-account',
                'name' => 'system-administrator-account',
                'code' => 'sYsAdmIn',
                'created_at' => now(),
                'created_by' => 1,
            ],
        ]);
    }
}

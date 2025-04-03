<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrerequisitesModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('company_positions')->insert([
            [
                'name' => 'ADMIN',
                'description' => null,
                'enabled' => 1,
                'created_at' => now(),
                'created_by' => 1
            ],
            [
                'name' => 'SUPERVISOR',
                'description' => null,
                'enabled' => 1,
                'created_at' => now(),
                'created_by' => 1
            ],
            [
                'name' => 'STAFF',
                'description' => null,
                'enabled' => 1,
                'created_at' => now(),
                'created_by' => 1
            ],
        ]);
    }
}

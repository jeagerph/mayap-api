<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);

        $this->command->info('Seeding database...');

        $this->call(AccessControlModuleSeeder::class);

        $this->call(PrerequisitesModuleSeeder::class);

        $this->call(UserAccountsModuleSeeder::class);
    }
}

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
         $this->call(UsersSeeder::class);
        //$this->call(SettingsSeeder::class);
        $this->call(DocumentTypesSeeder::class);
        $this->call(ExpenseTypesSeeder::class);
        $this->call(IncomeTypesSeeder::class);
        $this->call(PropertyTypesSeeder::class);
        $this->call(ServiceTypesSeeder::class);
    }
}

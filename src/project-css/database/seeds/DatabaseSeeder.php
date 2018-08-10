<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // Call the user table seed
        $this->call(UsersTableSeeder::class);
        $this->call(StopWordsSeeder::class);
        $this->call(AllowedFileTypesSeeder::class);
        $this->call(RecordTypesSeeder::class);      
    }
}

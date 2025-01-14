<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(StateSeeder::class);
        $this->call(CitiySeeder::class);
        $this->call(AreaOfInterestSeeder::class);
        $this->call(UnitCategorySeeder::class);
        $this->call(JobSkillsSeeder::class);
    }
}

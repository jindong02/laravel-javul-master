<?php

namespace Database\Seeders;

use App\Models\UnitCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitCategories = [
            [
                'name' => "Software",
                'status'=>'approved'
            ],
            [
                'name' => "Hardware",
                'status'=>'approved'
            ],
            [
                'name' => "Automobile",
                'status'=>'approved'
            ],
            [
                'name' => "Mechanical",
                'status'=>'approved'
            ]
        ];
        foreach ($unitCategories as $unitCategory)
        {
            UnitCategory::create($unitCategory);
        }
    }
}

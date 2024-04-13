<?php

namespace Database\Seeders;

use App\Models\AreaOfInterest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaOfInterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areasOfInterests = [
            ['id'=>1,'title'=>'Artificial Intelligence'],
            ['id'=>2,'title'=>'Computer Architecture'],
            ['id'=>3,'title'=>'Computer Graphics'],
            ['id'=>4,'title'=>'Computer Optimization'],
            ['id'=>5,'title'=>'Computer Security'],
            ['id'=>6,'title'=>'Distributed Systems'],
            ['id'=>7,'title'=>'Embedded Systems'],
            ['id'=>8,'title'=>'Fault Tolerance'],
            ['id'=>9,'title'=>'Human-Computer Interaction'],
            ['id'=>10,'title'=>'Large-Scale Data Analysis and Vizualization'],
            ['id'=>11,'title'=>'Machine Learning LG'],
            ['id'=>12,'title'=>'Multimedia Systems S4'],
            ['id'=>13,'title'=>'Parallel Computing PZ'],
            ['id'=>14,'title'=>'Software Measurement'],
            ['id'=>15,'title'=>'Software Requirements'],
            ['id'=>16,'title'=>'Virtual Execution Environments'],
            ['id'=>17,'title'=>'Virtual Reality']
        ];
        foreach ($areasOfInterests as $areasOfInterest)
        {
            AreaOfInterest::create($areasOfInterest);
        }
    }
}

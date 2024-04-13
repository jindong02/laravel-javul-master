<?php

namespace Database\Seeders;

use App\Models\JobSkill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobSkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobSkills = [
            ['skill_name'=>'Communication'],
            ['skill_name'=>'Teamwork'],
            ['skill_name'=>'Problem solving'],
            ['skill_name'=>'Initiative and enterprise'],
            ['skill_name'=>'Planning and organising'],
            ['skill_name'=>'Self-management'],
            ['skill_name'=>'Learning'],
            ['skill_name'=>'Technology']
        ];
        foreach ($jobSkills as $jobSkill)
        {
            JobSkill::create($jobSkill);
        }
    }
}

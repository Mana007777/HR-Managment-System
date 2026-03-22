<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeparmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $departments = $company->departments()->createMany([
                ['name' => 'Human Resources'],
                ['name' => 'Finance'],
                ['name' => 'Engineering'],
                ['name' => 'Marketing'],
            ]);
        }

        foreach ($departments as $department) {
                switch ($department->name) {
                    case 'Human Resources':
                        $designations = 
                        [
                            'HR Manager',
                            'Recruiter',
                            'HR Coordinator',
                        ];
                        break;
                        case 'Finance':
                        $designations = 
                        [
                            'Finance Manager',
                            'Accountant',
                            'Financial Analyst',
                        ];
                        break;
                        case 'Engineering':
                        $designations = 
                        [
                            'Engineering Manager',
                            'Software Engineer',
                            'DevOps Engineer',
                        ];
                        break;
                        case 'Marketing':
                        $designations = 
                        [
                            'Marketing Manager',
                            'Marketing Specialist',
                            'Content Creator',
                        ];
                        break;
                    
                    default:
                            $designations = [];
                    break;
                }
                foreach ($designations as $designation) {
                    $department->designations()->create(['name' => $designation]);
                }
            }
    }
}

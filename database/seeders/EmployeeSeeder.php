<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        $companies = Company::all();

        foreach ($companies as $key =>$company){
            foreach($company->departments as $department){
                foreach($department->designations as $key => $designation){
                    for ($i = 0; $i < 3; $i++) {
                        Employee::create([
                            'name' => $faker->name,
                            'email' => $faker->unique()->safeEmail,
                            'phone' => $faker->phoneNumber,
                            'designation_id' => $designation->id,
                            'address' => $faker->address,
                        ]);
                    }
                }
            }
        }
    }
}

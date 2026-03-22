<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(Employee::all() as $key =>$employee) {
            $employee->contracts()->create([
                'designation_id' => $employee->designation_id,
                'rate_type' => 'monthly',
                'rate' => 5000,
                'start_date' => now()->subMonths(6),
                'end_date' => now()->addMonths(6),
            ]);
        }
    }
}

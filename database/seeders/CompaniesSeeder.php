<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'name' => 'Optimal Solutions Ltd.',
                'email' => 'info@optimalsolutions.com',
                'website' => 'https://www.optimalsolutions.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tech Solutions Inc.',
                'email' => 'info@techsolutions.com',
                'website' => 'https://www.techsolutions.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gateway Technologies',
                'email' => 'info@gatewaytech.com',
                'website' => 'https://www.gatewaytech.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Innovatech Systems',
                'email' => 'info@innovatech.com',
                'website' => 'https://www.innovatech.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            ]);

            foreach(Company::all() as $key => $company) {
                $company->users()->attach(1);
            }
    }
}

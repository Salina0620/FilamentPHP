<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            ['name' => 'Cardiology'],
            ['name' => 'Neurology'],
            ['name' => 'Radiology'],
            ['name' => 'Dermatology'],
            ['name' => 'Opthalmology'],
            ['name' => 'Hematology'],
            ['name' => 'Urology'],
            ['name' => 'Anesthesiology'],
            ['name' => 'Internal Medicine'],
            ['name' => 'Oncology'],
            ['name' => 'Emergency Medicine'],
        ]);
    }
}

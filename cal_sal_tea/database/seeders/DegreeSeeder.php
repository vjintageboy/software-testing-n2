<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DegreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('degrees')->insert([
            [
                'name' => 'Cử nhân',
                'abbreviation' => 'CN',
                'coefficient' => 1.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Thạc sĩ',
                'abbreviation' => 'ThS',
                'coefficient' => 1.2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tiến sĩ',
                'abbreviation' => 'TS',
                'coefficient' => 1.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

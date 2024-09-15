<?php

namespace Database\Seeders;

use App\Models\family;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class familiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('families')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        family::create([
            "name" => "Ali",
        ]);
        family::create([
            "name" => "Mostafa",
        ]);
        family::create([
            "name" => "Hasan",
        ]);
        family::create([
            "name" => "Mohammad",
        ]);
        family::create([
            "name" => "Salah",
        ]);
    }
}

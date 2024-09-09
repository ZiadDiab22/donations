<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\donations_type;

class DonationsTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('donations_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        donations_type::create([
            "name" => "بناء مسجد",
        ]);
        donations_type::create([
            "name" => "مساعدة مريض",
        ]);
        donations_type::create([
            "name" => "عتق رقبة",
        ]);
        donations_type::create([
            "name" => "صدقة جارية",
        ]);
        donations_type::create([
            "name" => "زكاة فطر",
        ]);
    }
}

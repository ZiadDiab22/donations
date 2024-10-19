<?php

namespace Database\Seeders;

use App\Models\home_info;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('donations_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        home_info::create([
            "title" => "title",
            "text" => "text",
            "img_url" => "123",
        ]);
    }
}

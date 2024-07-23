<?php

namespace Database\Seeders;

use App\Models\SalesData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SalesData::factory()->count(4251)->create();
    }
}

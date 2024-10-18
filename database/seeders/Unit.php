<?php

namespace Database\Seeders;

use App\Models\Unit as ModelsUnit;
use Illuminate\Database\Seeder;

class Unit extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Unit = [

            [
                "id"=> 1,
                "unit" => "PCS",
            ],
            [
                "id"=> 2,
                "unit" => "KG",
            ],
            [
                "id"=> 3,
                "unit" => "BOX",
            ],
            [
                "id"=> 4,
                "unit" => "Sachet",
            ],
            [
                "id"=> 5,
                "unit" => "Ikat",
            ],
        ];

        foreach ($Unit as $key => $val) {
            ModelsUnit::create($val);
        }
    }
}

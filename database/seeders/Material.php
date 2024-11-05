<?php

namespace Database\Seeders;

use App\Models\Material as ModelsMaterial;
use Illuminate\Database\Seeder;

class Material extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Material = [

            [
                "id" => 1,
                "material" => "Indomie Goreng",
            ],
            [
                "id" => 2,
                "material" => "Aice",
            ],
            [
                "id" => 3,
                "material" => "Tepung Tapioka",
            ],
            [
                "id" => 4,
                "material" => "Margarin",
            ],
            [
                "id" => 5,
                "material" => "Ikan Sarden",
            ],
        ];

        foreach ($Material as $key => $val) {
            ModelsMaterial::create($val);
        }
    }
}

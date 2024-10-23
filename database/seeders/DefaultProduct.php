<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultProduct extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $categories = [
            ['category' => 'Minuman'],
            ['category' => 'Makanan']
        ];

        DB::table('categories')->insert($categories);

        $drinks = [
            ['product_name' => 'Air es', 'product_code' => 'DRK01', 'product_category' => 'Minuman', 'product_price' => 1000],
            ['product_name' => 'Teh manis (es/hangat)', 'product_code' => 'DRK02', 'product_category' => 'Minuman', 'product_price' => 3000],
            ['product_name' => 'Teh tawar (es/hangat)', 'product_code' => 'DRK03', 'product_category' => 'Minuman', 'product_price' => 2500],
            ['product_name' => 'Jeruk peras (es/hangat)', 'product_code' => 'DRK04', 'product_category' => 'Minuman', 'product_price' => 5000],
            ['product_name' => 'Chocolatos Matcha', 'product_code' => 'DRK05', 'product_category' => 'Minuman', 'product_price' => 5000],
            ['product_name' => 'Chocolatos Coklat', 'product_code' => 'DRK06', 'product_category' => 'Minuman', 'product_price' => 5000],
            ['product_name' => 'Chocolatos Creamy', 'product_code' => 'DRK07', 'product_category' => 'Minuman', 'product_price' => 5000],
            ['product_name' => 'Drink Beng-beng', 'product_code' => 'DRK08', 'product_category' => 'Minuman', 'product_price' => 5000],
            ['product_name' => 'Good day', 'product_code' => 'DRK09', 'product_category' => 'Minuman', 'product_price' => 4000],
            ['product_name' => 'Indocafe', 'product_code' => 'DRK10', 'product_category' => 'Minuman', 'product_price' => 4000],
            ['product_name' => 'Kapal api', 'product_code' => 'DRK11', 'product_category' => 'Minuman', 'product_price' => 4000],
            ['product_name' => 'Tora bika', 'product_code' => 'DRK12', 'product_category' => 'Minuman', 'product_price' => 4000],
            ['product_name' => 'White coffee', 'product_code' => 'DRK13', 'product_category' => 'Minuman', 'product_price' => 4000],
            ['product_name' => 'Pop ice', 'product_code' => 'DRK14', 'product_category' => 'Minuman', 'product_price' => 4000],
            ['product_name' => 'Susu kental manis coklat', 'product_code' => 'DRK15', 'product_category' => 'Minuman', 'product_price' => 4000],
            ['product_name' => 'Susu kental manis putih', 'product_code' => 'DRK16', 'product_category' => 'Minuman', 'product_price' => 4000],
            ['product_name' => 'Mari oppa', 'product_code' => 'DRK17', 'product_category' => 'Minuman', 'product_price' => 4000],
            ['product_name' => 'Cocorio', 'product_code' => 'DRK18', 'product_category' => 'Minuman', 'product_price' => 2000],
            ['product_name' => 'Tea jus', 'product_code' => 'DRK19', 'product_category' => 'Minuman', 'product_price' => 2000],
            ['product_name' => 'Teh sisri', 'product_code' => 'DRK20', 'product_category' => 'Minuman', 'product_price' => 2000],
            ['product_name' => 'Frenta', 'product_code' => 'DRK21', 'product_category' => 'Minuman', 'product_price' => 2000],
            ['product_name' => 'Nutrisari', 'product_code' => 'DRK22', 'product_category' => 'Minuman', 'product_price' => 4000],
            ['product_name' => 'Marimas', 'product_code' => 'DRK23', 'product_category' => 'Minuman', 'product_price' => 2000],
            ['product_name' => 'Jas jus', 'product_code' => 'DRK24', 'product_category' => 'Minuman', 'product_price' => 2000],
        ];

        $foods = [
            ['product_name' => 'Nasi putih', 'product_code' => 'FD01', 'product_category' => 'Makanan', 'product_price' => 3000],
            ['product_name' => 'Bakmi jawa kuah', 'product_code' => 'FD02', 'product_category' => 'Makanan', 'product_price' => 15000],
            ['product_name' => 'Bakmi jawa goreng', 'product_code' => 'FD03', 'product_category' => 'Makanan', 'product_price' => 15000],
            ['product_name' => 'Nasi goreng terasi', 'product_code' => 'FD04', 'product_category' => 'Makanan', 'product_price' => 10000],
            ['product_name' => 'Nasi goreng mentega', 'product_code' => 'FD05', 'product_category' => 'Makanan', 'product_price' => 10000],
            ['product_name' => 'Nasi goreng kecombrang', 'product_code' => 'FD06', 'product_category' => 'Makanan', 'product_price' => 10000],
            ['product_name' => 'Telur ceplok', 'product_code' => 'FD07', 'product_category' => 'Makanan', 'product_price' => 4000],
            ['product_name' => 'Telur dadar', 'product_code' => 'FD08', 'product_category' => 'Makanan', 'product_price' => 4000],
            ['product_name' => 'Indomi goreng', 'product_code' => 'FD09', 'product_category' => 'Makanan', 'product_price' => 7000],
            ['product_name' => 'Indomi rebus', 'product_code' => 'FD010', 'product_category' => 'Makanan', 'product_price' => 7000],
            ['product_name' => 'Indomi telur', 'product_code' => 'FD11', 'product_category' => 'Makanan', 'product_price' => 10000],
        ];

        DB::table('products')->insert(array_merge($drinks, $foods));
    }
}

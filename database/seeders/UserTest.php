<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTest extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $UserData = [

            [
                'name' => 'Fitri',
                'email' => 'fitri@gmail.com',
                'password' => 'kasir123',
                'role' => 'cashier'
            ],
            [
                'name' => 'Dev Cashier',
                'email' => 'thanos@gmail.com',
                'password' => 'dev123',
                'role' => 'cashier'
            ],
            [
                'name' => 'Ara',
                'email' => 'ara@gmail.com',
                'password' => 'kasir123',
                'role' => 'cashier'
            ],
            [
                'name' => 'Agni',
                'email' => 'agni@gmail.com',
                'password' => 'kasir123',
                'role' => 'cashier'
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin123',
                'role' => 'admin'
            ],
            [
                'name' => 'Dev Admin',
                'email' => 'gatot@gmail.com',
                'password' => 'BN123',
                'role' => 'admin'
            ],
        ];

        foreach ($UserData as $key => $val) {
            User::create($val);
        }
    }
}

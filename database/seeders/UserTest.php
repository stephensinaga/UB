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
                'name' => 'Cashier',
                'email' => 'cashier@gmail.com',
                'password' => 'cashier#1',
                'role' => 'cashier'
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin#1',
                'role' => 'admin'
            ],
        ];

        foreach ($UserData as $key => $val) {
            User::create($val);
        }
    }
}

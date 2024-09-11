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
                'name' => 'Sergio Moses Riyanto',
                'email' => 'sergiomoses.r@gmail.com',
                'password' => '123456',
                'level' => 'Admin'
            ],
            [
                'name' => 'Stepanus Berkat Sinaga',
                'email' => 'stepan@gmail.com',
                'password' => '123456',
                'level' => 'Admin'
            ],
        ];

        foreach ($UserData as $key => $val) {
            User::create($val);
        }
    }
}
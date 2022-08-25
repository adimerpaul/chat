<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            ["name"=>"BART SIMPSON","foto"=>"bart.jpg","email"=>"bart@gmail.cpm","password"=>Hash::make("123"),"fechaConexion"=>"2022-08-24 10:00:00"],
            ["name"=>"LISA SIMPSON","foto"=>"lisa.jpg","email"=>"lisa@gmail.cpm","password"=>Hash::make("123"),"fechaConexion"=>"2022-08-24 10:00:00"],
            ["name"=>"HOMERO SIMPSON","foto"=>"homero.jpg","email"=>"homero@gmail.cpm","password"=>Hash::make("123"),"fechaConexion"=>"2022-08-24 10:00:00"],
            ["name"=>"MARGE SIMPSON","foto"=>"marge.jpg","email"=>"marge@gmail.cpm","password"=>Hash::make("123"),"fechaConexion"=>"2022-08-24 10:00:00"],
        ]);
    }
}

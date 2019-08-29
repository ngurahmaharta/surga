<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // php artisan db:seed --class=UsersTableSeeder

        DB::table('users')->insert([
            'name' => 'Ngurah Maharta Admin',
            'username' => 'admin',
            'email' => 'ngurahmaharta.kpm@gmail.com',
            'password' => app('hash')->make('secret'),
            'phone' => '+6287862128588',
            'status' => 'active',
            'role' => 'admin',
        ]);

        $faker = Faker::create();
        foreach(range(1,100) as $i){
            DB::table('users')->insert([
                'name' => $faker->name,
                'username' => $faker->userName,
                'email' => $faker->email,
                'password' => app('hash')->make('secret'),
                'phone' => $faker->e164PhoneNumber,
                // 'alamat' => $faker->address,
                'status' => 'active',
                'role' => 'surveyor',
                'created_by' => '1',
            ]);
        }


    }
}

<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Store;

class StoreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // php artisan db:seed --class=StoreTableSeeder

        foreach(range(1,20) as $i){
            $faker = Faker::create();
            Store::create([
                'name' => $faker->name,
                'desc' => $faker->text,
                'tags' => $faker->word,
                'address' => $faker->address,
                'longitude' => $faker->longitude($min = -180, $max = 180),
                'latitude' => $faker->latitude($min = -90, $max = 90),
                'created_by' => '1'
            ]);
        }

    }
}

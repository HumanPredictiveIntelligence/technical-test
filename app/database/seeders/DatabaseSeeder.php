<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Factory::create();

        Customer::factory(10)->create();

        $products = Product::factory(10)->create();

        // create inventory for each created products
        foreach ($products as $product) {
            DB::table('inventory')->insert([
                'product_id' => $product->id,
                'quantity' => $faker->numberBetween(0, 10),
            ]);
        }
    }
}

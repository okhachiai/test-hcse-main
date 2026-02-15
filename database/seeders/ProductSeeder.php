<?php

namespace Database\Seeders;

use App\Enums\ProductState;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $offers = Offer::all();
        $states = [ProductState::Draft->value, ProductState::Published->value, ProductState::Invisible->value];

        foreach ($offers as $offer) {
            $count = rand(5, 15);
            $products = Product::factory($count)->create(['offer_id' => $offer->id]);

            foreach ($products as $i => $product) {
                $product->update(['state' => $states[$i % 3]]);
            }
        }
    }
}

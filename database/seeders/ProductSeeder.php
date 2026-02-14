<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $offers = Offer::all();

        foreach ($offers as $offer) {
            $count = rand(2, 5);
            $products = Product::factory($count)->create(['offer_id' => $offer->id]);

            $states = ['draft', 'published', 'invisible'];
            foreach ($products as $i => $product) {
                $product->update(['state' => $states[$i % 3]]);
            }
        }
    }
}

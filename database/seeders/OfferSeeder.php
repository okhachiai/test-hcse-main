<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $this->createPlaceholderImages();

        Offer::factory(3)->published()->create();
        Offer::factory(2)->draft()->create();
        Offer::factory(1)->hidden()->create();
    }

    private function createPlaceholderImages(): void
    {
        $png1x1 = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );

        Storage::disk('public')->put('offers/placeholder.jpg', $png1x1);
        Storage::disk('public')->put('products/placeholder.jpg', $png1x1);
    }
}

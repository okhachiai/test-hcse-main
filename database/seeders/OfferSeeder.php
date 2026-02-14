<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $this->createPlaceholderImages();

        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            Offer::factory(50)->published()->for($user)->create();
            Offer::factory(30)->draft()->for($user)->create();
            Offer::factory(20)->hidden()->for($user)->create();
        }
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

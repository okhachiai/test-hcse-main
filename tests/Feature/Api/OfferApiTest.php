<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Enums\Pagination;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_only_published_offers(): void
    {
        Offer::factory()->published()->create(['name' => 'Published Offer']);
        Offer::factory()->draft()->create(['name' => 'Draft Offer']);
        Offer::factory()->hidden()->create(['name' => 'Hidden Offer']);

        $response = $this->getJson('/api/offers');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', 'Published Offer');
    }

    public function test_api_includes_only_published_products_in_offers(): void
    {
        $offer = Offer::factory()->published()->create();
        Product::factory()->published()->create(['offer_id' => $offer->id, 'name' => 'Published Product']);
        Product::factory()->draft()->create(['offer_id' => $offer->id, 'name' => 'Draft Product']);
        Product::factory()->invisible()->create(['offer_id' => $offer->id, 'name' => 'Invisible Product']);

        $response = $this->getJson('/api/offers');

        $response->assertOk();
        $response->assertJsonCount(1, 'data.0.products');
        $response->assertJsonPath('data.0.products.0.name', 'Published Product');
    }

    public function test_api_returns_pagination_structure(): void
    {
        $response = $this->getJson('/api/offers');

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total',
            ],
        ]);
        $this->assertSame(Pagination::DefaultPerPage->value, $response->json('meta.per_page'));
    }

    public function test_api_does_not_leak_internal_fields(): void
    {
        $offer = Offer::factory()->published()->create();
        Product::factory()->published()->create(['offer_id' => $offer->id]);

        $response = $this->getJson('/api/offers');

        $response->assertOk();
        $offerData = $response->json('data.0');
        $this->assertArrayNotHasKey('state', $offerData);
        $this->assertArrayNotHasKey('created_at', $offerData);
        $this->assertArrayNotHasKey('updated_at', $offerData);

        $productData = $response->json('data.0.products.0');
        $this->assertArrayNotHasKey('state', $productData);
        $this->assertArrayNotHasKey('offer_id', $productData);
        $this->assertArrayNotHasKey('created_at', $productData);
        $this->assertArrayNotHasKey('updated_at', $productData);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Domain\Enums\Pagination;
use App\Domain\StateRules\OfferStateRules;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class OfferApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_only_published_by_default_using_state_rules(): void
    {
        $apiScope = OfferStateRules::defaultScopeForApi();
        $this->assertSame('published', $apiScope->value);

        Offer::factory()->published()->create(['name' => 'Visible']);
        Offer::factory()->draft()->create(['name' => 'Hidden']);
        Offer::factory()->hidden()->create(['name' => 'Also Hidden']);

        $response = $this->getJson('/api/offers');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', 'Visible');
    }

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

    public function test_api_returns_422_for_invalid_per_page(): void
    {
        $response = $this->getJson('/api/offers?per_page=999');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['per_page']);
        $this->assertArrayHasKey('errors', $response->json());
    }

    public function test_api_returns_422_for_invalid_page(): void
    {
        $response = $this->getJson('/api/offers?page=0');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['page']);
    }

    public function test_api_returns_429_when_rate_limit_exceeded(): void
    {
        $key = 'rate-limit-test-'.uniqid();
        RateLimiter::for('api', fn () => Limit::perMinute(2)->by($key));

        $this->getJson('/api/offers');
        $this->getJson('/api/offers');
        $response = $this->getJson('/api/offers');

        $response->assertStatus(429);
        $response->assertJsonStructure(['message']);
    }

    public function test_api_uses_cache(): void
    {
        Offer::factory()->published()->create(['name' => 'Cached Offer']);

        $response1 = $this->getJson('/api/offers');
        $response2 = $this->getJson('/api/offers');

        $response1->assertOk();
        $response2->assertOk();
        $this->assertSame($response1->json('data.0.name'), $response2->json('data.0.name'));

        $version = (int) Cache::get('api:offers:version', 0);
        $this->assertGreaterThanOrEqual(0, $version);
        $cacheKey = "api:offers:v{$version}:page:1:per_page:".Pagination::DefaultPerPage->value;
        $this->assertTrue(Cache::has($cacheKey));
    }

    public function test_api_cache_invalidated_on_offer_create(): void
    {
        Offer::factory()->published()->create(['name' => 'First Offer']);

        $response1 = $this->getJson('/api/offers');
        $response1->assertOk();
        $response1->assertJsonCount(1, 'data');

        Offer::factory()->published()->create(['name' => 'Second Offer']);

        $response2 = $this->getJson('/api/offers');
        $response2->assertOk();
        $response2->assertJsonCount(2, 'data');
    }

    public function test_api_cache_invalidated_on_product_update(): void
    {
        $offer = Offer::factory()->published()->create();
        $product = Product::factory()->draft()->create(['offer_id' => $offer->id, 'name' => 'Draft Product']);

        $response1 = $this->getJson('/api/offers');
        $response1->assertOk();
        $response1->assertJsonCount(0, 'data.0.products');

        $product->update(['state' => 'published']);

        $response2 = $this->getJson('/api/offers');
        $response2->assertOk();
        $response2->assertJsonCount(1, 'data.0.products');
        $response2->assertJsonPath('data.0.products.0.name', 'Draft Product');
    }
}

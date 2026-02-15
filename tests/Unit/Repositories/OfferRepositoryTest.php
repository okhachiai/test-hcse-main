<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Infrastructure\Repositories\OfferRepository;
use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private OfferRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new OfferRepository;
    }

    public function test_find_or_fail_with_products_returns_offer_with_products_loaded(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create(['name' => 'Test Offer']);
        Product::factory()->for($offer)->count(3)->create();

        $result = $this->repository->findOrFailWithProducts($offer->id);

        $this->assertSame($offer->id, $result->id);
        $this->assertSame('Test Offer', $result->name);
        $this->assertTrue($result->relationLoaded('products'));
        $this->assertCount(3, $result->products);
    }

    public function test_find_or_fail_with_products_throws_when_offer_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->findOrFailWithProducts(99999);
    }

    public function test_delete_returns_true_when_offer_exists(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();

        $result = $this->repository->delete($offer->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('offers', ['id' => $offer->id]);
    }

    public function test_delete_returns_false_when_offer_does_not_exist(): void
    {
        $result = $this->repository->delete(99999);

        $this->assertFalse($result);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OfferRepository;
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
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

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

    public function test_get_filtered_filters_by_user_id(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Offer::factory()->for($user1)->create(['name' => 'User1 Offer']);
        Offer::factory()->for($user2)->create(['name' => 'User2 Offer']);

        $result = $this->repository->getFiltered(userId: $user1->id);

        $this->assertCount(1, $result);
        $this->assertSame('User1 Offer', $result->first()->name);
    }

    public function test_get_filtered_filters_by_state(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->published()->create(['name' => 'Pub']);
        Offer::factory()->for($user)->draft()->create(['name' => 'Draft']);
        Offer::factory()->for($user)->hidden()->create(['name' => 'Hidden']);

        $result = $this->repository->getFiltered(state: 'hidden', userId: $user->id);

        $this->assertCount(1, $result);
        $this->assertSame('Hidden', $result->first()->name);
    }

    public function test_get_filtered_filters_by_slug(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->create(['name' => 'Offer A', 'slug' => 'unique-slug-xyz']);
        Offer::factory()->for($user)->create(['name' => 'Offer B', 'slug' => 'other-slug']);

        $result = $this->repository->getFiltered(slug: 'unique', userId: $user->id);

        $this->assertCount(1, $result);
        $this->assertSame('unique-slug-xyz', $result->first()->slug);
    }

    public function test_get_filtered_filters_by_name(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->create(['name' => 'Alpha Beta Gamma']);
        Offer::factory()->for($user)->create(['name' => 'Other']);

        $result = $this->repository->getFiltered(name: 'Beta', userId: $user->id);

        $this->assertCount(1, $result);
        $this->assertSame('Alpha Beta Gamma', $result->first()->name);
    }

    public function test_get_filtered_combines_multiple_filters(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->draft()->create(['name' => 'Match Name', 'slug' => 'draft-match-slug']);
        Offer::factory()->for($user)->draft()->create(['name' => 'Match Name', 'slug' => 'draft-other-slug']);
        Offer::factory()->for($user)->published()->create(['name' => 'Match Name', 'slug' => 'pub-match-slug']);

        $result = $this->repository->getFiltered(
            state: 'draft',
            name: 'Match',
            slug: 'match-slug',
            userId: $user->id
        );

        $this->assertCount(1, $result);
        $this->assertSame('draft-match-slug', $result->first()->slug);
    }

    public function test_get_filtered_ignores_empty_filters(): void
    {
        $user = User::factory()->create();
        Offer::factory()->for($user)->count(2)->create();

        $result = $this->repository->getFiltered(
            state: null,
            name: '',
            slug: null,
            userId: $user->id
        );

        $this->assertCount(2, $result);
    }

    public function test_get_filtered_orders_latest_first(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->create(['name' => 'Old', 'created_at' => now()->subDays(2)]);
        Offer::factory()->for($user)->create(['name' => 'New', 'created_at' => now()]);

        $result = $this->repository->getFiltered(userId: $user->id);

        $this->assertSame('New', $result->first()->name);
        $this->assertSame('Old', $result->last()->name);
    }
}

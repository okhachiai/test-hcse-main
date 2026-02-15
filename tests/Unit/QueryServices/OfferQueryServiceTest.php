<?php

declare(strict_types=1);

namespace Tests\Unit\QueryServices;

use App\Infrastructure\QueryServices\OfferQueryService;
use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferQueryServiceTest extends TestCase
{
    use RefreshDatabase;

    private OfferQueryService $queryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queryService = new OfferQueryService;
    }

    public function test_list_published_returns_only_published_offers(): void
    {
        Offer::factory()->published()->create(['name' => 'Published']);
        Offer::factory()->draft()->create(['name' => 'Draft']);
        Offer::factory()->hidden()->create(['name' => 'Hidden']);

        $result = $this->queryService->listPublished(page: 1, perPage: 10);

        $this->assertCount(1, $result);
        $this->assertSame('Published', $result->first()->name);
    }

    public function test_list_published_includes_only_published_products(): void
    {
        $offer = Offer::factory()->published()->create();
        Product::factory()->published()->create(['offer_id' => $offer->id, 'name' => 'Pub Product']);
        Product::factory()->draft()->create(['offer_id' => $offer->id, 'name' => 'Draft Product']);

        $result = $this->queryService->listPublished(page: 1, perPage: 10);

        $this->assertCount(1, $result->first()->products);
        $this->assertSame('Pub Product', $result->first()->products->first()->name);
    }

    public function test_list_for_dashboard_filters_by_user_id(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Offer::factory()->for($user1)->create(['name' => 'User1 Offer']);
        Offer::factory()->for($user2)->create(['name' => 'User2 Offer']);

        $result = $this->queryService->listForDashboard($user1->id);

        $this->assertCount(1, $result);
        $this->assertSame('User1 Offer', $result->first()->name);
    }

    public function test_list_for_dashboard_filters_by_state(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->published()->create(['name' => 'Pub']);
        Offer::factory()->for($user)->draft()->create(['name' => 'Draft']);
        Offer::factory()->for($user)->hidden()->create(['name' => 'Hidden']);

        $result = $this->queryService->listForDashboard($user->id, state: 'hidden');

        $this->assertCount(1, $result);
        $this->assertSame('Hidden', $result->first()->name);
    }

    public function test_list_for_dashboard_filters_by_name(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->create(['name' => 'Alpha Beta Gamma']);
        Offer::factory()->for($user)->create(['name' => 'Other']);

        $result = $this->queryService->listForDashboard($user->id, name: 'Beta');

        $this->assertCount(1, $result);
        $this->assertSame('Alpha Beta Gamma', $result->first()->name);
    }

    public function test_list_for_dashboard_filters_by_slug(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->create(['name' => 'A', 'slug' => 'unique-slug-xyz']);
        Offer::factory()->for($user)->create(['name' => 'B', 'slug' => 'other-slug']);

        $result = $this->queryService->listForDashboard($user->id, slug: 'unique');

        $this->assertCount(1, $result);
        $this->assertSame('unique-slug-xyz', $result->first()->slug);
    }

    public function test_list_for_dashboard_combines_filters(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->draft()->create(['name' => 'Match', 'slug' => 'draft-match']);
        Offer::factory()->for($user)->draft()->create(['name' => 'Match', 'slug' => 'draft-other']);
        Offer::factory()->for($user)->published()->create(['name' => 'Match', 'slug' => 'pub-match']);

        $result = $this->queryService->listForDashboard(
            $user->id,
            state: 'draft',
            name: 'Match',
            slug: 'match'
        );

        $this->assertCount(1, $result);
        $this->assertSame('draft-match', $result->first()->slug);
    }

    public function test_list_for_dashboard_orders_latest_first(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->create(['name' => 'Old', 'created_at' => now()->subDays(2)]);
        Offer::factory()->for($user)->create(['name' => 'New', 'created_at' => now()]);

        $result = $this->queryService->listForDashboard($user->id);

        $this->assertSame('New', $result->first()->name);
        $this->assertSame('Old', $result->last()->name);
    }
}

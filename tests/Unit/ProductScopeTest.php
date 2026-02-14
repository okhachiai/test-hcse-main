<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Enums\ProductState;
use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_scope_returns_only_published_products(): void
    {
        $offer = Offer::factory()->for(User::factory()->create())->create();

        Product::factory()->for($offer)->published()->create(['name' => 'Pub 1']);
        Product::factory()->for($offer)->draft()->create(['name' => 'Draft 1']);
        Product::factory()->for($offer)->invisible()->create(['name' => 'Invisible 1']);
        Product::factory()->for($offer)->published()->create(['name' => 'Pub 2']);

        $published = Product::published()->get();

        $this->assertCount(2, $published);
        $this->assertTrue($published->every(fn (Product $p) => $p->state === ProductState::Published));
        $this->assertSame(['Pub 1', 'Pub 2'], $published->pluck('name')->sort()->values()->all());
    }

    public function test_draft_scope_returns_only_draft_products(): void
    {
        $offer = Offer::factory()->for(User::factory()->create())->create();

        Product::factory()->for($offer)->draft()->create(['name' => 'Draft 1']);
        Product::factory()->for($offer)->published()->create(['name' => 'Pub 1']);
        Product::factory()->for($offer)->draft()->create(['name' => 'Draft 2']);

        $drafts = Product::draft()->get();

        $this->assertCount(2, $drafts);
        $this->assertTrue($drafts->every(fn (Product $p) => $p->state === ProductState::Draft));
    }
}

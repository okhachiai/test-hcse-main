<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Enums\OfferState;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_scope_returns_only_published_offers(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->published()->create(['name' => 'Pub 1']);
        Offer::factory()->for($user)->draft()->create(['name' => 'Draft 1']);
        Offer::factory()->for($user)->hidden()->create(['name' => 'Hidden 1']);
        Offer::factory()->for($user)->published()->create(['name' => 'Pub 2']);

        $published = Offer::published()->get();

        $this->assertCount(2, $published);
        $this->assertTrue($published->every(fn (Offer $o) => $o->state === OfferState::Published));
        $this->assertSame(['Pub 1', 'Pub 2'], $published->pluck('name')->sort()->values()->all());
    }

    public function test_draft_scope_returns_only_draft_offers(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->draft()->create(['name' => 'Draft 1']);
        Offer::factory()->for($user)->published()->create(['name' => 'Pub 1']);
        Offer::factory()->for($user)->draft()->create(['name' => 'Draft 2']);

        $drafts = Offer::draft()->get();

        $this->assertCount(2, $drafts);
        $this->assertTrue($drafts->every(fn (Offer $o) => $o->state === OfferState::Draft));
    }
}

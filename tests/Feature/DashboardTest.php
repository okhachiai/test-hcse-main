<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Pagination;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_paginated_offers(): void
    {
        $user = User::factory()->create();
        $perPage = Pagination::DefaultPerPage->value;

        Offer::factory()->for($user)->count($perPage + 3)->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('offers');
        $offers = $response->viewData('offers');
        $this->assertSame($perPage, $offers->count());
        $this->assertTrue($offers->hasPages());
        $this->assertSame($perPage + 3, $offers->total());
    }

    public function test_dashboard_pagination_preserves_filters_via_query_string(): void
    {
        $user = User::factory()->create();
        $perPage = Pagination::DefaultPerPage->value;

        foreach (range(1, $perPage + 2) as $i) {
            Offer::factory()->for($user)->draft()->create(['name' => "Matching Foo {$i}"]);
        }

        $response = $this->actingAs($user)->get(route('dashboard', [
            'state' => 'draft',
            'name' => 'Matching',
            'page' => 1,
        ]));

        $response->assertOk();
        $offers = $response->viewData('offers');
        $this->assertTrue($offers->hasPages());
        $nextUrl = $offers->nextPageUrl();
        $this->assertNotNull($nextUrl);
        $this->assertStringContainsString('state=draft', $nextUrl);
        $this->assertStringContainsString('name=Matching', $nextUrl);
    }

    public function test_dashboard_filters_by_state(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->published()->create(['name' => 'Published Offer']);
        Offer::factory()->for($user)->draft()->create(['name' => 'Draft Offer']);
        Offer::factory()->for($user)->hidden()->create(['name' => 'Hidden Offer']);

        $response = $this->actingAs($user)->get(route('dashboard', ['state' => 'draft']));

        $response->assertOk();
        $offers = $response->viewData('offers');
        $this->assertCount(1, $offers);
        $this->assertSame('Draft Offer', $offers->first()->name);
    }

    public function test_dashboard_filters_by_name(): void
    {
        $user = User::factory()->create();

        Offer::factory()->for($user)->create(['name' => 'Unique Alpha Name']);
        Offer::factory()->for($user)->create(['name' => 'Other Beta']);

        $response = $this->actingAs($user)->get(route('dashboard', ['name' => 'Alpha']));

        $response->assertOk();
        $offers = $response->viewData('offers');
        $this->assertCount(1, $offers);
        $this->assertSame('Unique Alpha Name', $offers->first()->name);
    }

    public function test_dashboard_orders_latest_first(): void
    {
        $user = User::factory()->create();

        $old = Offer::factory()->for($user)->create(['name' => 'Oldest', 'created_at' => now()->subDays(2)]);
        $mid = Offer::factory()->for($user)->create(['name' => 'Middle', 'created_at' => now()->subDay()]);
        $new = Offer::factory()->for($user)->create(['name' => 'Newest', 'created_at' => now()]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $offers = $response->viewData('offers');
        $names = $offers->pluck('name')->all();
        $this->assertSame(['Newest', 'Middle', 'Oldest'], $names);
    }
}

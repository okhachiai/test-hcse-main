<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_returns_200_when_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('offers.create'));

        $response->assertStatus(200);
    }

    public function test_show_returns_200_for_own_offer(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('offers.show', $offer));

        $response->assertStatus(200);
    }

    public function test_edit_returns_200_for_own_offer(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('offers.edit', $offer));

        $response->assertStatus(200);
    }

    public function test_show_returns_404_for_non_existent_offer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('offers.show', ['offer' => 99999]));

        $response->assertStatus(404);
    }

    public function test_edit_returns_404_for_non_existent_offer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('offers.edit', ['offer' => 99999]));

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_gets_401_or_redirect_when_accessing_protected_route(): void
    {
        $response = $this->getJson(route('offers.create'));

        $response->assertStatus(401);
    }

    public function test_show_returns_403_when_accessing_another_users_offer(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->get(route('offers.show', $offer));

        $response->assertStatus(403);
    }

    public function test_edit_returns_403_when_accessing_another_users_offer(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->get(route('offers.edit', $offer));

        $response->assertStatus(403);
    }
}

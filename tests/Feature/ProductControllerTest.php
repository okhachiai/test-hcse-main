<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_200_for_own_offer(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('offers.products.index', $offer));

        $response->assertStatus(200);
    }

    public function test_create_returns_200_for_own_offer(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('offers.products.create', $offer));

        $response->assertStatus(200);
    }

    public function test_edit_returns_200_for_own_product(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();
        $product = Product::factory()->for($offer)->create();

        $response = $this->actingAs($user)->get(route('offers.products.edit', [$offer, $product]));

        $response->assertStatus(200);
    }

    public function test_index_returns_404_for_non_existent_offer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('offers.products.index', ['offer' => 99999]));

        $response->assertStatus(404);
    }

    public function test_edit_returns_404_for_non_existent_product(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('offers.products.edit', [
            'offer' => $offer,
            'product' => 99999,
        ]));

        $response->assertStatus(404);
    }

    public function test_edit_returns_404_when_product_belongs_to_another_offer(): void
    {
        $user = User::factory()->create();
        $offer1 = Offer::factory()->for($user)->create();
        $offer2 = Offer::factory()->for($user)->create();
        $product = Product::factory()->for($offer1)->create();

        $response = $this->actingAs($user)->get(route('offers.products.edit', [
            'offer' => $offer2,
            'product' => $product,
        ]));

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_gets_401_when_accessing_products_index(): void
    {
        $offer = Offer::factory()->create();

        $response = $this->getJson(route('offers.products.index', $offer));

        $response->assertStatus(401);
    }

    public function test_index_returns_403_when_accessing_another_users_offer(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->get(route('offers.products.index', $offer));

        $response->assertStatus(403);
    }

    public function test_edit_returns_403_when_accessing_another_users_product(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->for($owner)->create();
        $product = Product::factory()->for($offer)->create();

        $response = $this->actingAs($otherUser)->get(route('offers.products.edit', [$offer, $product]));

        $response->assertStatus(403);
    }

    public function test_destroy_returns_403_when_accessing_another_users_product(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->for($owner)->create();
        $product = Product::factory()->for($offer)->create();

        $response = $this->actingAs($otherUser)->delete(route('offers.products.destroy', [$offer, $product]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}

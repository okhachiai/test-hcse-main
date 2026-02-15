<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_store_product_with_invalid_payload_returns_validation_errors(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('offers.products.store', $offer), [
            'name' => '',
            'sku' => '',
            'image' => '',
            'price' => -10,
            'state' => 'invalid-state',
        ]);

        $response->assertSessionHasErrors(['name', 'sku', 'image', 'price', 'state']);
    }

    public function test_store_product_with_invalid_sku_format_returns_validation_error(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();
        $file = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($user)->post(route('offers.products.store', $offer), [
            'name' => 'Product',
            'sku' => 'invalid sku with spaces',
            'image' => $file,
            'price' => 10,
            'state' => 'published',
        ]);

        $response->assertSessionHasErrors(['sku']);
    }

    public function test_store_product_with_duplicate_sku_returns_validation_error(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();
        Product::factory()->for($offer)->create(['sku' => 'EXISTING-SKU']);

        $file = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($user)->post(route('offers.products.store', $offer), [
            'name' => 'New Product',
            'sku' => 'EXISTING-SKU',
            'image' => $file,
            'price' => 99.99,
            'state' => 'published',
        ]);

        $response->assertSessionHasErrors(['sku']);
    }

    public function test_store_product_with_valid_payload_succeeds(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();
        $file = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($user)->post(route('offers.products.store', $offer), [
            'name' => 'My New Product',
            'sku' => 'SKU-'.strtoupper(uniqid()),
            'image' => $file,
            'price' => 49.99,
            'state' => 'published',
        ]);

        $response->assertRedirect(route('offers.products.index', $offer));
        $response->assertSessionHas('status', 'Produit créé avec succès.');
        $this->assertDatabaseHas('products', [
            'offer_id' => $offer->id,
            'name' => 'My New Product',
            'price' => 49.99,
            'state' => 'published',
        ]);
    }

    public function test_update_product_with_valid_payload_succeeds(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create();
        $product = Product::factory()->for($offer)->create([
            'name' => 'Original Product',
            'sku' => 'ORIGINAL-SKU',
            'price' => 29.99,
        ]);

        $response = $this->actingAs($user)->patch(route('offers.products.update', [$offer, $product]), [
            'name' => 'Updated Product',
            'sku' => 'ORIGINAL-SKU',
            'price' => 39.99,
            'state' => 'published',
        ]);

        $response->assertRedirect(route('offers.products.index', $offer));
        $response->assertSessionHas('status', 'Produit mis à jour avec succès.');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 39.99,
            'state' => 'published',
        ]);
    }
}

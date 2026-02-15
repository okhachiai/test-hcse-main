<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OfferValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_store_offer_with_invalid_payload_returns_validation_errors(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('offers.store'), [
            'name' => '',
            'slug' => '',
            'image' => '',
            'state' => 'invalid-state',
        ]);

        $response->assertSessionHasErrors(['name', 'slug', 'image', 'state']);
    }

    public function test_store_offer_with_invalid_payload_returns_422_with_error_structure_for_json(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('offers.store'), [
            'name' => '',
            'state' => 'invalid-state',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'slug',
                'image',
                'state',
            ],
        ]);
    }

    public function test_store_offer_with_duplicate_slug_returns_validation_error(): void
    {
        $user = User::factory()->create();
        Offer::factory()->for($user)->create(['slug' => 'existing-slug']);

        $file = UploadedFile::fake()->image('offer.jpg');

        $response = $this->actingAs($user)->post(route('offers.store'), [
            'name' => 'New Offer',
            'slug' => 'existing-slug',
            'image' => $file,
            'state' => 'draft',
        ]);

        $response->assertSessionHasErrors(['slug']);
    }

    public function test_store_offer_with_valid_payload_succeeds(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('offer.jpg');

        $response = $this->actingAs($user)->post(route('offers.store'), [
            'name' => 'My New Offer',
            'slug' => 'my-new-offer-'.uniqid(),
            'image' => $file,
            'description' => 'A great offer',
            'state' => 'draft',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('offers', [
            'name' => 'My New Offer',
            'state' => 'draft',
        ]);
    }

    public function test_update_offer_with_valid_payload_succeeds(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->create([
            'name' => 'Original Name',
            'slug' => 'original-slug',
        ]);

        $response = $this->actingAs($user)->patch(route('offers.update', $offer), [
            'name' => 'Updated Name',
            'slug' => 'original-slug',
            'description' => 'Updated description',
            'state' => 'published',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'name' => 'Updated Name',
            'state' => 'published',
        ]);
    }
}

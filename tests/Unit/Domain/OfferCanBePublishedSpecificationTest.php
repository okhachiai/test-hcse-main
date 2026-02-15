<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Specifications\OfferCanBePublishedSpecification;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferCanBePublishedSpecificationTest extends TestCase
{
    use RefreshDatabase;

    private OfferCanBePublishedSpecification $spec;

    protected function setUp(): void
    {
        parent::setUp();
        $this->spec = new OfferCanBePublishedSpecification;
    }

    public function test_draft_offer_can_be_published(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->draft()->create();

        $this->assertTrue($this->spec->isSatisfiedBy($offer));
    }

    public function test_hidden_offer_can_be_published(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->hidden()->create();

        $this->assertTrue($this->spec->isSatisfiedBy($offer));
    }

    public function test_published_offer_is_satisfied(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->for($user)->published()->create();

        $this->assertTrue($this->spec->isSatisfiedBy($offer));
    }
}

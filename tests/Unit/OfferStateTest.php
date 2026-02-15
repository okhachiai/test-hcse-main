<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Enums\OfferState;
use PHPUnit\Framework\TestCase;

class OfferStateTest extends TestCase
{
    public function test_draft_can_transition_to_published_and_hidden(): void
    {
        $transitions = OfferState::Draft->allowedTransitions();
        $this->assertContains(OfferState::Published, $transitions);
        $this->assertContains(OfferState::Hidden, $transitions);
        $this->assertCount(2, $transitions);
    }

    public function test_draft_cannot_transition_to_draft(): void
    {
        $this->assertFalse(OfferState::Draft->canTransitionTo(OfferState::Draft));
    }

    public function test_published_can_transition_to_draft_and_hidden(): void
    {
        $transitions = OfferState::Published->allowedTransitions();
        $this->assertContains(OfferState::Draft, $transitions);
        $this->assertContains(OfferState::Hidden, $transitions);
        $this->assertCount(2, $transitions);
    }

    public function test_hidden_can_transition_to_draft_and_published(): void
    {
        $transitions = OfferState::Hidden->allowedTransitions();
        $this->assertContains(OfferState::Draft, $transitions);
        $this->assertContains(OfferState::Published, $transitions);
        $this->assertCount(2, $transitions);
    }
}

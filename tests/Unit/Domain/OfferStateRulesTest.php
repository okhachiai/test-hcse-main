<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Enums\OfferState;
use App\Domain\StateRules\OfferStateRules;
use PHPUnit\Framework\TestCase;

class OfferStateRulesTest extends TestCase
{
    public function test_can_transition_draft_to_published(): void
    {
        $this->assertTrue(OfferStateRules::canTransition(OfferState::Draft, OfferState::Published));
    }

    public function test_can_transition_draft_to_hidden(): void
    {
        $this->assertTrue(OfferStateRules::canTransition(OfferState::Draft, OfferState::Hidden));
    }

    public function test_cannot_transition_draft_to_draft(): void
    {
        $this->assertFalse(OfferStateRules::canTransition(OfferState::Draft, OfferState::Draft));
    }

    public function test_can_transition_published_to_draft(): void
    {
        $this->assertTrue(OfferStateRules::canTransition(OfferState::Published, OfferState::Draft));
    }

    public function test_can_transition_published_to_hidden(): void
    {
        $this->assertTrue(OfferStateRules::canTransition(OfferState::Published, OfferState::Hidden));
    }

    public function test_can_transition_hidden_to_published(): void
    {
        $this->assertTrue(OfferStateRules::canTransition(OfferState::Hidden, OfferState::Published));
    }

    public function test_default_scope_for_api_returns_published(): void
    {
        $this->assertSame(OfferState::Published, OfferStateRules::defaultScopeForApi());
    }

    public function test_visible_for_backoffice_includes_all_states(): void
    {
        $visible = OfferStateRules::visibleForBackoffice();

        $this->assertCount(3, $visible);
        $this->assertContains(OfferState::Draft, $visible);
        $this->assertContains(OfferState::Published, $visible);
        $this->assertContains(OfferState::Hidden, $visible);
    }
}

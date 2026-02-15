<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Enums\ProductState;
use App\Domain\StateRules\ProductStateRules;
use PHPUnit\Framework\TestCase;

class ProductStateRulesTest extends TestCase
{
    public function test_can_transition_draft_to_published(): void
    {
        $this->assertTrue(ProductStateRules::canTransition(ProductState::Draft, ProductState::Published));
    }

    public function test_can_transition_draft_to_invisible(): void
    {
        $this->assertTrue(ProductStateRules::canTransition(ProductState::Draft, ProductState::Invisible));
    }

    public function test_cannot_transition_draft_to_draft(): void
    {
        $this->assertFalse(ProductStateRules::canTransition(ProductState::Draft, ProductState::Draft));
    }

    public function test_can_transition_published_to_draft(): void
    {
        $this->assertTrue(ProductStateRules::canTransition(ProductState::Published, ProductState::Draft));
    }

    public function test_default_scope_for_api_returns_published(): void
    {
        $this->assertSame(ProductState::Published, ProductStateRules::defaultScopeForApi());
    }

    public function test_visible_for_backoffice_includes_all_states(): void
    {
        $visible = ProductStateRules::visibleForBackoffice();

        $this->assertCount(3, $visible);
        $this->assertContains(ProductState::Draft, $visible);
        $this->assertContains(ProductState::Published, $visible);
        $this->assertContains(ProductState::Invisible, $visible);
    }
}

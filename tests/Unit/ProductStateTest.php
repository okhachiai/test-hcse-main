<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Enums\ProductState;
use PHPUnit\Framework\TestCase;

class ProductStateTest extends TestCase
{
    public function test_draft_can_transition_to_published_and_invisible(): void
    {
        $transitions = ProductState::Draft->allowedTransitions();
        $this->assertContains(ProductState::Published, $transitions);
        $this->assertContains(ProductState::Invisible, $transitions);
        $this->assertCount(2, $transitions);
    }

    public function test_draft_cannot_transition_to_draft(): void
    {
        $this->assertFalse(ProductState::Draft->canTransitionTo(ProductState::Draft));
    }

    public function test_published_can_transition_to_draft_and_invisible(): void
    {
        $transitions = ProductState::Published->allowedTransitions();
        $this->assertContains(ProductState::Draft, $transitions);
        $this->assertContains(ProductState::Invisible, $transitions);
        $this->assertCount(2, $transitions);
    }

    public function test_invisible_can_transition_to_draft_and_published(): void
    {
        $transitions = ProductState::Invisible->allowedTransitions();
        $this->assertContains(ProductState::Draft, $transitions);
        $this->assertContains(ProductState::Published, $transitions);
        $this->assertCount(2, $transitions);
    }
}

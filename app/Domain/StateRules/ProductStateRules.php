<?php

declare(strict_types=1);

namespace App\Domain\StateRules;

use App\Domain\Enums\ProductState;

/**
 * Centralized rules for product states: transitions, API scope, backoffice visibility.
 */
final class ProductStateRules
{
    /**
     * Transition matrix: from.value => [to, ...].
     *
     * @return array<string, array<ProductState>>
     */
    public static function transitions(): array
    {
        return [
            ProductState::Draft->value => [ProductState::Published, ProductState::Invisible],
            ProductState::Published->value => [ProductState::Draft, ProductState::Invisible],
            ProductState::Invisible->value => [ProductState::Draft, ProductState::Published],
        ];
    }

    public static function canTransition(ProductState $from, ProductState $to): bool
    {
        $allowed = self::transitions()[$from->value] ?? [];

        return in_array($to, $allowed, true);
    }

    /**
     * Default state filter for public API (only published products).
     */
    public static function defaultScopeForApi(): ProductState
    {
        return ProductState::Published;
    }

    /**
     * States that can be displayed/filtered in the backoffice.
     *
     * @return array<ProductState>
     */
    public static function visibleForBackoffice(): array
    {
        return ProductState::cases();
    }
}

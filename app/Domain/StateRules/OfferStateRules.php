<?php

declare(strict_types=1);

namespace App\Domain\StateRules;

use App\Domain\Enums\OfferState;

/**
 * Centralized rules for offer states: transitions, API scope, backoffice visibility.
 */
final class OfferStateRules
{
    /**
     * Transition matrix: from.value => [to, ...].
     *
     * @return array<string, array<OfferState>>
     */
    public static function transitions(): array
    {
        return [
            OfferState::Draft->value => [OfferState::Published, OfferState::Hidden],
            OfferState::Published->value => [OfferState::Draft, OfferState::Hidden],
            OfferState::Hidden->value => [OfferState::Draft, OfferState::Published],
        ];
    }

    public static function canTransition(OfferState $from, OfferState $to): bool
    {
        $allowed = self::transitions()[$from->value] ?? [];

        return in_array($to, $allowed, true);
    }

    /**
     * Default state filter for public API (only published offers).
     */
    public static function defaultScopeForApi(): OfferState
    {
        return OfferState::Published;
    }

    /**
     * States that can be displayed/filtered in the backoffice.
     *
     * @return array<OfferState>
     */
    public static function visibleForBackoffice(): array
    {
        return OfferState::cases();
    }
}

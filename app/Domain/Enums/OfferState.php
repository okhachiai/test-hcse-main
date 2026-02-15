<?php

declare(strict_types=1);

namespace App\Domain\Enums;

use App\Domain\StateRules\OfferStateRules;

enum OfferState: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Hidden = 'hidden';

    /**
     * States reachable from this state (delegates to OfferStateRules).
     *
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return OfferStateRules::transitions()[$this->value] ?? [];
    }

    public function canTransitionTo(self $to): bool
    {
        return OfferStateRules::canTransition($this, $to);
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Published => 'Publié',
            self::Hidden => 'Masqué',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::Draft->value => self::Draft->label(),
            self::Published->value => self::Published->label(),
            self::Hidden->value => self::Hidden->label(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Enums;

use App\Domain\StateRules\ProductStateRules;

enum ProductState: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Invisible = 'invisible';

    /**
     * States reachable from this state (delegates to ProductStateRules).
     *
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return ProductStateRules::transitions()[$this->value] ?? [];
    }

    public function canTransitionTo(self $to): bool
    {
        return ProductStateRules::canTransition($this, $to);
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Published => 'Publié',
            self::Invisible => 'Invisible',
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
            self::Invisible->value => self::Invisible->label(),
        ];
    }
}

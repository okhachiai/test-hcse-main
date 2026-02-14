<?php

declare(strict_types=1);

namespace App\Enums;

enum OfferState: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Hidden = 'hidden';

    /**
     * States reachable from this state.
     *
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::Published, self::Hidden],
            self::Published => [self::Draft, self::Hidden],
            self::Hidden => [self::Draft, self::Published],
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, $this->allowedTransitions(), true);
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

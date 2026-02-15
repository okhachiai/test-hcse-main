<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductState: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Invisible = 'invisible';

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

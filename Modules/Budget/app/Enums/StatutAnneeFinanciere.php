<?php

namespace Modules\Budget\app\Enums;

enum StatutAnneeFinanciere: string
{
    case ACTIF = 'ACTIF';
    case INACTIF = 'INACTIF';

    public function label(): string
    {
        return match($this) {
            self::ACTIF => 'Actif',
            self::INACTIF => 'Inactif',
        };
    }

    public static function options(): array
    {
        return [
            self::ACTIF->value => self::ACTIF->label(),
            self::INACTIF->value => self::INACTIF->label(),
        ];
    }
}
<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TravelStyle: string implements HasLabel
{
    case Solo = 'solo';
    case Family = 'family';
    case Group = 'group';

    public function getLabel(): string
    {
        return match ($this) {
            self::Solo => 'Solo',
            self::Family => 'Family',
            self::Group => 'Group',
        };
    }
}

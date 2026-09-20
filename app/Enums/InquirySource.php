<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InquirySource: string implements HasColor, HasLabel
{
    case Enquiry = 'enquiry';
    case PlanTrip = 'plan_trip';

    public function getLabel(): string
    {
        return match ($this) {
            self::Enquiry => 'Quick Enquiry',
            self::PlanTrip => 'Plan Trip Request',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Enquiry => 'gray',
            self::PlanTrip => 'primary',
        };
    }
}

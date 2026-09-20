<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $phone;

    public ?string $whatsapp_number;

    public static function group(): string
    {
        return 'general';
    }
}

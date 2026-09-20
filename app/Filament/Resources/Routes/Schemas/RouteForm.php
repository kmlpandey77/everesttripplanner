<?php

namespace App\Filament\Resources\Routes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class RouteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_path')
                    ->prefix('/')
                    ->disabled(),
                TextInput::make('type')
                    ->disabled(),
                Toggle::make('is_active'),
                TextInput::make('redirect_to')
                    ->helperText('Path or URL to redirect this route to. Leave blank for no redirect.'),
                Select::make('redirect_code')
                    ->options([
                        301 => '301 (Permanent)',
                        302 => '302 (Temporary)',
                    ])
                    ->visible(fn (Get $get): bool => filled($get('redirect_to'))),
            ]);
    }
}

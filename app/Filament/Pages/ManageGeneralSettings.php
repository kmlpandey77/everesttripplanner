<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageGeneralSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Contact phone')
                            ->tel()
                            ->helperText('Shown as a "Call" link, e.g. +977 9800000000.')
                            ->columnSpan(1),
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp number')
                            ->helperText('Digits only with country code, no spaces or +, e.g. 9779800000000.')
                            ->columnSpan(1),
                    ]),
            ]);
    }
}

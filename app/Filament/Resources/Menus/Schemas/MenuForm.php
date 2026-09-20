<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menu')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                            ->columnSpan(1),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),
                        TextInput::make('cta_label')
                            ->label('Button label')
                            ->helperText('e.g. "Plan Your Trip". Leave blank to hide the button.')
                            ->columnSpan(1),
                        TextInput::make('cta_url')
                            ->label('Button URL')
                            ->columnSpan(1),
                    ]),
                Section::make('Items')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->label('Menu items')
                            ->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('url')
                                    ->required()
                                    ->columnSpan(1),
                                Toggle::make('open_in_new_tab')
                                    ->columnSpanFull(),
                                Repeater::make('children')
                                    ->relationship()
                                    ->label('Dropdown items')
                                    ->schema([
                                        TextInput::make('label')
                                            ->required()
                                            ->columnSpan(1),
                                        TextInput::make('url')
                                            ->required()
                                            ->columnSpan(1),
                                        Toggle::make('open_in_new_tab')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->orderColumn('sort_order')
                                    ->addActionLabel('Add dropdown item')
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->orderColumn('sort_order')
                            ->addActionLabel('Add menu item')
                            ->collapsible()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),
            ])->columns(1);
    }
}

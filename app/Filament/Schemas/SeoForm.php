<?php

namespace App\Filament\Schemas;

use App\Enums\TwitterCardType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class SeoForm
{
    /**
     * @return array<int, Component>
     */
    public static function components(): array
    {
        return [
            Section::make('SEO')
                ->relationship('seo')
                ->columnSpanFull()
                ->schema([
                    Tabs::make('Seo')
                        ->tabs([
                            Tab::make('General')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('meta_title')
                                        ->maxLength(255)
                                        ->helperText('Falls back to the title if left blank.')
                                        ->columnSpanFull(),
                                    Textarea::make('meta_description')
                                        ->maxLength(255)
                                        ->rows(2)
                                        ->columnSpanFull(),
                                    TextInput::make('canonical_url')
                                        ->label('Canonical URL')
                                        ->url()
                                        ->columnSpanFull(),
                                    Toggle::make('robots_index')
                                        ->label('Indexable')
                                        ->default(true),
                                    Toggle::make('robots_follow')
                                        ->label('Follow links')
                                        ->default(true),
                                ]),
                            Tab::make('Open Graph')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('og_title')
                                        ->label('OG title')
                                        ->maxLength(255)
                                        ->helperText('Falls back to the meta title if left blank.'),
                                    TextInput::make('og_description')
                                        ->label('OG description')
                                        ->maxLength(255)
                                        ->helperText('Falls back to the meta description if left blank.'),
                                    FileUpload::make('og_image')
                                        ->label('OG image')
                                        ->image()
                                        ->disk('public')
                                        ->directory('seo')
                                        ->columnSpanFull(),
                                ]),
                            Tab::make('Twitter Card')
                                ->columns(2)
                                ->schema([
                                    Select::make('twitter_card')
                                        ->label('Card type')
                                        ->options(TwitterCardType::class)
                                        ->columnSpanFull(),
                                    TextInput::make('twitter_title')
                                        ->label('Title')
                                        ->maxLength(255)
                                        ->helperText('Falls back to the OG title if left blank.'),
                                    TextInput::make('twitter_description')
                                        ->label('Description')
                                        ->maxLength(255)
                                        ->helperText('Falls back to the OG description if left blank.'),
                                    FileUpload::make('twitter_image')
                                        ->label('Image')
                                        ->image()
                                        ->disk('public')
                                        ->directory('seo')
                                        ->columnSpanFull()
                                        ->helperText('Falls back to the OG image if left blank.'),
                                ]),
                            Tab::make('Structured Data')
                                ->schema([
                                    TextInput::make('schema_type')
                                        ->label('Schema.org type')
                                        ->placeholder('TouristTrip, Article, WebPage…')
                                        ->maxLength(255),
                                    Textarea::make('schema_json')
                                        ->label('Schema.org JSON-LD overrides')
                                        ->rows(4)
                                        ->rule('json')
                                        ->afterStateHydrated(function (Textarea $component, mixed $state): void {
                                            $component->state(is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state);
                                        })
                                        ->dehydrateStateUsing(fn (?string $state) => filled($state) ? json_decode($state, true) : null)
                                        ->helperText('Optional raw JSON merged into the auto-generated schema.'),
                                ]),
                        ]),
                ]),
        ];
    }
}

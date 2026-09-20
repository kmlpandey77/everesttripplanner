<?php

namespace App\Filament\Resources\Trips\Schemas;

use App\Enums\PageStatus;
use App\Enums\TripDifficulty;
use App\Filament\Schemas\SeoForm;
use App\Services\RouteGenerator;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Trip')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                            ->columnSpan(1),
                        Textarea::make('short_description')
                            ->label('Short description')
                            ->rows(2)
                            ->helperText('One or two sentences shown under the title on the trip page. Also used as the SEO meta description when set.')
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rule(fn () => fn (string $attribute, $value, $fail) => RouteGenerator::isReserved($value)
                                ? $fail('This slug is reserved and cannot be used.')
                                : null)
                            ->columnSpan(1),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->columnSpan(1),
                        Toggle::make('nest_route_under_category')
                            ->label('Nest URL under category')
                            ->helperText('e.g. /nepal-trekking/everest-base-camp-trek instead of /everest-base-camp-trek')
                            ->visible(fn (Get $get): bool => filled($get('category_id')))
                            ->columnSpan(1),
                        Select::make('status')
                            ->options(PageStatus::class)
                            ->default(PageStatus::Draft)
                            ->required()
                            ->live()
                            ->columnSpan(1),
                        DateTimePicker::make('published_at')
                            ->visible(fn (Get $get): bool => $get('status') === PageStatus::Scheduled->value)
                            ->columnSpan(1),
                    ]),
                Section::make('Details')
                    ->columns(4)
                    ->schema([
                        TextInput::make('duration')
                            ->placeholder('14 days'),
                        Select::make('difficulty')
                            ->options(TripDifficulty::class),
                        TextInput::make('altitude')
                            ->placeholder('5,364m'),
                        TextInput::make('price')
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('rating')
                            ->label('Rating')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(0)
                            ->maxValue(5)
                            ->helperText('Out of 5. Leave blank to hide stars — never guess a number.'),
                        TextInput::make('reviews_count')
                            ->label('Review count')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Leave blank to hide "based on N reviews" — never guess a number.'),
                        Toggle::make('is_best_seller')
                            ->label('Best seller')
                            ->helperText('Shows a "Best Seller" badge on the trip page.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Group Pricing')
                    ->description('Optional. Shown as a discount table under the price on the trip page.')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('price_tiers')
                            ->label(false)
                            ->schema([
                                TextInput::make('label')
                                    ->placeholder('2-7 pax')
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->addActionLabel('Add tier')
                            ->collapsible()
                            ->reorderable(),
                    ]),
                Section::make('Overview')
                    ->columnSpanFull()
                    ->schema([
                        RichEditor::make('overview')
                            ->label(false),
                    ]),
                Section::make('Highlights')
                    ->columnSpanFull()
                    ->schema([
                        TagsInput::make('highlights')
                            ->label(false)
                            ->placeholder('Add a highlight'),
                    ]),
                Section::make('Itinerary')
                    ->description('Also powers the short outline shown on the trip page itself. If any days are added here, a separate "detailed itinerary" page is published for this trip.')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('detailed_itinerary')
                            ->label(false)
                            ->schema([
                                TextInput::make('day')
                                    ->placeholder('Day 1')
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('title')
                                    ->required()
                                    ->columnSpan(3),
                                TextInput::make('altitude')
                                    ->placeholder('3,440m'),
                                TextInput::make('distance')
                                    ->placeholder('12 km'),
                                TextInput::make('duration')
                                    ->placeholder('6-7 hours'),
                                TextInput::make('accommodation')
                                    ->placeholder('Teahouse'),
                                TextInput::make('meals')
                                    ->placeholder('Breakfast, Lunch, Dinner')
                                    ->columnSpan(2),
                                Textarea::make('description')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(4)
                            ->itemLabel(fn (array $state): ?string => trim(($state['day'] ?? '').' — '.($state['title'] ?? ''), ' —') ?: null)
                            ->addActionLabel('Add day')
                            ->collapsible()
                            ->collapsed()
                            ->reorderable(),
                    ]),
                Section::make('Fixed Departures')
                    ->description('Optional. Specific upcoming dates this trip runs, shown as a booking table on the trip page.')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('departures')
                            ->label(false)
                            ->schema([
                                DatePicker::make('date')
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1),
                                TextInput::make('seats_available')
                                    ->label('Seats available')
                                    ->numeric()
                                    ->minValue(0)
                                    ->columnSpan(1),
                                TextInput::make('price')
                                    ->label('Price override')
                                    ->numeric()
                                    ->prefix('$')
                                    ->helperText('Leave blank to use the trip price.')
                                    ->columnSpan(1),
                                Toggle::make('is_guaranteed')
                                    ->label('Guaranteed departure')
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->itemLabel(fn (array $state): ?string => filled($state['date'] ?? null) ? Carbon::parse($state['date'])->format('d M Y') : null)
                            ->addActionLabel('Add departure date')
                            ->collapsible()
                            ->reorderable(),
                    ]),
                Section::make('Includes & Excludes')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TagsInput::make('includes'),
                        TagsInput::make('excludes'),
                    ]),
                Section::make('Checklist & Practical Info')
                    ->columnSpanFull()
                    ->schema([
                        TagsInput::make('checklist')
                            ->label('Packing checklist')
                            ->placeholder('Add an item'),
                        RichEditor::make('know_before_you_go')
                            ->label('Know before you go'),
                        TagsInput::make('do_not_do')
                            ->label('Do not do')
                            ->placeholder('Add a warning'),
                    ]),
                Section::make('FAQ')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('faq')
                            ->schema([
                                TextInput::make('question')
                                    ->required(),
                                Textarea::make('answer')
                                    ->rows(2)
                                    ->required(),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['question'] ?? null)
                            ->addActionLabel('Add question')
                            ->collapsible()
                            ->collapsed()
                            ->reorderable(),
                    ]),
                Section::make('Gallery & Map')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        CuratorPicker::make('gallery')
                            ->multiple()
                            ->columnSpanFull(),
                        TextInput::make('map_url')
                            ->label('Map embed URL')
                            ->url()
                            ->columnSpanFull(),
                    ]),
                ...SeoForm::components(),
            ]);
    }
}

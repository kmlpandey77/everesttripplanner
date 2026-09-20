<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('trip_id')
                    ->label('Trip')
                    ->relationship('trip', 'title')
                    ->searchable()
                    ->preload()
                    ->helperText('Leave blank for a general testimonial not tied to a trip.'),
                TextInput::make('reviewer_name')
                    ->label('Reviewer name')
                    ->required(),
                TextInput::make('reviewer_role')
                    ->label('Trek / date (optional)')
                    ->helperText('e.g. "Everest Base Camp Trek, 2024"'),
                Select::make('rating')
                    ->options([
                        5 => '5 stars',
                        4 => '4 stars',
                        3 => '3 stars',
                        2 => '2 stars',
                        1 => '1 star',
                    ])
                    ->required(),
                CuratorPicker::make('avatar_id')
                    ->label('Photo (optional)'),
                Textarea::make('quote')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Published')
                    ->default(true)
                    ->helperText('Unpublished reviews are hidden from the trip page.')
                    ->columnSpanFull(),
                Toggle::make('is_featured')
                    ->label('Featured')
                    ->helperText('Featured reviews appear in the "Featured reviews" block on the home page.')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}

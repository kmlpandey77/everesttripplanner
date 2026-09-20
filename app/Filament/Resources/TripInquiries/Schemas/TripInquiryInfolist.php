<?php

namespace App\Filament\Resources\TripInquiries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TripInquiryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('trip.title')
                    ->label('Trip')
                    ->placeholder('-'),
                TextEntry::make('preferred_start_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('group_size')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('budget_range')
                    ->placeholder('-'),
                TextEntry::make('message')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

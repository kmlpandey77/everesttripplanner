<?php

namespace App\Filament\Widgets;

use App\Enums\InquirySource;
use App\Models\TripInquiry;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestTripInquiries extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest Leads')
            ->query(fn (): Builder => TripInquiry::query()->latest())
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('source')
                    ->badge(),
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('trip.title')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (TripInquiry $record): string => $record->source === InquirySource::PlanTrip
                        ? route('filament.admin.resources.plan-trip-requests.view', $record)
                        : route('filament.admin.resources.trip-inquiries.view', $record)),
            ]);
    }
}

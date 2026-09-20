<?php

namespace App\Filament\Resources\TripInquiries;

use App\Filament\Resources\TripInquiries\Pages\ListTripInquiries;
use App\Filament\Resources\TripInquiries\Pages\ViewTripInquiry;
use App\Filament\Resources\TripInquiries\Schemas\TripInquiryInfolist;
use App\Filament\Resources\TripInquiries\Tables\TripInquiriesTable;
use App\Models\TripInquiry;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TripInquiryResource extends Resource
{
    protected static ?string $model = TripInquiry::class;

    protected static ?string $modelLabel = 'enquiry';

    protected static ?string $pluralModelLabel = 'enquiries';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Leads';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->enquiries();
    }

    public static function infolist(Schema $schema): Schema
    {
        return TripInquiryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripInquiriesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTripInquiries::route('/'),
            'view' => ViewTripInquiry::route('/{record}'),
        ];
    }
}

<?php

namespace App\Filament\Resources\PlanTripRequests;

use App\Filament\Resources\PlanTripRequests\Pages\ListPlanTripRequests;
use App\Filament\Resources\PlanTripRequests\Pages\ViewPlanTripRequest;
use App\Filament\Resources\PlanTripRequests\Schemas\PlanTripRequestInfolist;
use App\Filament\Resources\PlanTripRequests\Tables\PlanTripRequestsTable;
use App\Models\TripInquiry;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PlanTripRequestResource extends Resource
{
    protected static ?string $model = TripInquiry::class;

    protected static ?string $modelLabel = 'plan trip request';

    protected static ?string $pluralModelLabel = 'plan trip requests';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Leads';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->planTripRequests();
    }

    public static function infolist(Schema $schema): Schema
    {
        return PlanTripRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlanTripRequestsTable::configure($table);
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
            'index' => ListPlanTripRequests::route('/'),
            'view' => ViewPlanTripRequest::route('/{record}'),
        ];
    }
}

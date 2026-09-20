<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Exceptions\InvalidRouteException;
use App\Filament\Resources\Trips\TripResource;
use App\Services\RouteGenerator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTrip extends CreateRecord
{
    protected static string $resource = TripResource::class;

    protected function afterCreate(): void
    {
        $parent = $this->record->nest_route_under_category
            ? $this->record->category?->route
            : null;

        try {
            $generator = app(RouteGenerator::class);
            $mainRoute = $generator->sync($this->record, $this->record->slug, 'trip', $parent);

            if (filled($this->record->detailed_itinerary)) {
                $generator->sync($this->record, 'itinerary', 'trip-itinerary', $mainRoute, 'itineraryRoute');
            }
        } catch (InvalidRouteException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->send();
        }
    }
}

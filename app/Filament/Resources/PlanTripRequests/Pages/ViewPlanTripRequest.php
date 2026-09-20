<?php

namespace App\Filament\Resources\PlanTripRequests\Pages;

use App\Filament\Resources\PlanTripRequests\PlanTripRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPlanTripRequest extends ViewRecord
{
    protected static string $resource = PlanTripRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Exceptions\InvalidRouteException;
use App\Filament\Resources\Categories\CategoryResource;
use App\Services\RouteGenerator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function afterCreate(): void
    {
        try {
            app(RouteGenerator::class)->sync($this->record, $this->record->slug, 'category', $this->record->parent?->route);
        } catch (InvalidRouteException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->send();
        }
    }
}

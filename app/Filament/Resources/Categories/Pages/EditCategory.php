<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Exceptions\InvalidRouteException;
use App\Filament\Resources\Categories\CategoryResource;
use App\Services\RouteGenerator;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        try {
            app(RouteGenerator::class)->sync($this->record, $this->record->slug, 'category', $this->record->parent?->route);
        } catch (InvalidRouteException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->send();
        }
    }
}

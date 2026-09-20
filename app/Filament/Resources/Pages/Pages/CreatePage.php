<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Exceptions\InvalidRouteException;
use App\Filament\Resources\Pages\PageResource;
use App\Models\Page;
use App\Services\RouteGenerator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function afterCreate(): void
    {
        $generator = app(RouteGenerator::class);

        if ($this->record->is_home) {
            $previousHomePages = Page::where('is_home', true)->whereKeyNot($this->record->id)->get();
            Page::whereKeyNot($this->record->id)->update(['is_home' => false]);

            foreach ($previousHomePages as $page) {
                $generator->sync($page, $page->slug, 'page', $page->parent?->route);
            }
        }

        try {
            $slug = $this->record->is_home ? '' : $this->record->slug;
            $parentRoute = $this->record->is_home ? null : $this->record->parent?->route;
            $generator->sync($this->record, $slug, 'page', $parentRoute);
        } catch (InvalidRouteException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->send();
        }
    }
}

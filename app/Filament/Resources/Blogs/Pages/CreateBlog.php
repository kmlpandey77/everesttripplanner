<?php

namespace App\Filament\Resources\Blogs\Pages;

use App\Exceptions\InvalidRouteException;
use App\Filament\Resources\Blogs\BlogResource;
use App\Models\Page;
use App\Services\RouteGenerator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBlog extends CreateRecord
{
    protected static string $resource = BlogResource::class;

    protected function afterCreate(): void
    {
        try {
            $blogsHub = Page::where('slug', 'blogs')->first();

            app(RouteGenerator::class)->sync($this->record, $this->record->slug, 'blog', $blogsHub?->route);
        } catch (InvalidRouteException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->send();
        }
    }
}

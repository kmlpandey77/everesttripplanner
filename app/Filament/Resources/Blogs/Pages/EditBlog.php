<?php

namespace App\Filament\Resources\Blogs\Pages;

use App\Exceptions\InvalidRouteException;
use App\Filament\Resources\Blogs\BlogResource;
use App\Models\Page;
use App\Services\RouteGenerator;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBlog extends EditRecord
{
    protected static string $resource = BlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        try {
            $blogsHub = Page::where('slug', 'blogs')->first();

            app(RouteGenerator::class)->sync($this->record, $this->record->slug, 'blog', $blogsHub?->route);
        } catch (InvalidRouteException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->send();
        }
    }
}

<?php

namespace App\Filament\Actions;

use App\Models\Blog;
use App\Models\Trip;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

/**
 * "Share" header button for published blogs and trips: opens each network's share dialog
 * prefilled with the public URL, so the editor posts from their own logged-in account.
 * The preview card comes from the page's Open Graph tags.
 */
class ShareActions
{
    // ponytail: share dialogs, no API tokens; switch to Graph/X/LinkedIn APIs if posts must go out unattended
    public static function make(): ActionGroup
    {
        $networks = [
            'facebook' => ['Facebook', fn (string $url, string $text): string => 'https://www.facebook.com/sharer/sharer.php?'.http_build_query(['u' => $url])],
            'x' => ['X', fn (string $url, string $text): string => 'https://x.com/intent/post?'.http_build_query(['text' => $text, 'url' => $url])],
            'linkedin' => ['LinkedIn', fn (string $url, string $text): string => 'https://www.linkedin.com/sharing/share-offsite/?'.http_build_query(['url' => $url])],
        ];

        return ActionGroup::make(collect($networks)->map(
            fn (array $network, string $name): Action => Action::make("share_{$name}")
                ->label($network[0])
                ->url(fn (Blog|Trip $record): string => $network[1](url($record->route->full_path), $record->title))
                ->openUrlInNewTab()
        )->values()->all())
            ->label('Share')
            ->icon(Heroicon::Share)
            ->button()
            // ActionGroup closures don't get $record injected, so read it from the edit page
            ->visible(function (EditRecord $livewire): bool {
                $record = $livewire->getRecord();

                return $record->isPublished() && (bool) $record->route?->is_active;
            });
    }
}

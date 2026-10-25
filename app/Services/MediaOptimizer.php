<?php

namespace App\Services;

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Throwable;

class MediaOptimizer
{
    private const CONVERTIBLE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Convert the given media to WebP and rename each file after $slug
     * ("{slug}.webp", "{slug}-2.webp", ...) in the order given.
     *
     * Only media still carrying Curator's random UUID name or a non-WebP
     * format is touched, so an image shared between records is renamed once
     * and its URL stays stable on later saves.
     *
     * @param  iterable<int|string|null>  $mediaIds
     */
    public function optimize(iterable $mediaIds, string $slug): void
    {
        $slug = Str::slug($slug);
        $ids = collect($mediaIds)->filter()->map(fn ($id): int => (int) $id)->unique()->values();

        if ($slug === '' || $ids->isEmpty()) {
            return;
        }

        $media = Media::query()->findMany($ids)->keyBy('id');

        foreach ($ids as $position => $id) {
            if ($item = $media->get($id)) {
                $this->convert($item, $position === 0 ? $slug : "{$slug}-".($position + 1));
            }
        }
    }

    private function convert(Media $media, string $name): void
    {
        $ext = strtolower((string) $media->ext);

        if (! in_array($ext, self::CONVERTIBLE_EXTENSIONS, true) || ($ext === 'webp' && ! Str::isUuid($media->name))) {
            return;
        }

        $disk = Storage::disk($media->disk);

        if (! $disk->exists($media->path)) {
            return;
        }

        try {
            $webp = (string) ImageManager::gd()->read($disk->get($media->path))->toWebp(80);
        } catch (Throwable $exception) {
            Log::warning("Could not convert media #{$media->id} to WebP: {$exception->getMessage()}");

            return;
        }

        $name = $this->uniqueName($media, $name);
        $path = ltrim("{$media->directory}/{$name}.webp", '/');

        $disk->put($path, $webp, $media->visibility ?: 'public');

        if ($path !== $media->path) {
            $disk->delete($media->path);
        }

        // Saved quietly: Curator's observer would otherwise try to move the file itself.
        $media->forceFill(['name' => $name, 'path' => $path, 'ext' => 'webp', 'size' => strlen($webp)])->saveQuietly();
    }

    /**
     * Append a short random suffix when another file already holds the name.
     */
    private function uniqueName(Media $media, string $name): string
    {
        $candidate = $name;

        while ($this->isTaken($media, ltrim("{$media->directory}/{$candidate}.webp", '/'))) {
            $candidate = $name.'-'.Str::lower(Str::random(4));
        }

        return $candidate;
    }

    private function isTaken(Media $media, string $path): bool
    {
        if ($path === $media->path) {
            return false;
        }

        return Storage::disk($media->disk)->exists($path)
            || Media::query()->whereKeyNot($media->id)->where('path', $path)->exists();
    }
}

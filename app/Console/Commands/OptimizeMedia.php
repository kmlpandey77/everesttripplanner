<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\Page;
use App\Models\Trip;
use App\Services\MediaOptimizer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:optimize-media')]
#[Description('Convert images used by pages, trips and blogs to WebP, named after their slug')]
class OptimizeMedia extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(MediaOptimizer $optimizer): void
    {
        foreach ([Page::class, Trip::class, Blog::class] as $model) {
            $model::query()->each(function (Page|Trip|Blog $record) use ($optimizer): void {
                $optimizer->optimize($record->mediaIds(), (string) $record->slug);
            });

            $this->info('Optimized '.class_basename($model).' media.');
        }
    }
}

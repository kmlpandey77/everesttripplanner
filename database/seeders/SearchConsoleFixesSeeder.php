<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Page;
use App\Models\Route;
use App\Models\Trip;
use Illuminate\Database\Seeder;

/**
 * Fixes from the 2026-09-30 Search Console export: 301s for old URLs that still 404,
 * and links from the place pages Google already shows to the luxury trip and the comfort pillar post.
 * Safe to run more than once.
 *
 * php artisan db:seed --class=SearchConsoleFixesSeeder --force
 */
class SearchConsoleFixesSeeder extends Seeder
{
    use ResolvesTripLinks;

    private const PILLAR = '/blogs/everest-base-camp-in-comfort';

    public function run(): void
    {
        $this->redirects();
        $this->placePageLinks();
    }

    private function redirects(): void
    {
        $gokyo = Trip::query()->where('slug', 'gokyo-valley-trek')->firstOrFail();

        $redirects = [
            'trips/everest-base-camp-trek' => '/'.Trip::query()->where('slug', 'everest-base-camp-trek')->firstOrFail()->route->full_path,
            'trips/gokyo-valley-trek/details-itinerary' => '/'.$gokyo->itineraryRoute()->firstOrFail()->full_path,
            'page/terms-and-conditions' => '/'.Page::query()->where('slug', 'terms-and-conditions')->firstOrFail()->route->full_path,
        ];

        foreach ($redirects as $oldPath => $newPath) {
            // ponytail: never overwrite a path someone already set up in /admin
            Route::query()->firstOrCreate(['full_path' => $oldPath], [
                'slug' => str($oldPath)->afterLast('/')->toString(),
                'type' => 'redirect',
                'redirect_to' => $newPath,
                'redirect_code' => 301,
                'is_active' => true,
            ]);
        }
    }

    private function placePageLinks(): void
    {
        $links = [
            'everest-khumbu-region' => '<p>Would you like to see the Khumbu without roughing it? Our <a href="trip:luxury-everest-base-camp-trek">Luxury Everest Base Camp Trek</a> follows the classic route through Namche Bazaar and Tengboche, with luxury lodges, a private Sherpa guide and a helicopter home. Our guide to <a href="'.self::PILLAR.'">Everest Base Camp in comfort</a> explains how it all works.</p>',
            'sagarmatha-national-park' => '<p>The classic way to experience Sagarmatha National Park is on foot, walking up the Dudh Koshi valley to Everest Base Camp. If you would prefer warm lodges and a gentle pace, our <a href="trip:luxury-everest-base-camp-trek">Luxury Everest Base Camp Trek</a> arranges your park permits for you. Read our guide to <a href="'.self::PILLAR.'">trekking to Everest Base Camp in comfort</a>.</p>',
        ];

        foreach ($links as $slug => $html) {
            $page = Page::query()->where('slug', $slug)->firstOrFail();
            $content = $page->content;

            if (str_contains(json_encode($content, JSON_UNESCAPED_SLASHES), self::PILLAR)) {
                continue;
            }

            $block = ['type' => 'text', 'data' => ['content' => $this->resolveTripLinks($html)]];
            $featuredTrips = collect($content)->search(fn (array $b): bool => $b['type'] === 'featured_trips');
            array_splice($content, $featuredTrips === false ? count($content) : $featuredTrips, 0, [$block]);

            $page->update(['content' => $content]);
        }

        $blog = Blog::query()->where('slug', 'sagarmatha-where-the-earth-touches-the-sky')->firstOrFail();

        if (! str_contains($blog->content, self::PILLAR)) {
            $blog->update(['content' => $blog->content.$this->resolveTripLinks(
                '<p>If Sagarmatha is calling you, our <a href="trip:luxury-everest-base-camp-trek">Luxury Everest Base Camp Trek</a> takes you through the heart of the park with luxury lodges, a private Sherpa guide and a helicopter flight home. Our guide to <a href="'.self::PILLAR.'">Everest Base Camp in comfort</a> is a gentle place to start.</p>'
            )]);
        }
    }
}

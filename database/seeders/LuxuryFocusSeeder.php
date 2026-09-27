<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Trip;
use Illuminate\Database\Seeder;

/**
 * Repositions an existing site around the Luxury Everest Base Camp Trek:
 * home page copy + SEO, the trip's SEO/best-seller flag, and a main-menu link.
 * Looks records up by slug and is safe to run more than once.
 *
 * php artisan db:seed --class=LuxuryFocusSeeder --force
 */
class LuxuryFocusSeeder extends Seeder
{
    public function run(): void
    {
        $trip = Trip::query()->where('slug', 'luxury-everest-base-camp-trek')->firstOrFail();
        $tripPath = '/'.$trip->route->full_path;

        $trip->update([
            'short_description' => '12-day premium trek to Everest Base Camp with luxury lodges, a private Sherpa guide, five-star Kathmandu stays and a helicopter return from Gorak Shep.',
            'is_best_seller' => true,
            // ponytail: featured_trips orders by latest published_at, so this puts the trip first; a sort column is the upgrade if more trips need pinning
            'published_at' => now(),
        ]);
        $trip->seo()->updateOrCreate([], [
            'meta_title' => 'Luxury Everest Base Camp Trek – 12 Days with Helicopter Return',
            'meta_description' => 'Trek to Everest Base Camp in comfort: luxury lodges, private Sherpa guide, gourmet meals, five-star Kathmandu hotel and a helicopter flight back. 12 days.',
        ]);

        $home = Page::query()->where('is_home', true)->firstOrFail();
        $heroMediaId = collect($home->content)->firstWhere('type', 'hero')['data']['media_id'] ?? null;

        $home->update(['content' => $this->homeContent($tripPath, $heroMediaId)]);
        $home->seo()->updateOrCreate([], [
            'meta_title' => 'Luxury Everest Base Camp Trek | Everest Trip Planner',
            'meta_description' => 'Luxury Everest Base Camp Trek with the finest Khumbu lodges, private Sherpa guides, five-star Kathmandu hotels and a helicopter return. Plan your trip today.',
        ]);

        $menu = Menu::query()->where('slug', 'main')->firstOrFail();

        if (! MenuItem::query()->where('menu_id', $menu->id)->where('url', $tripPath)->exists()) {
            MenuItem::query()->where('menu_id', $menu->id)->whereNull('parent_id')->where('sort_order', '>=', 1)->increment('sort_order');
            MenuItem::create(['menu_id' => $menu->id, 'label' => 'Luxury EBC Trek', 'url' => $tripPath, 'sort_order' => 1]);
        }
    }

    /**
     * @return array<int, array{type: string, data: array<string, mixed>}>
     */
    private function homeContent(string $tripPath, ?int $heroMediaId): array
    {
        return [
            [
                'type' => 'hero',
                'data' => [
                    'heading' => 'Luxury Everest Base Camp Trek',
                    'subheading' => 'Walk the legendary trail to Everest Base Camp with luxury lodges, a private Sherpa guide, gourmet meals and a helicopter flight home.',
                    'media_id' => $heroMediaId,
                    'primary_button_label' => 'View the Luxury Trek',
                    'primary_button_url' => $tripPath,
                    'secondary_button_label' => 'Plan My Trip',
                    'secondary_button_url' => '/start-planning',
                ],
            ],
            [
                'type' => 'heading',
                'data' => ['text' => 'Everest Base Camp, Without the Hardship', 'level' => 'h2'],
            ],
            [
                'type' => 'text',
                'data' => [
                    'content' => '<p>Everest Trip Planner specialises in the <a href="'.$tripPath.'">Luxury Everest Base Camp Trek</a>: the classic route through Namche Bazaar, Tengboche and the Khumbu Glacier to base camp at 5,364m, paired with the finest lodges in the Khumbu, private guiding and a scenic helicopter return from Gorak Shep. You keep every highlight of the walk up and skip the three-day descent.</p>',
                ],
            ],
            [
                'type' => 'features',
                'data' => [
                    'heading' => 'What Makes It Luxury',
                    'items' => [
                        ['icon' => 'heroicon-o-home-modern', 'title' => 'Finest lodges', 'description' => 'Heated rooms, attached bathrooms and hot showers in the lower Khumbu; the best premium rooms higher up.'],
                        ['icon' => 'heroicon-o-user-group', 'title' => 'Private Sherpa guide', 'description' => 'A veteran, licensed guide and dedicated porters, so your pace and daypack stay light.'],
                        ['icon' => 'heroicon-o-paper-airplane', 'title' => 'Helicopter return', 'description' => 'Fly from Gorak Shep to Kathmandu after sunrise on Kala Patthar and skip three days of descent.'],
                        ['icon' => 'heroicon-o-shield-check', 'title' => 'Uncompromising safety', 'description' => 'Two acclimatization days, daily oximeter checks and supplemental oxygen carried above 4,000m.'],
                    ],
                ],
            ],
            [
                'type' => 'heading',
                'data' => ['text' => 'About Everest', 'level' => 'h2'],
            ],
            [
                'type' => 'text',
                'data' => [
                    'content' => '<p>Mount Everest, at 8,850m the highest point on Earth, sits within Nepal\'s Khumbu region. The area is home to the Sherpa people, Sagarmatha National Park, and some of the most iconic trekking routes in the world. <a href="/about-everest/everest-khumbu-region">Read more about Everest</a>.</p>',
                ],
            ],
            [
                'type' => 'featured_trips',
                'data' => ['heading' => 'Our Everest Journeys', 'count' => 5, 'category_slug' => 'everest-trek'],
            ],
            [
                'type' => 'faq',
                'data' => [
                    'heading' => 'Frequently Asked Questions',
                    'items' => [
                        ['question' => 'What makes the Luxury Everest Base Camp Trek different?', 'answer' => 'Same classic route and acclimatization schedule, but with the best lodges on the trail, private guiding, multi-course meals, five-star hotels in Kathmandu and a helicopter return instead of walking back down.'],
                        ['question' => 'Where do we stay on the trek?', 'answer' => 'Luxury lodges with heated rooms and attached bathrooms in Phakding, Namche and Tengboche. Above Dingboche no luxury lodges exist, so we reserve the best premium rooms available.'],
                        ['question' => 'How fit do I need to be?', 'answer' => 'No mountaineering experience is needed, but you should be comfortable walking 5-7 hours a day. We recommend 8-10 weeks of cardio training before departure.'],
                        ['question' => 'When is the best time to go?', 'answer' => 'Spring (March-May) and autumn (September-November) have the most stable weather, clearest views and most reliable helicopter flights.'],
                        ['question' => 'Can the trek be private or customised?', 'answer' => 'Yes. Every luxury departure can run as a private trip, and we can add extra rest days, a private helicopter to Lukla or a longer Kathmandu stay.'],
                    ],
                ],
            ],
        ];
    }
}

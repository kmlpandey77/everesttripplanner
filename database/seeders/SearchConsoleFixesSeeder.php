<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Page;
use App\Models\Route;
use App\Models\Trip;
use Illuminate\Database\Seeder;

/**
 * Fixes from the 2026-09-30 and 2026-11-03 Search Console exports: 301s for old URLs that still 404,
 * links from the place pages Google already shows to the luxury trip and the comfort pillar post,
 * meta titles/descriptions for pages with impressions but no clicks, a Sagarmatha quick-facts box + FAQ,
 * and a night-by-night section on the luxury lodges post.
 * Never overwrites SEO fields or content already edited in /admin. Safe to run more than once.
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
        $this->metaTags();
        $this->sagarmathaFacts();
        $this->lodgesPost();
    }

    private function redirects(): void
    {
        $gokyo = Trip::query()->where('slug', 'gokyo-valley-trek')->firstOrFail();

        $redirects = [
            'trips/everest-base-camp-trek' => '/'.Trip::query()->where('slug', 'everest-base-camp-trek')->firstOrFail()->route->full_path,
            'trips/gokyo-valley-trek/details-itinerary' => '/'.$gokyo->route->full_path.'#itinerary',
            'page/terms-and-conditions' => '/'.Page::query()->where('slug', 'terms-and-conditions')->firstOrFail()->route->full_path,
            'trips/everest-base-camp-luxury-trek/faqs' => '/'.Trip::query()->where('slug', 'luxury-everest-base-camp-trek')->firstOrFail()->route->full_path.'#faq',
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

    private function metaTags(): void
    {
        /** @var array<class-string, array<string, array{0: string, 1: string}>> $tags */
        $tags = [
            Trip::class => [
                'everest-base-camp-trek' => ['Everest Base Camp Trek – 14 Days via Namche & Kala Patthar', 'The classic 14-day Everest Base Camp trek with a licensed Sherpa guide: Lukla, Namche Bazaar, Tengboche, Kala Patthar sunrise and base camp at 5,364m.'],
                'everest-base-camp-trek-with-helicopter-return' => ['Everest Base Camp Trek with Helicopter Return – 11 Days', 'Walk up to Everest Base Camp and Kala Patthar, then fly back to Kathmandu by helicopter. 11 days, privately guided, with permits, lodges and flights arranged.'],
                'gokyo-valley-trek' => ['Gokyo Valley Trek – 14 Days to Gokyo Lakes & Gokyo Ri', 'Trek the quieter side of Everest: the turquoise Gokyo Lakes, the Ngozumpa Glacier and Gokyo Ri views of Everest, Lhotse, Makalu and Cho Oyu. 14 days, guided.'],
                'everest-heli-tour' => ['Everest Heli Tour – Helicopter to Kala Patthar (5,545m)', 'Fly by helicopter through Sagarmatha National Park to Kala Patthar for close views of Everest, then breakfast at Hotel Everest View. No trekking needed.'],
            ],
            Page::class => [
                'sagarmatha-national-park' => ['Sagarmatha National Park: Facts, Wildlife & Trekking', 'Sagarmatha National Park, Nepal: established 19 July 1976, UNESCO World Heritage Site since 1979, home to Mount Everest, red pandas and snow leopards.'],
                'everest-khumbu-region' => ['Everest (Khumbu) Region: Villages, Culture & Treks', 'A guide to Nepal\'s Everest (Khumbu) region: Sherpa villages, monasteries, Sagarmatha National Park and the treks that lead to Everest Base Camp.'],
                'lukla' => ['Lukla (2,860m): Airport & Gateway to the Everest Trek', 'Lukla sits at 2,860m and is where every Everest Base Camp trek begins. Altitude, flights from Kathmandu, what the town is like and where to stay.'],
                'kala-patthar' => ['Kala Patthar (5,545m): Best Viewpoint of Everest', 'Kala Patthar (5,545m) above Gorak Shep is the best place to see Everest\'s summit up close. Altitude, the sunrise hike and how to reach it on the EBC trek.'],
                'namchebazar' => ['Namche Bazaar (3,440m): Sherpa Capital of the Khumbu', 'Namche Bazaar (3,440m) is the Sherpa capital of the Everest region and the trek\'s acclimatization stop. Altitude, things to do and where to stay.'],
                'tengboche' => ['Tengboche Monastery (3,870m) on the Everest Trek', 'Tengboche (3,870m) is home to the Khumbu\'s largest monastery, with famous views of Everest and Ama Dablam. History, prayers and the trek from Namche.'],
                'geography' => ['Geography of the Everest (Khumbu) Region', 'Mountains, glaciers, rivers and climate of Nepal\'s Everest region, from the Dudh Koshi valley to the Khumbu Glacier and the summit of Everest.'],
                'about-us' => ['About Us | Everest Trip Planner', 'Everest Trip Planner is a Nepal-based operator specialising in luxury Everest Base Camp treks, helicopter tours and private guided trips in the Khumbu.'],
                'contact-us' => ['Contact Us | Everest Trip Planner', 'Talk to our Nepal-based team about your Everest Base Camp trek or helicopter tour and get a tailored itinerary and quote.'],
            ],
        ];

        foreach ($tags as $modelClass => $bySlug) {
            foreach ($bySlug as $slug => [$title, $description]) {
                $seo = $modelClass::query()->where('slug', $slug)->firstOrFail()->seo()->firstOrNew();

                // ponytail: fill blanks only, an editor's wording in /admin wins
                $seo->meta_title = $seo->meta_title ?: $title;
                $seo->meta_description = $seo->meta_description ?: $description;
                $seo->save();
            }
        }
    }

    private function sagarmathaFacts(): void
    {
        $page = Page::query()->where('slug', 'sagarmatha-national-park')->firstOrFail();
        $content = $page->content;

        if (collect($content)->contains('type', 'faq')) {
            return;
        }

        $facts = ['type' => 'text', 'data' => ['content' => '<h2>Quick facts</h2><table><tbody>'
            .'<tr><th>Established</th><td>19 July 1976</td></tr>'
            .'<tr><th>UNESCO World Heritage Site</th><td>Inscribed 1979</td></tr>'
            .'<tr><th>Area</th><td>1,148 km² (plus a 275 km² buffer zone added in 2002)</td></tr>'
            .'<tr><th>Location</th><td>Solukhumbu District, Koshi Province, north-east Nepal</td></tr>'
            .'<tr><th>Elevation</th><td>2,845m to 8,849m (the summit of Mount Everest)</td></tr>'
            .'<tr><th>Wildlife</th><td>Snow leopard, red panda, musk deer, Himalayan tahr and 118+ bird species</td></tr>'
            .'<tr><th>Entry</th><td>Park permit checked at Monjo, a day\'s walk from Lukla</td></tr>'
            .'</tbody></table>']];

        $faq = ['type' => 'faq', 'data' => ['heading' => 'Sagarmatha National Park FAQs', 'items' => [
            ['question' => 'When was Sagarmatha National Park established?', 'answer' => 'Sagarmatha National Park was established on 19 July 1976 and was listed as a UNESCO World Heritage Site in 1979.'],
            ['question' => 'What does Sagarmatha mean?', 'answer' => 'Sagarmatha is the Nepali name for Mount Everest. It is usually translated as "forehead of the sky" or "head of the sky".'],
            ['question' => 'Where is Sagarmatha National Park?', 'answer' => 'It is in the Solukhumbu District of north-east Nepal, on the border with Tibet. Most visitors enter on foot from Lukla, passing the park gate at Monjo.'],
            ['question' => 'Do I need a permit to trek in Sagarmatha National Park?', 'answer' => 'Yes. Trekkers need a Sagarmatha National Park entry permit and the local Khumbu Pasang Lhamu Rural Municipality permit. On a guided trek both are arranged for you.'],
        ]]];

        // Facts right under the H1 heading, FAQ just before the featured trips.
        array_splice($content, 1, 0, [$facts]);
        $featuredTrips = collect($content)->search(fn (array $b): bool => $b['type'] === 'featured_trips');
        array_splice($content, $featuredTrips === false ? count($content) : $featuredTrips, 0, [$faq]);

        $page->update(['content' => $content]);
    }

    private function lodgesPost(): void
    {
        $blog = Blog::query()->where('slug', 'best-luxury-lodges-everest-base-camp')->firstOrFail();
        $trip = Trip::query()->where('slug', 'luxury-everest-base-camp-trek')->firstOrFail();
        $postPath = '/blogs/'.$blog->slug;

        if (! str_contains($blog->content, 'night-by-night')) {
            $section = $this->resolveTripLinks('<h2 id="night-by-night">A luxury lodge trek, night by night</h2>'
                .'<p>Here is how the lodges fit into our 12-day <a href="trip:luxury-everest-base-camp-trek">Luxury Everest Base Camp Trek</a>. The exact lodge depends on your dates and availability, but the pattern stays the same: true luxury lodges low down, the best premium rooms higher up, and a five-star hotel either side in Kathmandu.</p>'
                .'<table><thead><tr><th>Night</th><th>Where you sleep</th><th>Type of stay</th></tr></thead><tbody>'
                .'<tr><td>1</td><td>Kathmandu</td><td>Five-star hotel</td></tr>'
                .'<tr><td>2</td><td>Phakding (2,610m)</td><td>Luxury lodge, such as Yeti Mountain Home</td></tr>'
                .'<tr><td>3–4</td><td>Namche Bazaar (3,440m)</td><td>Luxury lodge for both acclimatization nights</td></tr>'
                .'<tr><td>5</td><td>Tengboche / Deboche (3,870m)</td><td>Luxury lodge, such as Rivendell Lodge</td></tr>'
                .'<tr><td>6–7</td><td>Dingboche (4,410m)</td><td>Best premium room, attached bathroom where available</td></tr>'
                .'<tr><td>8</td><td>Lobuche (4,940m)</td><td>Best premium room</td></tr>'
                .'<tr><td>9</td><td>Gorak Shep (5,164m)</td><td>Best available room, one night only</td></tr>'
                .'<tr><td>10–11</td><td>Kathmandu</td><td>Five-star hotel, after the helicopter flight back</td></tr>'
                .'</tbody></table>'
                .'<p>Because the helicopter brings you down from Gorak Shep, you spend just one night at the simplest lodge on the trail and skip the long walk back down.</p>');

            $blog->update(['content' => str_replace('<h2>The bottom line</h2>', $section.'<h2>The bottom line</h2>', $blog->content)]);
        }

        $seo = $blog->seo()->firstOrNew();
        if (in_array($seo->meta_title, [null, '', 'Best Luxury Lodges on the Everest Base Camp Trail'], true)) {
            $seo->meta_title = 'Best Luxury Lodges on the Everest Base Camp Trek, by Village';
            $seo->save();
        }

        if (! str_contains($trip->overview, $postPath)) {
            $trip->update(['overview' => $trip->overview.'<p>Want to know exactly where you will sleep? Our guide to the <a href="'.$postPath.'">best luxury lodges on the Everest Base Camp trek</a> goes village by village, night by night.</p>']);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Enums\TripDifficulty;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Trip;
use App\Services\RouteGenerator;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Site content mirrors what's live on everesttripplanner.com: one "Trek"
 * category, its trips, an "About Everest" info hub, blog posts, and a fixed
 * set of standalone pages.
 */
class EverestSeeder extends Seeder
{
    use WithoutModelEvents;

    private RouteGenerator $routes;

    public function run(): void
    {
        $this->routes = app(RouteGenerator::class);

        $trekCategory = $this->seedCategory();
        $this->seedSitePages($trekCategory);
        $this->seedEverestPages();
        $this->seedBlogs();
        $this->seedTrips($trekCategory);
        $this->seedMenu();
    }

    private function seedCategory(): Category
    {
        $trek = Category::factory()->create([
            'name' => 'Everest Trek',
            'slug' => 'everest-trek',
            'description' => 'Guided treks through the Everest (Khumbu) region of Nepal.',
        ]);
        $this->routes->sync($trek, $trek->slug, 'category');

        return $trek;
    }

    private function seedSitePages(Category $trekCategory): void
    {
        $heroImage = $this->importMedia('mount-everest.webp', 'Mount Everest');

        $home = Page::factory()->published()->create([
            'title' => 'Home',
            'slug' => 'home',
            'is_home' => true,
            'content' => [
                [
                    'type' => 'hero',
                    'data' => [
                        'heading' => 'Plan Your Next Adventure in the Everest',
                        'subheading' => 'We help trekkers plan and book unforgettable treks in the Everest (Khumbu) region.',
                        'media_id' => $heroImage->id,
                        'primary_button_label' => 'Start Planning',
                        'primary_button_url' => url('/start-planning'),
                    ],
                ],
                [
                    'type' => 'heading',
                    'data' => ['text' => 'Everest Trip Planner', 'level' => 'h2'],
                ],
                [
                    'type' => 'text',
                    'data' => [
                        'content' => '<p>We help you plan treks across the Everest region, from the classic Base Camp trek to shorter heli tours. Every trip page includes a full day-by-day itinerary, transparent pricing, and practical details, so you know exactly what to expect before you book.</p>',
                    ],
                ],
                [
                    'type' => 'features',
                    'data' => [
                        'heading' => 'Why Trek With Us',
                        'items' => [
                            ['icon' => 'heroicon-o-user-group', 'title' => 'Local guides', 'description' => 'Every trek is led by guides based in the region you\'re visiting.'],
                            ['icon' => 'heroicon-o-shield-check', 'title' => 'Safety first', 'description' => 'Structured acclimatization days and experienced staff on every route.'],
                            ['icon' => 'heroicon-o-currency-dollar', 'title' => 'Transparent pricing', 'description' => 'Clear includes/excludes on every trip page — no surprise costs.'],
                            ['icon' => 'heroicon-o-calendar-days', 'title' => 'Flexible dates', 'description' => 'Talk to us about rescheduling if your plans change.'],
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
                        'content' => '<p>Mount Everest, at 8,850m the highest point on Earth, sits within Nepal\'s Khumbu region. The area is home to the Sherpa people, Sagarmatha National Park, and some of the most iconic trekking routes in the world. <a href="'.url('about-everest/everest-khumbu-region').'">Read more about Everest</a>.</p>',
                    ],
                ],
                [
                    'type' => 'featured_trips',
                    'data' => ['heading' => 'Most Popular Treks in Everest', 'count' => 5, 'category_slug' => 'everest-trek'],
                ],
                [
                    'type' => 'faq',
                    'data' => [
                        'heading' => 'Frequently Asked Questions',
                        'items' => [
                            ['question' => 'How do I choose a trek?', 'answer' => 'Start with duration and difficulty on the trip page — each one lists distance, altitude, and daily walking time so you can compare routes.'],
                            ['question' => 'What is included in the price?', 'answer' => 'Every trip page has a detailed Includes/Excludes list. In general, guiding and permits are included; flights and personal gear are not.'],
                            ['question' => 'Do I need prior trekking experience?', 'answer' => 'It depends on the trek. Easier trips like the Everest Heli Tour need none; longer treks like Everest Base Camp are achievable for most people with good fitness and no prior high-altitude experience.'],
                            ['question' => 'Can I reschedule my trip?', 'answer' => 'Yes — contact us before departure and we will work with you on new dates.'],
                        ],
                    ],
                ],
            ],
        ]);
        $this->routes->sync($home, '', 'page');

        $aboutUs = Page::factory()->published()->create([
            'title' => 'About Us',
            'slug' => 'about-us',
            'content' => $this->simpleContent('About Us', [
                '<p>We plan and guide treks across the Everest region, handling logistics, permits, and on-the-ground support so you can focus on the trek itself.</p>',
            ]),
        ]);
        $this->routes->sync($aboutUs, $aboutUs->slug, 'page');

        $contactUs = Page::factory()->published()->create([
            'title' => 'Contact Us',
            'slug' => 'contact-us',
            'content' => $this->simpleContent('Contact Us', [
                '<p>Have a question about one of our treks? Get in touch and we\'ll help you plan your trip.</p>',
            ]),
        ]);
        $this->routes->sync($contactUs, $contactUs->slug, 'page');

        $terms = Page::factory()->published()->create([
            'title' => 'Terms and Conditions',
            'slug' => 'terms-and-conditions',
            'content' => $this->simpleContent('Terms and Conditions', [
                '<p>Booking terms, cancellation policy, and liability information for treks booked through this site.</p>',
            ]),
        ]);
        $this->routes->sync($terms, $terms->slug, 'page');
    }

    private function seedEverestPages(): void
    {
        $pages = [
            [
                'slug' => 'everest-khumbu-region',
                'title' => 'Everest (Khumbu) Region',
                'description' => 'Explore the Everest Region, known for Mount Everest and the resilient Sherpa people. Discover its stunning landscapes, rich culture, and growing tourism that boosts the local economy.',
                'paragraphs' => [
                    '<p>The Everest region, also known as Solu Khumbu, is famous for hosting the world\'s highest mountain, Mt. Everest, which stands at 8,850 metres (29,030 feet). Since 1953, when Sir Edmund Hillary and Tenzing Norgay first reached the summit, the resilient Sherpa people have supported countless expeditions to achieve their dreams of conquering this iconic peak. This region is not only one of the most visually stunning areas on Earth but also captivates visitors with its vibrant communities, ancient monasteries, and towering mountains.</p>',
                    '<p>The number of tourists visiting the Everest region has surged from 3,600 in 1979 to over 60,000 in 2019. In 2018 alone, Everest expeditions contributed more than $300 million, accounting for 8% of Nepal\'s gross tourism revenue. This influx of tourism has significantly boosted the local economy, enhancing the standard of living through improved health, education, and infrastructure facilities. The region\'s breathtaking beauty and cultural richness continue to draw visitors back time and time again to explore its wonders.</p>',
                ],
            ],
            [
                'slug' => 'geography',
                'title' => 'Geography of the Everest (Khumbu) Region',
                'description' => 'The Khumbu region\'s geography spans from 600 meters to the towering Everest at 8,850 meters. Discover the geological processes shaping this mountainous landscape and the seismic hazards it faces.',
                'paragraphs' => [
                    '<p>The Khumbu region of Nepal, with a gradient of elevation from 600 metres (2,000 ft) at its lowest point to Everest\'s 8,850m (29,030ft) above sea level, has been subjected to deformational processes for the past ~55 million years, creating regular failure planes. These planes can be due to weaker lithology or prior faulting or shearing of the bedrock. As India continues its generally northward movement, this interplay of processes shapes the mountain belt, resulting in a network of steep glacial headwalls with active erosion.</p>',
                    '<p>The seismic hazard is ever-present throughout the Himalayas and heightens the risk of mass movements and avalanches that can threaten the lives and livelihoods of residents and visitors alike, enhancing the potential for impacts to infrastructure, agriculture, and the lives of residents in the Mt. Everest watershed. Though now stable, this human adaptation to a changing environment highlights the ongoing threat to infrastructure and ecosystem stability presented by the unstable high-relief Himalaya.</p>',
                ],
            ],
            [
                'slug' => 'biodiversity',
                'title' => 'Biodiversity in the Khumbu Region',
                'description' => 'Discover the rich biodiversity of the Khumbu region, home to Sagarmatha National Park and rare species like the snow leopard and red panda. Learn about the Sherpa culture\'s role in conservation.',
                'paragraphs' => [
                    '<p>The Khumbu region includes the highest point on the Earth\'s surface, Mount Sagarmatha (Everest; 8,848m), and Sagarmatha National Park (SNP), covering almost 124,400 hectares in the Solu-Khumbu district of Nepal at elevations up to 6,000m. An exceptional area with dramatic mountains, glaciers, deep valleys, and seven peaks other than Mount Everest, the park is home to several rare species such as the snow leopard and the red panda.</p>',
                    '<p>A well-known destination for mountain tourism, with over 2,500 Sherpa people living within the park, the region combines nature and culture. It hosts over 20 villages with more than 6,000 Sherpas, who have inhabited the region for the last four centuries, continuing their traditional cultural and religious practices, including restrictions on hunting and slaughtering and a broader reverence for all living beings. These practices, combined with indigenous natural resource management, have been major contributing factors to the successful conservation of the SNP.</p>',
                ],
            ],
            [
                'slug' => 'sagarmatha-national-park',
                'title' => 'Sagarmatha National Park',
                'description' => 'Explore Sagarmatha National Park, a UNESCO World Heritage Site known for its dramatic landscapes, rich biodiversity, and Sherpa culture. Learn about its conservation efforts and management challenges.',
                'paragraphs' => [
                    '<blockquote>Established in 1976, Sagarmatha National Park is a UNESCO World Heritage Site encompassing diverse landscapes and rare wildlife. Despite conservation efforts, the park faces challenges from increasing tourism and environmental degradation.</blockquote>',
                    '<p>Sagarmatha National Park was established on 19 July 1976 under the National Parks and Wildlife Conservation Act, and is managed by the National Park and Wildlife Conservation Office under the National Park and Wildlife Protection Act 1973 and the Himalayan National Park Regulations 1978. Most of the park (69%) comprises barren land above 5,000m, with 28% grazing land and nearly 3% forested; this, combined with the resident Sherpa population, who rely on subsistence agro-pastoralism, presents a number of management challenges. UNESCO listed SNP as a World Heritage Site in 1979. The park includes pine and hemlock forests, while fir, juniper, birch, rhododendron, scrub, and alpine plant communities are common at higher altitude. The park is home to the red panda, snow leopard, musk deer, Himalayan tahr, marten, and Himalayan mouse hare (pika), along with over 118 species of bird including the Impeyan pheasant, snow cock, and blood pheasant.</p>',
                    '<p>The Government of Nepal provides a regular budget for the management and protection of the property and buffer zone, and has been providing 50% of the park\'s revenue to local communities through the buffer zone Integrated Conservation and Development Programme (ICDP). Buffer Zone Management Committees, User Committees, and User Groups work as additional tools for the sustainable management of the park and buffer zone resources.</p>',
                    '<p>Dramatic increases in the number of annual visitors has stimulated the local economy but has also brought an increase in the degradation of the region\'s fragile ecology and cultural traditions, due to a constant and increasing demand for firewood despite the mitigating impacts of a few operational micro-hydro projects. Construction of illegal trails, resort development, energy demand and supply, and the assessment of tourism carrying capacity remain important issues in the management of the property, despite recent success working with local communities and stakeholders to halt a number of development projects, including the extension of the Syangboche airport.</p>',
                ],
            ],
            [
                'slug' => 'sherpas',
                'title' => 'Sherpas: The Highland People of the Everest Region',
                'description' => 'Learn about the Sherpas, renowned for their mountaineering skills and rich cultural heritage. Discover their role in the Everest region\'s tourism and their efforts to preserve their traditions.',
                'paragraphs' => [
                    '<p>The Sherpas are the highland people of Nepal living in the Khumbu region, sharing certain aspects of their cultural identity with the Tibetan people and following the Buddhist faith. Living on the periphery of a great civilization, Sherpas have existed in the region for much of the preceding millennium, and are internationally acclaimed for their mountaineering feats. The life and culture of the Sherpa people in Solu-Khumbu are direct and profound, with those who live within the park carrying out primarily agricultural or trade-based activities.</p>',
                    '<p>During the 1960s and 70s, a rapid cultural shift occurred in some communities, and a generation lost the rudimentary skills in written Tibetan, with only a few able to read the language of their religion. Monasteries, temples, and libraries fell into disrepair, and precious collections of printing blocks began to deteriorate. With little support for traditional artistry, skilled craftsmen increasingly turned to producing tourist art for their livelihood. Today, an estimated 150,000 Sherpa people live in Nepal, primarily engaged in tourism and mountain expedition work.</p>',
                ],
            ],
            [
                'slug' => 'buddhist-culture',
                'title' => 'Buddhist Culture in Everest',
                'description' => 'Discover the Buddhist culture of the Sherpas, rooted in the Nyingmapa school of Tibetan Buddhism. Explore their religious practices, festivals, and the Bon religion\'s influence.',
                'paragraphs' => [
                    '<p>The origins of the Bon religion among the Sherpas are lost in tribal and clan legend. Khumbu, the Sherpa homeland, has northern boundaries marked by Mount Everest. The rites and rituals practised there are profound and beautiful, as their titles suggest &mdash; "The Union of All that is Precious," "The Heavenly Principle of the Land of Bliss" &mdash; and are marked by spectacular offerings, melodious chanting, and festivities.</p>',
                    '<p>The Sherpas have long followed the Nyingmapa school of Tibetan Buddhism, the "ancient school," a heterogeneous and somewhat loosely organised sect adhering to the rites revealed by a given Nyingmapa visionary. In Tibet, this tradition extends back to the introduction of Buddhism during the eighth century AD; the Sherpas, however, are a comparatively young people, with their legendary past reaching back a mere three or four centuries.</p>',
                ],
            ],
            [
                'slug' => 'lukla',
                'title' => 'Lukla: The Gateway to the Everest Region',
                'description' => 'Explore Lukla, the gateway to the Everest region, famous for its challenging airstrip and vibrant tourist facilities. Discover its role as the starting point for Everest treks.',
                'paragraphs' => [
                    '<p>Lukla is a small town in the Khumbu, a gateway to the Everest region in north-east Nepal. Situated at 2,860 metres (9,383 ft), it is famous around the world for its very short and steep airstrip at high altitude, often referred to as Tenzing-Hillary Airport. Lukla, in the local language, means "place with many goats and sheep," though few are found in the area today.</p>',
                    '<p>Lukla is a small servicing hub with a variety of shops, cafes, and lodges catering to tourists and trekkers before their onward journey into the Everest region.</p>',
                ],
            ],
            [
                'slug' => 'namchebazar',
                'title' => 'Namche Bazaar',
                'description' => 'Discover Namche Bazaar, the heart of Everest trekking. This bustling town offers lodgings, shops, and acclimatization opportunities for trekkers, along with stunning views and cultural experiences.',
                'paragraphs' => [
                    '<blockquote>Namche Bazaar, the central hub for Everest trekkers, offers diverse accommodations, shops, and acclimatization spots. Known for its vibrant market and stunning surroundings, it is a must-visit on the trek to Everest.</blockquote>',
                    '<p>Located within the Khumbu area at 3,440 metres (11,286 ft) at its low point, Namche Bazaar is often dubbed the heart of the Everest trek, populating the sides of a hill. Most Sherpa people in the tourism business are from the area, and the town is popular with trekkers especially for altitude acclimatization. It features cool, wet summers and chilly, dry winters, mainly affected by its altitude and the summer monsoon season, with average precipitation of 1,110mm and an average temperature of 6.1&deg;C. The town has a number of lodgings and stores catering to visitors, as well as internet cafes, German bakeries, small cafes, and many restaurants &mdash; including an Irish pub considered to be the remotest in the world. A popular local meal is yak steak.</p>',
                    '<p>On a hill overlooking Namche Bazaar is Syangboche Airport (3,750m/12,303ft), no longer used for passenger flights, though helicopters make occasional cargo flights. Immediately west of Namche is Kongde Ri at 6,187 metres (20,299 ft), and to the east is Thamserku at 6,623 metres (21,729 ft). On Saturday mornings, a weekly market is held in the centre of the village, and there may also be a daily Tibet market selling clothing and consumer goods. Namche has had electricity from the nearby Thame-Namche hydropower plant (600kW) since it opened in October 1995.</p>',
                ],
            ],
            [
                'slug' => 'tengboche',
                'title' => 'Tengboche: A Spiritual Haven on the Everest Trek',
                'description' => 'Explore Tengboche, known for its monastery and stunning panoramic views. This village is a spiritual and scenic highlight on the trek to Everest Base Camp.',
                'paragraphs' => [
                    '<blockquote>Tengboche, famous for its monastery, offers trekkers spiritual solace and breathtaking views of Everest and Ama Dablam. This village is a key stop on the journey to Everest Base Camp.</blockquote>',
                    '<p>About two days\' trekking from Lukla, Tengboche is a small village located on a hill at the confluence of the Dudh Kosi and Imja Khola rivers. The famous Tengboche monastery, at 3,870 metres (12,700 ft) altitude, lies along a trail that initially crosses the Dudh Kosi (3,250m/10,660ft) river; downhill lies Deboche, the nunnery. Tengboche captures the heart of all trekkers, as during winter the snow peaks of Ama Dablam, the tip of Everest glowing from the Nuptse ridge, and several other peaks form a picturesque landscape.</p>',
                    '<p>The strategically placed Tengboche monastery, on the way to Everest Base Camp, attracts large numbers of tourists from around the world. Built in 1916 by Lama Gulu with strong links to its mother monastery, the Rongbuk Monastery in Tibet, it was destroyed by an earthquake in 1934 and subsequently rebuilt, then destroyed a second time by fire in 1989 and rebuilt again with the help of volunteers and international assistance. During the spring season, the hill slopes around Tengboche are covered with flowering rhododendrons.</p>',
                ],
            ],
            [
                'slug' => 'everest-base-camp',
                'title' => 'Everest Base Camp',
                'description' => 'Discover Everest Base Camp, the ultimate goal for trekkers and climbers. Located at 5,364 meters, it serves as a resting and acclimatization site for Everest expeditions.',
                'paragraphs' => [
                    '<p>Lying on opposite sides of Mount Everest are South Base Camp in Nepal, at an altitude of 5,364 metres (17,598 ft), and North Base Camp in Tibet, China, at 5,150 metres (16,900 ft). The base camps are basic campsites at the foot of Mount Everest, generally used by mountain climbers during their ascent and descent, and are also a popular destination for hikers.</p>',
                    '<p>Everest Base Camp is a striking campsite, dotted with colourful tents during expedition periods, with supplies delivered by yak and porter. Climbers typically rest at base camp for several days for acclimatization, to reduce the risk of altitude sickness.</p>',
                ],
            ],
            [
                'slug' => 'kala-patthar',
                'title' => 'Kala Patthar',
                'description' => 'Kala Patthar, meaning \'black rock\' in Nepali, is a renowned landmark in the Nepali Himalayas, offering trekkers the most accessible close-up view of Mount Everest. Standing at 5,545 metres (18,192 feet), it provides breathtaking vistas of Everest, Nuptse, Changtse, and glimpses of Lhotse\'s northern flank.',
                'paragraphs' => [
                    '<p>Kala Patthar, meaning "black rock" in Nepali, is a famous landmark located on the south ridge of Pumori in the Nepali Himalayas, above Gorak Shep. Standing only 10m (33ft) above the surrounding terrain, the ascent of Kala Patthar (5,545m/18,192ft) is very popular with trekkers in the Everest region since it provides the most accessible close-up view of Everest itself.</p>',
                    '<p>The views of Everest, Nuptse, and Changtse are spectacular from Kala Patthar, with glimpses of the northern flank and the Lhotse summit.</p>',
                ],
            ],
            [
                'slug' => 'other-must-visit-places',
                'title' => 'Key Places in Everest',
                'description' => 'A guide to the smaller villages and waypoints along the Everest trekking trails, from Phakding to Gorak Shep.',
                'paragraphs' => [
                    '<h2>Phakding (2,610m)</h2><p>Located in the Dudh Kosi river valley, Phakding is a small village in the Khumbu region just north of Lukla and south of Monjo, within a UNESCO World Heritage Site since 1979. It\'s often the main stopping point for trekkers on their way to Mount Everest via the Gokyo Ri or Tengboche routes, and consists of a number of guesthouses.</p>',
                    '<h2>Monjo (2,835m)</h2><p>Just below the Sagarmatha National Park entrance gate and checkpoint, Monjo is a small hamlet in the Dudh Kosi river valley just north of Phakding and south of Jorsale. The trail at Monjo is often a stopping point for trekkers, as an alternative to Phakding, on their way to Everest Base Camp, and it contains numerous teahouses and lodges.</p>',
                    '<h2>Thame</h2><p>Home to many famous Sherpa mountaineers, Thame and its neighbouring Thameteng (Upper Thame) are small Sherpa villages near Namche, and the last year-round villages on the historic salt trading route between Tibet, Nepal, and India. Great climbers including Apa Sherpa (21 Everest summits) and Kami Rita Sherpa (24 summits as of May 2019) were born here, as was Tenzing Norgay, who with Sir Edmund Hillary was one of the first to climb Mount Everest.</p><p>Thame monastery is one of the oldest in the Khumbu region and is famous for the annual Mani Rimdu festival. A small hydropower plant with an installed capacity of 600kW, operated by the Khumbu Bijuli Company, opened near Thame in October 1995.</p>',
                    '<h2>Khumjung (3,790m)</h2><p>Khumjung is a village inside Sagarmatha National Park, near Mount Khumbila. Its monastery holds a purported "yeti scalp," and the village has modern communications including internet and phone services. As of 2011, it had a population of 1,912 people across 551 households. The Khumjung school, built by Sir Edmund Hillary\'s Himalayan Trust in 1961, began with two classrooms and now caters to pre-school, primary, and secondary students, with over 350 enrolled.</p>',
                    '<h2>Pangboche (3,985m)</h2><p>Pangboche lies high in the Imja Khola valley, about 3km northeast of Tengboche, and serves as a base for climbing nearby Ama Dablam as well as for trekking. North of the village is the Dughla lake and pass. Pangboche monastery is famed for its "yeti scalp and hand," the latter of which was later stolen. The Pangboche school was built by Sir Edmund Hillary\'s Himalayan Trust in 1963.</p>',
                    '<h2>Dingboche (4,410m)</h2><p>Dingboche is a small Sherpa village in the Chukhung Valley, a popular stop for trekkers and climbers headed to Mount Everest, Ama Dablam, or Imja Tse. Groups typically spend two nights here for acclimatization. The village relies heavily on tourism, with lodges and tenting areas, an internet cafe using satellite technology, and one of the world\'s highest billiard parlours. Its distinctive stone mane walls run for kilometres, built using stones of different sizes.</p>',
                    '<h2>Lobuche (4,940m)</h2><p>Lobuche is a small settlement in the Khumbu region and one of the last overnight stops with lodging on the trail to base camp. From there, trekkers can continue to Everest Base Camp or stop at Gorak Shep, the last stop with lodging, and climb the nearby peak of Kala Patthar for a rare view of the Everest summit. Lodging at Lobuche is notoriously basic, mostly stone huts with shared bunk dormitories, though recent years have seen the addition of more modern facilities.</p>',
                    '<h2>Gorak Shep</h2><p>In the 1950s, Gorak Shep was the original Everest Base Camp, used by mountain climbers attempting the summit. The name means "dead ravens," a reference to the complete lack of vegetation at the site, which lies inside Sagarmatha National Park. It\'s the final overnight stop for most trekkers, and the best launching pad for an ascent of Kala Patthar or a visit to Everest Base Camp itself, just below the Khumbu Icefall.</p>',
                ],
            ],
        ];

        $hub = Page::factory()->published()->create([
            'title' => 'About Everest',
            'slug' => 'about-everest',
            'content' => [
                ['type' => 'heading', 'data' => ['text' => 'About Everest', 'level' => 'h1']],
                [
                    'type' => 'text',
                    'data' => [
                        'content' => '<p>Everything you need to know about the Everest (Khumbu) region before you go &mdash; its geography and biodiversity, the Sherpa people and their Buddhist culture, and the villages and landmarks you\'ll pass through on the trail, from Lukla to Everest Base Camp and Kala Patthar.</p>',
                    ],
                ],
                [
                    'type' => 'html',
                    'data' => [
                        'html' => '<div class="mx-auto grid max-w-6xl gap-4 px-6 py-4 sm:grid-cols-2 lg:grid-cols-3">'
                            .collect($pages)->map(fn (array $p): string => '<a href="/about-everest/'.$p['slug'].'" class="rounded-lg border border-slate-100 bg-white p-5 transition hover:border-primary hover:shadow-sm">'
                                .'<h3 class="font-semibold text-slate-900">'.e($p['title']).'</h3>'
                                .'<p class="mt-1 text-sm text-slate-600">'.e(Str::limit($p['description'], 110)).'</p>'
                                .'</a>')->implode('')
                            .'</div>',
                    ],
                ],
            ],
        ]);
        $hubRoute = $this->routes->sync($hub, $hub->slug, 'page');

        foreach ($pages as $pageData) {
            $page = Page::factory()->published()->create([
                'title' => $pageData['title'],
                'slug' => $pageData['slug'],
                'parent_id' => $hub->id,
                'content' => $this->simpleContent($pageData['title'], $pageData['paragraphs']),
            ]);
            $this->routes->sync($page, $page->slug, 'page', $hubRoute);
        }
    }
    private function seedBlogs(): void
    {
        $posts = [
            [
                'slug' => 'lukla-the-first-step-on-the-everest-trail',
                'title' => 'Lukla: The First Step on the Everest Trail',
                'description' => 'Lukla: The starting point for Everest treks. Learn about Lukla Airport, Sherpa culture, acclimatization tips, and the first steps on the Everest Trail.',
                'image' => 'lukla-airport-runway.jpg',
                'image_alt' => 'Lukla airport',
                'published_at' => '2024-12-27',
                'paragraphs' => [
                    '<p>Lukla. The name itself conjures images of dramatic mountain landscapes, prayer flags fluttering in the wind, and the thrill of embarking on an epic adventure. For many, Lukla is more than just a small town in Nepal; it\'s the gateway to Everest, the starting point for a journey to the roof of the world. It\'s the first step on the <a href="/everest-trek/everest-base-camp-trek">Everest Trail</a>.</p><p>Perched precariously on a mountainside at an altitude of 2,860 metres (9,383 feet), Lukla is home to the Tenzing-Hillary Airport, famously known as one of the world\'s most dangerous airports. The short, sloped runway, nestled between towering mountains, makes for a thrilling and often nerve-wracking landing. For most trekkers, this exhilarating arrival is the official beginning of their Everest adventure.</p>',
                    '<h3>Touching Down in Lukla: A Surreal Experience</h3><p>The flight to Lukla is an experience in itself. From Kathmandu, small propeller planes navigate through narrow valleys, offering breathtaking views of terraced hillsides and snow-capped peaks. As the plane descends towards Lukla, the runway suddenly appears, seemingly carved into the side of a mountain. The landing is often bumpy, but the sense of relief upon touching down is palpable. You\'ve arrived.</p><p>Stepping off the plane, you\'re immediately greeted by the crisp mountain air and the vibrant atmosphere of Lukla. The town is a bustling hub of activity, with trekkers, porters, and locals mingling in the narrow streets. The air is filled with the sounds of chatter, the clanging of trekking gear, and the distant rumble of helicopters ferrying supplies.</p>',
                    '<h3>More Than Just an Airport Town</h3><p>While the airport is undoubtedly Lukla\'s defining feature, the town itself has a charm of its own. It\'s a place where you can experience the unique culture of the Sherpa people, the ethnic group that has called this region home for centuries. You\'ll find colourful prayer flags adorning buildings, mani stones carved with Buddhist mantras, and friendly faces eager to welcome you to their land.</p><p>Lukla offers a range of teahouses and lodges, providing trekkers with a place to rest, refuel, and prepare for the journey ahead. These teahouses are more than just accommodation; they are social hubs where trekkers from all over the world can connect, share stories, and exchange tips.</p>',
                    '<h3>Preparing for the Trek</h3><p>Lukla is not just a place to arrive; it\'s a place to prepare. Trekkers often spend a night in Lukla to acclimatize to the altitude before setting off on the trail. This is a crucial step in preventing altitude sickness, a potentially serious condition that can affect trekkers at high altitudes.</p><p>In Lukla, you can also finalize your trekking arrangements, hire porters if needed, and purchase any last-minute supplies. The town has a number of shops selling trekking gear, clothing, and other essentials.</p>',
                    '<h3>The First Steps on the Trail</h3><p>From Lukla, the trail winds its way through picturesque valleys, across suspension bridges spanning rushing rivers, and past traditional Sherpa villages. The first few days of the trek are relatively gentle, allowing trekkers to acclimatize further and enjoy the stunning scenery.</p><p>The trail from Lukla to Phakding is a common first day\'s trek, a relatively easy walk that provides a good introduction to the Himalayan landscape. As you walk, you\'ll catch glimpses of snow-capped peaks in the distance, building anticipation for the majestic views to come.</p>',
                    '<h3>A Lasting Impression</h3><p>Lukla is more than just a starting point; it\'s an integral part of the Everest experience. It\'s a place that embodies the spirit of adventure, a place where dreams of reaching the top of the world begin. It\'s the first step on the Everest Trail, a step that marks the beginning of an unforgettable journey.</p>',
                ],
            ],
            [
                'slug' => 'sagarmatha-where-the-earth-touches-the-sky',
                'title' => 'Sagarmatha: Where the Earth Touches the Sky',
                'description' => 'Explore Sagarmatha National Park, home to Mount Everest and breathtaking Himalayan scenery. Discover Sherpa culture, trekking routes, and the majesty of Nepal\'s mountains.',
                'image' => 'sagarmatha.jpg',
                'image_alt' => 'Sagarmatha (Mount Everest)',
                'published_at' => '2024-12-20',
                'paragraphs' => [
                    '<p>Sagarmatha. The Nepali name for Mount Everest, meaning "Forehead of the Sky." But Sagarmatha is more than just the world\'s highest peak; it\'s a national park, a <a href="/about-everest/sagarmatha-national-park">UNESCO World Heritage Site</a>, and a realm of unparalleled natural beauty and cultural significance. It\'s a place where the earth truly seems to touch the sky, a land of towering mountains, deep valleys, and resilient people.</p><p>Entering <a href="/about-everest/sagarmatha-national-park">Sagarmatha National Park</a> is like stepping into another world. The landscape is dominated by snow-capped peaks, glaciers, and rugged terrain. The air is thin and crisp, the silence broken only by the wind whistling through the valleys and the occasional rumble of an avalanche. This is a land of extremes, where life clings tenaciously to the slopes and the forces of nature are on full display.</p>',
                    '<h3>A Kingdom of Giants</h3><p>Of course, the undisputed king of Sagarmatha is Mount Everest itself. Its pyramidal peak, draped in snow and ice, dominates the skyline, a constant reminder of the power and majesty of nature. But Everest is not alone. Lhotse, Nuptse, Ama Dablam, and countless other peaks rise majestically around it, creating a panorama of breathtaking grandeur. These mountains are not just geological formations; they are sacred places, revered by the local Sherpa people and imbued with spiritual significance.</p>',
                    '<h3>The Sherpa: Guardians of the Mountains</h3><p>Sagarmatha is not just a natural wonder; it\'s also a cultural landscape. The Sherpa people, renowned for their mountaineering skills and deep connection to the mountains, have called this region home for centuries. Their unique culture, traditions, and way of life are inextricably linked to the mountains. From their colourful prayer flags fluttering in the wind to their ancient monasteries perched on hillsides, the Sherpa presence adds a rich human dimension to the park\'s landscape.</p>',
                    '<h3>Trekking Through a Wonderland</h3><p>For many, the primary reason to visit Sagarmatha is to <a href="/everest-trek">trek</a>. The park offers a variety of trekking routes, from the classic Everest Base Camp trek to shorter, less strenuous hikes. Trekking through Sagarmatha is an unforgettable experience. Each step reveals new vistas of snow-capped peaks, glacial valleys, and remote Sherpa villages. The trails are challenging, but the rewards are immense. The sense of accomplishment upon reaching <a href="/everest-trek/everest-base-camp-trek">Everest Base Camp</a>, or simply witnessing the sunrise over the Himalayas, is a moment that will stay with you forever.</p>',
                    '<h3>A Fragile Ecosystem</h3><p>Despite its rugged appearance, Sagarmatha is a fragile ecosystem. The high altitude, harsh climate, and increasing human activity have put pressure on its delicate balance. Conservation efforts are crucial to protect the park\'s unique biodiversity and preserve its natural beauty for future generations. Responsible tourism, sustainable trekking practices, and community involvement are essential to ensuring the long-term health of Sagarmatha.</p>',
                    '<h3>More Than Just a Mountain</h3><p>Sagarmatha is more than just a mountain or a national park; it\'s a symbol of human aspiration, resilience, and the enduring power of nature. It\'s a place where the earth truly seems to touch the sky, a place that inspires awe and wonder in all who visit. It\'s a reminder of the beauty and fragility of our planet and the importance of protecting its natural treasures.</p>',
                ],
            ],
            [
                'slug' => 'the-heart-of-the-khumbu-exploring-namche-bazaar',
                'title' => 'The Heart of the Khumbu: Exploring Namche Bazaar',
                'description' => 'Explore Namche Bazaar, the vibrant heart of the Khumbu region and gateway to Everest. Discover Sherpa culture, trekking routes, acclimatization tips, and stunning Himalayan views.',
                'image' => 'namche-bazaar.jpg',
                'image_alt' => 'Namche Bazaar',
                'published_at' => '2024-12-13',
                'paragraphs' => [
                    '<p>Namche Bazaar. The name itself evokes images of soaring peaks, prayer flags fluttering in the crisp mountain air, and the indomitable spirit of the Sherpa people. More than just a stop on the <a href="/everest-trek/everest-base-camp-trek">Everest Base Camp trek</a>, Namche is the vibrant heart of the Khumbu region, a bustling hub of commerce, culture, and acclimatization. It\'s a place where the modern world meets ancient traditions, where yaks share the streets with trekkers, and where the allure of Everest is ever-present.</p><p>Perched at an altitude of 3,440 metres (11,286 feet), Namche isn\'t easily reached. The two-day trek from Lukla, the gateway airport to the Everest region, is a challenging but rewarding introduction to the Himalayan landscape. The steep climb through rhododendron forests, across suspension bridges spanning roaring rivers, builds anticipation for the spectacle that awaits. As you round the final bend and Namche comes into view, nestled in a horseshoe-shaped bowl against the backdrop of snow-capped peaks, you understand why it\'s considered one of the most beautiful towns in the world.</p>',
                    '<h3>A Market of Marvels</h3><p>The heart of Namche is its bustling Saturday market. This weekly event draws traders from surrounding villages, bringing a vibrant tapestry of goods: fresh produce carried from lower valleys, Tibetan handicrafts, trekking gear, and everything in between. The market is a sensory feast &mdash; the cacophony of bartering voices, the vibrant colours of textiles, the aroma of spices and freshly baked bread. It\'s a unique opportunity to witness the local way of life and connect with the Sherpa community. Even if you\'re not there on a Saturday, the permanent market area offers a glimpse into this vibrant commercial spirit.</p>',
                    '<h3>More Than Just a Stopover</h3><p>Namche is strategically located as an acclimatization stop for trekkers heading higher into the mountains. Spending a couple of nights here is crucial for allowing your body to adjust to the thinning air, reducing the risk of altitude sickness. But Namche is more than just a place to rest; it\'s a place to explore.</p><p><strong>Sherpa Culture Museum:</strong> This small but informative museum offers insights into the rich culture and history of the Sherpa people, their unique traditions, and their deep connection to the mountains.</p><p><strong>Everest View Hotel:</strong> A short hike above Namche brings you to this hotel, which, as its name suggests, offers breathtaking panoramic views of Everest and other Himalayan giants. It\'s a perfect spot to soak in the grandeur of the landscape and capture iconic photos.</p><p><strong>Hiking to nearby villages:</strong> Day hikes to Thame, Khumjung, and Khunde offer opportunities to experience traditional Sherpa villages, visit ancient monasteries, and enjoy stunning views of Ama Dablam and other peaks. The Hillary School in Khumjung, established by Sir Edmund Hillary, is a particularly poignant reminder of the enduring connection between the Sherpa people and the outside world.</p>',
                    '<h3>A Blend of Old and New</h3><p>Namche is a fascinating blend of traditional Sherpa culture and modern amenities. While yaks still roam the streets, you\'ll also find Wi-Fi cafes, bakeries serving apple pie, and well-stocked shops selling everything from trekking boots to chocolate bars. This blend of old and new is part of Namche\'s unique charm.</p>',
                    '<h3>A Lasting Impression</h3><p>Namche Bazaar is more than just a town on a map; it\'s an experience. It\'s the feeling of crisp mountain air filling your lungs, the sight of snow-capped peaks piercing the azure sky, the warmth of Sherpa hospitality, and the sense of being part of something truly special. It\'s a place that stays with you long after you\'ve descended from the mountains, a reminder of the power and beauty of the Himalayas and the resilience of the human spirit. It\'s the heart of the Khumbu, beating strong amidst the world\'s highest peaks.</p>',
                ],
            ],
        ];

        $hub = Page::factory()->published()->create([
            'title' => 'Blogs',
            'slug' => 'blogs',
            'content' => [
                ['type' => 'heading', 'data' => ['text' => 'Blogs', 'level' => 'h1']],
                [
                    'type' => 'text',
                    'data' => [
                        'content' => '<p>Stories, guides, and dispatches from the Everest region &mdash; written by our Nepal-based team to help you plan a trek that matches what you actually want from the Khumbu.</p>',
                    ],
                ],
                ['type' => 'blog_index', 'data' => ['count' => 12]],
            ],
        ]);
        $hubRoute = $this->routes->sync($hub, $hub->slug, 'page');

        foreach ($posts as $postData) {
            $image = $this->importMedia($postData['image'], $postData['image_alt']);

            $post = Blog::factory()->published()->create([
                'title' => $postData['title'],
                'slug' => $postData['slug'],
                'excerpt' => $postData['description'],
                'image_id' => $image->id,
                'published_at' => $postData['published_at'],
                'content' => collect($postData['paragraphs'])->implode(''),
            ]);
            $this->routes->sync($post, $post->slug, 'blog', $hubRoute);
        }
    }
    private function seedMenu(): void
    {
        $menu = Menu::factory()->create([
            'name' => 'Main Menu',
            'slug' => 'main',
            'cta_label' => 'Plan Your Trip',
            'cta_url' => '/start-planning',
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Home',
            'url' => '/',
            'sort_order' => 0,
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'About',
            'url' => '/about-us',
            'sort_order' => 1,
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Everest Treks',
            'url' => '/everest-trek',
            'sort_order' => 2,
        ]);

        $aboutEverest = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'About Everest',
            'url' => '/about-everest',
            'sort_order' => 3,
        ]);

        $aboutEverestChildren = [
            ['label' => 'Everest (Khumbu) Region', 'url' => '/about-everest/everest-khumbu-region'],
            ['label' => 'Geography', 'url' => '/about-everest/geography'],
            ['label' => 'Biodiversity', 'url' => '/about-everest/biodiversity'],
            ['label' => 'Sagarmatha National Park', 'url' => '/about-everest/sagarmatha-national-park'],
            ['label' => 'Sherpas', 'url' => '/about-everest/sherpas'],
            ['label' => 'Buddhist Culture', 'url' => '/about-everest/buddhist-culture'],
            ['label' => 'Lukla', 'url' => '/about-everest/lukla'],
            ['label' => 'Namche Bazaar', 'url' => '/about-everest/namchebazar'],
            ['label' => 'Tengboche', 'url' => '/about-everest/tengboche'],
            ['label' => 'Everest Base Camp', 'url' => '/about-everest/everest-base-camp'],
            ['label' => 'Kala Patthar', 'url' => '/about-everest/kala-patthar'],
            ['label' => 'Key Places in Everest', 'url' => '/about-everest/other-must-visit-places'],
        ];

        foreach ($aboutEverestChildren as $index => $child) {
            MenuItem::factory()->create([
                'menu_id' => $menu->id,
                'parent_id' => $aboutEverest->id,
                'label' => $child['label'],
                'url' => $child['url'],
                'sort_order' => $index,
            ]);
        }

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Blogs',
            'url' => '/blogs',
            'sort_order' => 4,
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Contact us',
            'url' => '/contact-us',
            'sort_order' => 5,
        ]);
    }

    private function seedTrips(Category $trekCategory): void
    {
        $trips = [
            [
                'title' => 'Everest Base Camp Trek',
                'slug' => 'everest-base-camp-trek',
                'image' => 'everest-base-camp.jpg',
                'duration' => '14 days',
                'difficulty' => TripDifficulty::Moderate,
                'altitude' => '5,545m',
                'overview' => '<p>Everest Trip Planner presents the classic Everest Base Camp Trek revealing the beauty of the Himalayas of Nepal. The journey to the base camp provides you with an adventure like never before.</p><p>The trek to Everest Base Camp (EBC) usually takes around 14 days through the mountain landscape. Lukla, the starting point of the trek in the Everest region, is reached via a scenic flight from Kathmandu.</p><p>As the trek initiates, you come across Sagarmatha National Park while passing through dense forests, pristine rivers, green hills, waterfalls, glaciers, and many more. On top of that, the Sherpa villages like Namche, Tengboche, Dingboche, and more expose you to unique Sherpa culture and their way of life.</p><p>As the name of the trek suggests, you are sure to be captivated by the world\'s highest mountain, Mt Everest. Besides Everest, the majestic views of Ama Dablam, Lhotse, Nuptse, Lobuche, etc. are out of this world.</p><p>Moreover, visiting ancient monasteries, witnessing mountain vistas from Kala Patthar, and seeing major landmarks like the Khumbu Glacier up close are the major highlights of the trek.</p><p>As for the trekking difficulty, the EBC trek is considered moderately difficult. However, trekkers need to have good physical fitness since one needs to trek above 5,000 metres in altitude. So, it\'s best to prepare for the trek, especially for beginners.</p><p>In addition, the trekking experience can greatly vary depending on the time of year. Spring and autumn are the most preferred seasons due to the fair weather and temperature.</p><p>So come join us today! We strive to make every trekking experience a positive one and hope to make it meaningful.</p>',
                'detailed_itinerary' => [
                    ['day' => 'Day 1', 'title' => 'Arrival in Kathmandu', 'altitude' => '1,350m', 'meals' => 'Breakfast', 'description' => 'You arrive at Tribhuvan International Airport in Kathmandu, where an Everest Trip Planner staff member greets you with a flower garland and transfers you to your hotel. Depending on your arrival time, relax at the hotel or explore Kathmandu, then meet your guide to discuss the trek ahead. Overnight at a hotel in Kathmandu.'],
                    ['day' => 'Day 2', 'title' => 'Fly from Kathmandu to Lukla and trek to Phakding', 'altitude' => 'Lukla (2,840m/9,318ft)', 'duration' => '30-45 min flight, 3 hrs trek', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'In the busy spring (Mar-May) and autumn (Sep-Nov) seasons, the day starts very early with a long drive to Manthali airport followed by a short flight to Lukla. In quieter winter and monsoon months, you fly directly from Kathmandu instead — airlines choose the routing based on passenger numbers. From Lukla, you trek roughly 3 hours to Phakding.'],
                    ['day' => 'Day 3', 'title' => 'Trek to Namche Bazaar', 'altitude' => '3,440m/11,283ft', 'duration' => '7 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The trail today is steep but scenic. You pass through Monjo, entering the UNESCO-listed Sagarmatha National Park, then continue through villages like Jorsale to reach Namche Bazaar, the bustling gateway to Everest with shops, cafes, lodges, banks, and ATMs.'],
                    ['day' => 'Day 4', 'title' => 'Acclimatization day in Namche', 'altitude' => '3,440m/11,283ft', 'duration' => '3-4 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'An acclimatization day in Namche Bazaar. Trekkers can take short hikes to the Hillary School, a traditional Sherpa village, or Syangboche Airport. Most also visit the Everest View Hotel, the world\'s highest-placed hotel, for panoramic mountain views including Everest itself, before returning to Namche for the night.'],
                    ['day' => 'Day 5', 'title' => 'Trek to Tengboche', 'altitude' => '3,860m/12,660ft', 'duration' => '5 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A roughly five-hour hike beginning with a gradual ascent through pine forest and a crossing of the Dudh Koshi River. Climbing a series of ridges reveals views of Ama Dablam, Lhotse, and Everest. After lunch at Phunki Thenga, a steeper ascent leads to Tengboche, where you can visit the monastery and watch the monks\' ceremony.'],
                    ['day' => 'Day 6', 'title' => 'Trek to Dingboche', 'altitude' => '4,410m/14,464ft', 'duration' => '5 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The trail follows the Dudh Koshi beneath Ama Dablam, Lhotse, Nuptse, and Thamserku, passing above Pangboche with views of its monastery. As the terrain widens into the Imja Valley, the trail descends to the Lobuche River before climbing sharply to Dingboche, a windswept village with stone-walled fields, prayer flags, and chortens.'],
                    ['day' => 'Day 7', 'title' => 'Acclimatization day in Dingboche', 'altitude' => 'Nangkartshang Peak (5,083m)', 'duration' => '4-5 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A second acclimatization day. A strenuous but worthwhile short climb to Nangkartshang Peak (5,083m) rewards you with views of Ama Dablam, Lhotse, Nuptse, and Lobuche, before descending back to Dingboche for the night.'],
                    ['day' => 'Day 8', 'title' => 'Trek to Lobuche', 'altitude' => '4,910m/16,105ft', 'duration' => '5 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'An uphill hike to Thukla brings views of Taboche and Cholatse. The trail passes stone memorials at Thukla Pass honouring climbers lost on Everest, then follows the Khumbu Glacier\'s lateral moraine to Lobuche, with views of Nuptse, Lobuche, and Pumori.'],
                    ['day' => 'Day 9', 'title' => 'Trek to Everest Base Camp, return to Gorak Shep', 'altitude' => 'Everest Base Camp (5,364m/17,594ft)', 'duration' => '8 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The trail climbs to Gorak Shep, where you can leave your luggage before continuing to Everest Base Camp past the Khumbu Glacier. After time at base camp, you return to Gorak Shep for the night — the group does not stay overnight at base camp itself.'],
                    ['day' => 'Day 10', 'title' => 'Kala Patthar hike, descend to Pheriche', 'altitude' => 'Kala Patthar (5,545m/18,192ft)', 'duration' => '7 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'An early start for the short but steep climb to Kala Patthar, the highest point of the trek, for a sunrise view of the surrounding peaks — the only close-up view of Everest itself on the trip. After returning to Gorak Shep for breakfast, the trail continues down to Pheriche, home to a clinic run by the Himalayan Rescue Association.'],
                    ['day' => 'Day 11', 'title' => 'Trek to Namche', 'altitude' => '3,440m/11,283ft', 'duration' => '7 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The return trek to Namche Bazaar follows the same route back through Tengboche and Pangboche villages, crossing a suspension bridge before arriving in Namche.'],
                    ['day' => 'Day 12', 'title' => 'Trek to Lukla', 'altitude' => '2,840m/9,318ft', 'duration' => '7 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The final trekking day retraces the route back to Lukla through Jorsale, Monjo, and the other villages passed on the way up.'],
                    ['day' => 'Day 13', 'title' => 'Fly back to Kathmandu', 'altitude' => 'Kathmandu (1,400m/4,593ft)', 'duration' => '30-45 min', 'meals' => 'Breakfast & Dinner', 'description' => 'A scenic flight back to Kathmandu from Lukla (or via Manthali, depending on season). A representative meets you at the airport and transfers you back to your hotel for the night.'],
                    ['day' => 'Day 14', 'title' => 'The final farewell', 'meals' => 'Breakfast', 'description' => 'Depending on your flight time, staff transfer you to the airport around three hours before departure for your onward journey home.'],
                ],
                'includes' => [
                    'Airport transfers in Kathmandu',
                    'Domestic flights (Kathmandu/Manthali to Lukla and back)',
                    'Accommodation in Kathmandu before and after the trek',
                    'Accommodation in teahouses during the trek',
                    'Three meals per day during the trek (breakfast, lunch, dinner)',
                    'English-speaking trekking guide',
                    'Porter service (usually 1 porter for 2 trekkers)',
                    'Necessary trekking permits and national park fees',
                    'First aid kit carried by the guide',
                    'Trekking map',
                    'Duffel bag for porter-carried gear',
                    'Salary for all the staff',
                    'Government taxes and service charges',
                    'Sleeping bag rental',
                    'Down jacket rental',
                ],
                'excludes' => [
                    'International airfare to and from Kathmandu',
                    'Nepal entry visa fees',
                    'Travel insurance (including emergency helicopter evacuation)',
                    'Personal expenses (phone calls, internet, laundry, etc.)',
                    'Meals in Kathmandu (except those specified as included)',
                    'Extra nights in Kathmandu due to early arrival or late departure',
                    'Alcoholic and cold drinks during the trek',
                    'Hot showers and battery charging at teahouses',
                    'Tips for guides and porters',
                    'Personal trekking gear and clothing',
                ],
                'faq' => [
                    ['question' => 'How long does the Everest Base Camp Trek take?', 'answer' => 'Generally, the Everest Base Camp Trek can be accomplished in 12 to 14 days. However, various factors such as the number of acclimatization days, trekking route, weather situation, and a trekker\'s fitness level can impact the trek\'s duration.'],
                    ['question' => 'When is the best time for the Everest Base Camp Trek?', 'answer' => 'Spring (Mar-May) and autumn (Sep-Nov) are the most suitable times, with stable weather and fair temperatures. Trekking during winter is doable with the right clothing and gear, though colder; trekking during the monsoon is not recommended due to frequent rainfall, floods, and landslides.'],
                    ['question' => 'Can I obtain a visa for Nepal on arrival?', 'answer' => 'Yes, you can obtain a visa for Nepal upon arrival at Tribhuvan International Airport, provided you have the necessary documents. Indian citizens don\'t need a visa. Citizens of a small number of countries, including Nigeria, Ghana, Afghanistan, and Syria, cannot obtain visas on arrival and should check requirements before travelling.'],
                    ['question' => 'What documents do I need for the trek?', 'answer' => 'A passport with at least six months\' validity, more than two passport-sized photos, a copy of your international flight ticket, and travel insurance.'],
                    ['question' => 'Do I need prior trekking experience?', 'answer' => 'The Everest Base Camp Trek is rated as a moderate trek. If you have a good level of physical fitness, no prior trekking experience is needed — though it\'s best to train physically and mentally if this is your first trek.'],
                    ['question' => 'What permits are required for the trek?', 'answer' => 'The Sagarmatha National Park Entry Permit, the Khumbu Pasang Lhamu Rural Municipality Entrance Permit, and the Everest Trek Card.'],
                    ['question' => 'Are there ATMs or banking facilities on the route?', 'answer' => 'Only Nepali currency is used throughout the trek, so it\'s best to withdraw cash while in Kathmandu, where ATMs and money-exchange services are abundant. Namche Bazaar has some ATMs and banking facilities in the lower region, but it\'s easier to come prepared.'],
                ],
                'highlights' => [
                    'Trek through Sagarmatha National Park, a UNESCO World Heritage Site, alongside the Dudh Koshi river',
                    'Reach Everest Base Camp (5,364m) at the foot of the Khumbu Icefall',
                    'Climb Kala Patthar (5,545m) for the trek\'s best close-up view of Mount Everest',
                    'Cross high suspension bridges over the Dudh Koshi, including the Hillary Bridge',
                    'Explore Namche Bazaar, the bustling Sherpa capital of the Khumbu',
                    'Visit Tengboche Monastery, the largest monastery in the region',
                    'Walk beneath Ama Dablam, Lhotse, Nuptse, and Thamserku',
                    'Acclimatize properly with two dedicated rest days in Namche and Dingboche',
                    'Experience authentic Sherpa culture and hospitality in teahouses along the trail',
                    'Trek with an experienced English-speaking guide and full porter support',
                ],
                'checklist' => [
                    'Broken-in waterproof trekking boots and camp shoes',
                    'Warm base layers, a fleece, and an insulated down jacket',
                    'Waterproof/windproof jacket and trousers',
                    'Thermal gloves and a warm hat',
                    'UV-protection sunglasses and sunscreen',
                    'Trekking poles',
                    'Headlamp with spare batteries',
                    'Sleeping bag rated to -15°C or colder',
                    'Water bottles or a hydration bladder and purification tablets',
                    'First aid kit and personal medication',
                    'Duffel bag for porter-carried luggage and a daypack',
                    'Passport, permits, and travel insurance documents',
                ],
                'know_before_you_go' => '<h3>Best Time to Trek</h3><p>Spring (March-May) and autumn (September-November) offer the most stable weather and the clearest mountain views. Winter is possible for well-prepared trekkers, but some lodges close and temperatures drop well below freezing at night; the summer monsoon (June-August) brings rain, leeches, and unreliable flights and is best avoided.</p><h3>Fitness and Experience</h3><p>No technical climbing skills are required, but the trek involves long walking days at altitude. A good level of cardiovascular fitness, built up over 2-3 months of hiking, running, or stair training, makes the trek significantly more enjoyable.</p><h3>Altitude and Safety</h3><p>The itinerary includes two acclimatization days, in Namche Bazaar and Dingboche, following the "climb high, sleep low" principle. Your guide monitors for symptoms of altitude sickness throughout the trek, and travel insurance covering emergency evacuation is required.</p>',
                'do_not_do' => [
                    'Don\'t skip the acclimatization days in Namche and Dingboche, even if you\'re feeling strong',
                    'Don\'t rush the ascent — altitude sickness can affect trekkers of any fitness level',
                    'Don\'t drink untreated water — use purification tablets or a filter',
                    'Don\'t travel without insurance that covers trekking above 5,000m and emergency evacuation',
                    'Don\'t ignore headaches, nausea, or dizziness — tell your guide immediately',
                ],
                'reviews' => [
                    ['reviewer_name' => 'Sarah Mitchell', 'reviewer_role' => 'Trekked October 2025, United Kingdom', 'quote' => 'Standing at Everest Base Camp was everything I dreamed of and more. Our guide was knowledgeable and made sure we acclimatized properly the whole way up.'],
                    ['reviewer_name' => 'James Carter', 'reviewer_role' => 'Trekked April 2025, Australia', 'quote' => 'The sunrise from Kala Patthar was the highlight of my life. Well organized from the airport pickup to the final farewell dinner.'],
                    ['reviewer_name' => 'Priya Nair', 'reviewer_role' => 'Trekked November 2024, India', 'quote' => 'Fourteen days of pure magic. The teahouses were comfortable and the food was surprisingly good even at 4,900m.'],
                    ['reviewer_name' => 'Lukas Weber', 'reviewer_role' => 'Trekked March 2025, Germany', 'quote' => 'Our porter and guide took such good care of us. The acclimatization days in Namche and Dingboche made all the difference.'],
                    ['reviewer_name' => 'Emily Turner', 'reviewer_role' => 'Trekked October 2024, Canada', 'quote' => 'I was nervous about the altitude but the pace was perfect and the views of Ama Dablam were unforgettable.'],
                    ['reviewer_name' => 'Haruto Sato', 'reviewer_role' => 'Trekked May 2025, Japan', 'quote' => 'A well-planned itinerary with knowledgeable Sherpa guides. Reaching base camp felt like a real achievement.'],
                    ['reviewer_name' => 'Isabella Romano', 'reviewer_role' => 'Trekked September 2025, Italy', 'quote' => 'Everest Trip Planner handled every detail perfectly, from permits to the flight to Lukla. Highly recommend.'],
                    ['reviewer_name' => 'Daniel Kim', 'reviewer_role' => 'Trekked April 2024, South Korea', 'quote' => 'Tengboche Monastery and the walk through Sagarmatha National Park were incredible. Worth every step.'],
                    ['reviewer_name' => 'Grace O\'Connor', 'reviewer_role' => 'Trekked November 2025, Ireland', 'quote' => 'The guide\'s daily health checks put my mind at ease about altitude sickness. An unforgettable two weeks.'],
                    ['reviewer_name' => 'Thomas Nguyen', 'reviewer_role' => 'Trekked October 2025, USA', 'quote' => 'Kala Patthar at sunrise with Everest right in front of us — I\'ll remember that moment forever.'],
                ],
            ],
            [
                'title' => 'Luxury Everest Base Camp Trek',
                'slug' => 'luxury-everest-base-camp-trek',
                'image' => 'everest-base-camp-luxury-trek.jpg',
                'duration' => '12 days',
                'difficulty' => TripDifficulty::Challenging,
                'altitude' => '5,545m',
                'overview' => '<p>The Luxury Everest Base Camp Trek is a 12-day premium Himalayan journey that pairs the world\'s most iconic trekking route with five-star comfort. Walk the classic trail through Sherpa villages, prayer-flag-draped monasteries, and glacial valleys to Everest Base Camp at 5,364m (17,598ft) &mdash; then skip the long descent with a breathtaking helicopter flight back to Kathmandu. With the finest lodges in the Khumbu, gourmet dining, private guides, and a carefully paced acclimatization profile, this is Everest as a once-in-a-lifetime experience, not an endurance test.</p><p>The trail to Everest Base Camp needs no introduction &mdash; but the way you experience it can be transformed entirely. This itinerary follows the legendary route pioneered by the 1953 expedition, through the heart of the Khumbu: across swaying suspension bridges over the Dudh Koshi, into the amphitheater town of Namche Bazaar, past the spiritual heart of the region at Tengboche Monastery, and finally along the Khumbu Glacier to base camp itself. What changes is everything around the walking.</p><p>Each night you retire to the finest accommodation the Khumbu offers. In the lower valley, that means genuine luxury lodges &mdash; think heated bedrooms with attached bathrooms, hot showers, duvets, espresso, and panoramic dining rooms facing the peaks. Higher up, where true luxury lodges thin out, we reserve the best premium rooms available, with heaters, comfortable bedding, and private facilities wherever they exist. Gourmet touches follow you up the mountain: freshly baked goods in Namche, multi-course dinners, and unlimited hot drinks to keep you warm and hydrated.</p><p>The trek is fully supported by a private, licensed, English-speaking Sherpa guide with extensive high-altitude experience, plus a dedicated porter team. Safety infrastructure is uncompromising: two built-in acclimatization days, daily pulse-oximeter checks, a comprehensive medical kit, supplemental oxygen carried above 4,000m, and helicopter evacuation coverage coordinated on your behalf if ever needed.</p><p>The signature flourish comes at the end. After sunrise on Kala Patthar &mdash; the finest viewpoint of Everest\'s summit pyramid anywhere on the trek &mdash; a private helicopter lifts you out of the high Himalaya, retracing eight days of trail in under an hour of jaw-dropping aerial scenery, and delivers you to Kathmandu in time for a spa afternoon and celebration dinner.</p><h3>Why Choose This Trek</h3><p>Most trekkers reach base camp exhausted, sleep-deprived, and facing three more days of dusty descent. This itinerary rewrites that experience. By combining the classic ascent &mdash; essential for safe acclimatization and for earning the journey &mdash; with a helicopter return, you keep every highlight of the walk while eliminating its most repetitive days. The time saved goes into comfort and recovery: a full leisure day in Kathmandu, better rest at altitude, and a schedule that leaves room to actually savor the Khumbu.</p><p>The premium lodge network makes a genuine difference to how your body copes with altitude. Warm rooms, proper beds, good food, and hot showers mean better sleep and stronger recovery &mdash; which translates directly into higher summit-day energy and a lower risk of altitude problems. Luxury here isn\'t indulgence for its own sake; it is the smartest way to trek high.</p><h3>Accommodation and Food</h3><p>In Kathmandu you stay at a five-star hotel with breakfast, spa access, and airport transfers by private vehicle. On the trail, nights in the lower Khumbu &mdash; Phakding, Namche, and Tengboche &mdash; are spent in true luxury lodges with heated rooms, attached bathrooms, hot showers, and fine dining rooms with mountain panoramas. Above Dingboche, where no five-star infrastructure exists, we secure the best premium rooms in each settlement, prioritizing warmth, cleanliness, and private facilities wherever they are offered.</p><p>Food is a highlight rather than fuel. Expect hearty breakfasts of eggs, porridge, pancakes, and fresh coffee; trail lunches at carefully chosen stops; and multi-course dinners featuring Sherpa stew, dal bhat, pasta, yak steak, momos, and fresh-baked desserts in the lower valley. Unlimited tea and coffee are included daily, and boiled drinking water keeps you safely hydrated without single-use plastics. Dietary requirements &mdash; vegetarian, vegan, gluten-free &mdash; are catered for with advance notice.</p>',
                'itinerary' => [
                    ['day' => 'Day 1', 'title' => 'Arrival in Kathmandu', 'description' => 'Arrive in Kathmandu (1,400m) and transfer to your hotel.'],
                    ['day' => 'Day 2', 'title' => 'Fly to Lukla, trek to Phakding', 'description' => 'Fly to Lukla, then a 3-4 hour trek to Phakding (2,610m).'],
                    ['day' => 'Day 3', 'title' => 'Trek to Namche Bazaar', 'description' => 'A 5-6 hour trek to Namche Bazaar (3,440m).'],
                    ['day' => 'Day 4', 'title' => 'Acclimatization hike to Everest View Hotel', 'description' => 'A 3-4 hour acclimatization hike from Namche Bazaar (3,440m) up to the Everest View Hotel (3,880m).'],
                    ['day' => 'Day 5', 'title' => 'Trek to Tengboche', 'description' => 'A 5-hour trek to Tengboche (3,860m).'],
                    ['day' => 'Day 6', 'title' => 'Trek to Dingboche', 'description' => 'A 5-6 hour trek to Dingboche (4,410m).'],
                    ['day' => 'Day 7', 'title' => 'Acclimatization hike to Nangkartshang Ridge', 'description' => 'A 3-4 hour acclimatization hike to Nangkartshang Ridge (5,083m) from Dingboche (4,410m).'],
                    ['day' => 'Day 8', 'title' => 'Trek to Lobuche', 'description' => 'A 5-hour trek to Lobuche (4,940m).'],
                    ['day' => 'Day 9', 'title' => 'Trek to Gorak Shep, visit Everest Base Camp', 'description' => 'A 7-8 hour day trekking to Gorak Shep (5,164m) and on to Everest Base Camp (5,364m).'],
                    ['day' => 'Day 10', 'title' => 'Kala Patthar sunrise, helicopter to Kathmandu', 'description' => 'A pre-dawn hike to Kala Patthar (5,545m) for sunrise, then a helicopter flight back to Kathmandu.'],
                    ['day' => 'Day 11', 'title' => 'Leisure day in Kathmandu', 'description' => 'A free day for the hotel spa, sightseeing, or shopping, followed by a farewell dinner.'],
                    ['day' => 'Day 12', 'title' => 'Departure', 'description' => 'Final departure from Kathmandu.'],
                ],
                'highlights' => [
                    'Stand at Everest Base Camp (5,364m / 17,598ft) beside the Khumbu Icefall, on the very ground where summit expeditions begin',
                    'Climb Kala Patthar (5,545m / 18,192ft) at dawn for the trek\'s definitive close-up panorama of Everest, Nuptse, and Pumori',
                    'Fly back to Kathmandu by helicopter from the high Khumbu — an unforgettable scenic flight that saves three days of descent',
                    'Sleep in the Khumbu\'s finest luxury lodges and premium rooms, with heated bedrooms, hot showers, and attached bathrooms wherever available',
                    'Dine well at altitude: multi-course meals, fresh bakery stops in Namche, and unlimited tea and coffee throughout the trek',
                    'Trek with a private, veteran Sherpa guide and generous porter support, keeping your daypack light and your pace personal',
                    'Visit Tengboche Monastery, the spiritual heart of the Khumbu, with Ama Dablam rising behind its golden roof',
                    'Acclimatize properly with two dedicated rest-and-hike days in Namche Bazaar and Dingboche, following best-practice altitude protocols',
                    'Cross the famous Hillary Suspension Bridge and walk historic Sherpa trails through Phakding, Namche, and Pangboche',
                    'Unwind in five-star comfort in Kathmandu before and after the trek, with a leisure day, spa time, and a farewell celebration dinner',
                ],
                'detailed_itinerary' => [
                    ['day' => 'Day 1', 'title' => 'Arrival in Kathmandu', 'altitude' => '1,400m', 'distance' => '-', 'duration' => '-', 'accommodation' => '5-star hotel', 'meals' => 'Welcome Dinner', 'description' => 'A private vehicle and representative meet you at Tribhuvan International Airport for the transfer to your five-star hotel. After check-in and time to rest, you meet your Sherpa guide for a detailed briefing and gear check, then join a welcome dinner of fine Nepali cuisine with cultural performances.'],
                    ['day' => 'Day 2', 'title' => 'Fly to Lukla, trek to Phakding', 'altitude' => '2,860m', 'distance' => '8 km', 'duration' => '3-4 hours', 'accommodation' => 'Luxury lodge', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'An early transfer takes you to the airport for the 35-minute mountain flight to Lukla\'s Tenzing-Hillary Airport (flights may operate via Ramechhap in peak season, or a private helicopter transfer can be arranged). From Lukla, the trail descends gently through pine forest and Sherpa hamlets along the Dudh Koshi river to Phakding, where your first luxury lodge awaits with hot showers and river-view dining.'],
                    ['day' => 'Day 3', 'title' => 'Trek to Namche Bazaar', 'altitude' => '3,440m', 'distance' => '11 km', 'duration' => '5-6 hours', 'accommodation' => 'Luxury lodge', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The trail crosses the Dudh Koshi repeatedly on high suspension bridges, including the iconic Hillary Bridge, before entering Sagarmatha National Park at Monjo. A steady climb through blue pine forest brings your first glimpse of Everest\'s summit plume before you reach Namche Bazaar, the amphitheater-shaped capital of the Khumbu.'],
                    ['day' => 'Day 4', 'title' => 'Acclimatization day in Namche Bazaar', 'altitude' => '3,880m', 'distance' => '6 km', 'duration' => '3-4 hours', 'accommodation' => 'Luxury lodge', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'After a leisurely breakfast, hike up to the Everest View Hotel (3,880m) for coffee facing Everest, Lhotse, and Ama Dablam. Descend via Khumjung village with its Hillary school and monastery, then spend the afternoon exploring Namche\'s bakeries, gear shops, and the Sherpa Culture Museum before returning to your lodge.'],
                    ['day' => 'Day 5', 'title' => 'Trek to Tengboche', 'altitude' => '3,860m', 'distance' => '10 km', 'duration' => '5 hours', 'accommodation' => 'Luxury lodge', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The trail contours high above the Dudh Koshi with continuous views of Ama Dablam, drops to the river at Phunki Tenga, then climbs steadily to the Tengboche ridge. Tengboche Monastery, the largest in the Khumbu, crowns the ridge — time your arrival for the afternoon prayer ceremony.'],
                    ['day' => 'Day 6', 'title' => 'Trek to Dingboche', 'altitude' => '4,410m', 'distance' => '11 km', 'duration' => '5-6 hours', 'accommodation' => 'Premium teahouse', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'Descend through birch and rhododendron forest to Deboche, cross the Imja Khola, and climb past the ancient monastery village of Pangboche, home to the oldest gompa in the Khumbu. The landscape opens dramatically past the tree line into the wide Imja Valley, ending at Dingboche.'],
                    ['day' => 'Day 7', 'title' => 'Acclimatization day in Dingboche', 'altitude' => '5,083m', 'distance' => '5 km', 'duration' => '3-4 hours', 'accommodation' => 'Premium teahouse', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A climb-high, sleep-low acclimatization hike up Nangkartshang Ridge (5,083m) rewards you with views of Makalu, Lhotse\'s south face, and Island Peak across the Imja Valley, before a relaxed afternoon back at your lodge in Dingboche.'],
                    ['day' => 'Day 8', 'title' => 'Trek to Lobuche', 'altitude' => '4,940m', 'distance' => '8 km', 'duration' => '5 hours', 'accommodation' => 'Premium teahouse', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The trail climbs along the broad valley to Thukla, then tackles the steep moraine of the Khumbu Glacier to Thukla Pass, where memorials honor climbers lost on Everest, before a gentler path along the glacier\'s lateral moraine to Lobuche.'],
                    ['day' => 'Day 9', 'title' => 'Trek to Gorak Shep, visit Everest Base Camp', 'altitude' => '5,364m', 'distance' => '13 km', 'duration' => '7-8 hours', 'accommodation' => 'Best-available teahouse', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A rocky trail alongside the Khumbu Glacier brings you to Gorak Shep, the last settlement, where you check in before the round-trip to base camp. The final stretch weaves across glacial moraine to the marker at 5,364m, beside the tumbling ice towers of the Khumbu Icefall, before returning to Gorak Shep.'],
                    ['day' => 'Day 10', 'title' => 'Kala Patthar sunrise, helicopter to Kathmandu', 'altitude' => '5,545m', 'distance' => '4 km', 'duration' => '3-4 hours', 'accommodation' => '5-star hotel', 'meals' => 'Breakfast', 'description' => 'A pre-dawn start for the ascent of Kala Patthar, the black rocky viewpoint above Gorak Shep, for the closest and finest view of Everest\'s summit pyramid on the entire route. After breakfast, your helicopter lifts off for a spectacular flight down the length of the Khumbu, landing in Kathmandu by late morning.'],
                    ['day' => 'Day 11', 'title' => 'Leisure day in Kathmandu', 'altitude' => '1,400m', 'distance' => '-', 'duration' => '-', 'accommodation' => '5-star hotel', 'meals' => 'Breakfast, Farewell Dinner', 'description' => 'A full day to recover and celebrate: the hotel spa, a private guided tour of UNESCO World Heritage sites such as Boudhanath Stupa or Pashupatinath Temple, or browsing Thamel for souvenirs. In the evening, a farewell dinner and trek completion certificate.'],
                    ['day' => 'Day 12', 'title' => 'Departure', 'altitude' => '-', 'distance' => '-', 'duration' => '-', 'accommodation' => '-', 'meals' => 'Breakfast', 'description' => 'A final leisurely breakfast, then a private vehicle transfer to Tribhuvan International Airport for your onward flight.'],
                ],
                'checklist' => [
                    'Merino or synthetic base layers (2-3) and trekking shirts',
                    'Mid-weight fleece and insulated down jacket (provided if needed)',
                    'Waterproof/windproof outer shell',
                    'Trekking trousers (2), thermal leggings, and waterproof over-trousers',
                    'Comfortable lodge wear and underwear',
                    'Broken-in waterproof trekking boots and insulated camp shoes',
                    'Warm trekking socks (4-5 pairs) and liner socks',
                    'Lightweight gloves plus insulated mittens',
                    'Warm beanie, sun hat, and buff or balaclava',
                    'Category 3-4 glacier sunglasses',
                    'SPF 50 sunscreen and lip balm with SPF',
                    'Personal medications and blister care',
                    'Water purification backup, hand sanitizer, and wet wipes',
                    'Quick-dry towel and toiletries',
                    'Headlamp with spare batteries and ear plugs',
                    'Passport (6+ months validity) and 4 passport photos',
                    'Insurance certificate showing helicopter-evacuation cover',
                    'Flight details and cash in NPR (last reliable ATMs are in Namche Bazaar)',
                    '30-40L daypack with rain cover',
                    '2L insulated water bottles (bladders freeze above Lobuche — bottles recommended)',
                    'Trekking poles, power bank, and camera',
                    'Snacks, journal, and dry bags',
                ],
                'know_before_you_go' => '<h3>Best Time to Go</h3><p>Autumn (September&ndash;November) delivers the sharpest mountain views of the year with stable weather and daytime temperatures of 8-15&deg;C in the mid-valley; nights above Lobuche fall well below freezing, though heated luxury rooms take the sting out of the cold. Spring (March&ndash;May) is expedition season, when base camp transforms into a vibrant tent city preparing for summit bids, with rhododendrons blooming below Namche. Winter treks are possible for cold-tolerant travelers, though some high lodges close, and the summer monsoon (June&ndash;August) is not recommended due to rain, cloud, and unreliable Lukla flights.</p><h3>Is This Trek Right for You?</h3><p>No mountaineering background is needed &mdash; a training block of regular hiking, stair work, or cycling in the two to three months before departure is enough to prepare you. The route suits everyone from fit first-timers to seasoned trekkers celebrating milestone birthdays and honeymoons, and private departures let couples, families, and small groups move at their own pace. If you have pre-existing heart, lung, or blood-pressure conditions, consult your doctor before booking.</p><h3>Safety and Acclimatization</h3><p>Sleeping altitudes rise gradually &mdash; Namche (3,440m), Tengboche (3,860m), Dingboche (4,410m), Lobuche (4,940m) &mdash; with acclimatization hikes in Namche and Dingboche following the "climb high, sleep low" principle. Your guide performs daily pulse-oximeter and wellness checks above 3,000m, carries a full medical kit, and travels with supplemental oxygen for emergencies. Because a helicopter is already built into the itinerary, evacuation logistics are unusually fast on this trip, and your mandatory insurance (covering evacuation to 6,000m) is verified before departure.</p><p>The trekking route from Lukla to Everest Base Camp covers approximately 60 km one way; with the helicopter return, your total walking distance is roughly 65 km including acclimatization hikes.</p>',
                'do_not_do' => [
                    'Don\'t skip the two scheduled acclimatization days, even if you feel strong',
                    'Don\'t skip pre-departure training — 8-10 weeks of cardio preparation is recommended',
                    'Don\'t travel without insurance that covers helicopter evacuation and trekking to 6,000m',
                    'Don\'t ignore symptoms of altitude sickness — tell your guide immediately',
                    'Don\'t rely on water bladders above Lobuche; they freeze — carry insulated bottles instead',
                ],
                'includes' => [
                    'Private airport transfers in Kathmandu (arrival and departure)',
                    '3 nights\' five-star hotel accommodation in Kathmandu with breakfast',
                    'Kathmandu-Lukla flight (or Ramechhap routing in peak season; private heli upgrade available)',
                    'Helicopter return flight from Gorak Shep to Kathmandu (weight-shared; private charter available)',
                    'Luxury lodge accommodation in the lower Khumbu and best-available premium rooms at higher elevations',
                    'All meals during the trek (breakfast, lunch, dinner) with unlimited tea/coffee and boiled drinking water',
                    'Sagarmatha National Park entry permit and Khumbu Pasang Lhamu Rural Municipality permit',
                    'Private, licensed, English-speaking Sherpa guide (salary, insurance, equipment, meals, and lodging included)',
                    'Porter service (1 porter per 2 trekkers, max 20 kg combined)',
                    'Comprehensive medical kit, pulse oximeter, and supplemental oxygen carried above 4,000m',
                    'Four-season sleeping bag, down jacket, and duffel bag (returned after the trek)',
                    'Welcome and farewell dinners in Kathmandu',
                    'Trek completion certificate',
                    'All government taxes and service charges',
                ],
                'excludes' => [
                    'International airfare to and from Kathmandu',
                    'Nepal entry visa (available on arrival)',
                    'Travel insurance — mandatory, must cover helicopter evacuation and trekking to 6,000m',
                    'Lunch and dinner in Kathmandu outside welcome/farewell dinners',
                    'Personal expenses (laundry, Wi-Fi, battery charging, and hot showers at basic high-altitude teahouses)',
                    'Alcoholic beverages, soft drinks, and bottled water',
                    'Spa treatments and optional sightseeing entrance fees in Kathmandu',
                    'Tips for guide, porters, and drivers (customary but discretionary)',
                    'Extra hotel nights due to early return or flight delays',
                    'Costs arising from events beyond our control (weather, flight cancellations, health issues)',
                ],
                'faq' => [
                    ['question' => 'What makes this a "luxury" Everest Base Camp trek?', 'answer' => 'Three things: accommodation, support, and the exit. You sleep in the Khumbu\'s best lodges, trek with a private Sherpa guide and full porter support, and return from the high mountains by helicopter instead of retracing the trail for three days.'],
                    ['question' => 'How long is the trek?', 'answer' => 'The full itinerary is 12 days, with 9 days in the mountains and 8 days of actual walking. The helicopter return compresses what is normally a 14-16 day round trip without sacrificing any highlight of the ascent.'],
                    ['question' => 'How high will I go?', 'answer' => 'You will visit Everest Base Camp at 5,364m (17,598ft) and climb Kala Patthar at 5,545m (18,192ft) for the classic sunrise view of Everest. The highest sleeping altitude is Gorak Shep at 5,164m.'],
                    ['question' => 'Do I need mountaineering experience?', 'answer' => 'No. This is a trek, not a climb — no ropes, crampons, or technical terrain. Good fitness and sensible acclimatization are the only requirements, and both are built into the itinerary.'],
                    ['question' => 'When is the best time to go?', 'answer' => 'Autumn (September-November) for the clearest skies and most stable weather, or spring (March-May) to see base camp alive with summit expeditions. Both seasons deliver superb views.'],
                    ['question' => 'Are the luxury lodges available all the way to base camp?', 'answer' => 'True luxury lodges operate up to roughly Tengboche/Pangboche. Above that, we book the finest premium rooms available in Dingboche, Lobuche, and Gorak Shep, so comfort declines gently rather than suddenly.'],
                    ['question' => 'Is the helicopter return safe and reliable?', 'answer' => 'Yes. We work with Kathmandu\'s most experienced high-altitude helicopter operators, flying weather-permitting morning slots when conditions are calmest. In the rare event weather delays the flight, your guide manages the schedule and alternative plans at no stress to you.'],
                    ['question' => 'What about altitude sickness?', 'answer' => 'Our itinerary includes two acclimatization days and conservative altitude gains, and your guide checks oxygen saturation daily above 3,000m. Supplemental oxygen is carried for emergencies, and descent or evacuation is always prioritized over the schedule if symptoms appear.'],
                    ['question' => 'What permits do I need?', 'answer' => 'Two: the Sagarmatha National Park entry permit and the Khumbu Pasang Lhamu Rural Municipality permit. Both are arranged by us and included in the trip cost.'],
                    ['question' => 'Do I need travel insurance?', 'answer' => 'Yes — it is mandatory and must cover emergency helicopter evacuation and trekking up to 6,000m. We verify every trekker\'s policy before departure and coordinate directly with insurers if evacuation is ever required.'],
                ],
                'reviews' => [
                    ['reviewer_name' => 'Charlotte Bennett', 'reviewer_role' => 'Trekked October 2025, United Kingdom', 'quote' => 'The heated lodges and private guide made this feel like a completely different trek. Comfort at altitude I didn\'t think was possible.'],
                    ['reviewer_name' => 'Robert Hayes', 'reviewer_role' => 'Trekked April 2025, USA', 'quote' => 'The helicopter flight back over the Khumbu was worth the price alone. A truly once-in-a-lifetime trip.'],
                    ['reviewer_name' => 'Sophie Laurent', 'reviewer_role' => 'Trekked November 2024, France', 'quote' => 'Hot showers and proper beds every night, even close to base camp. Exceeded every expectation.'],
                    ['reviewer_name' => 'Michael Chen', 'reviewer_role' => 'Trekked March 2025, Singapore', 'quote' => 'Our private Sherpa guide was outstanding, and the food along the way was genuinely gourmet.'],
                    ['reviewer_name' => 'Anna Kowalski', 'reviewer_role' => 'Trekked September 2025, Poland', 'quote' => 'Twelve days that felt indulgent and adventurous at the same time. The farewell dinner in Kathmandu was a lovely touch.'],
                    ['reviewer_name' => 'David Osei', 'reviewer_role' => 'Trekked May 2025, Ghana', 'quote' => 'The extra comfort made a real difference to how I recovered each night at altitude. Would book again.'],
                    ['reviewer_name' => 'Mei Lin', 'reviewer_role' => 'Trekked October 2024, Malaysia', 'quote' => 'Watching sunrise from Kala Patthar and being back in Kathmandu for dinner the same day still feels surreal.'],
                    ['reviewer_name' => 'Ethan Brooks', 'reviewer_role' => 'Trekked April 2025, Canada', 'quote' => 'Every logistical detail was handled — permits, gear, oxygen on standby. I just had to walk and enjoy it.'],
                    ['reviewer_name' => 'Olivia Fischer', 'reviewer_role' => 'Trekked November 2025, Austria', 'quote' => 'The private porters meant we barely carried anything. Best trekking experience of my life.'],
                    ['reviewer_name' => 'Rajesh Kumar', 'reviewer_role' => 'Trekked September 2024, India', 'quote' => 'Worth every cent for the comfort alone, but the helicopter ride down sealed it as an unforgettable trip.'],
                ],
            ],
            [
                'title' => 'Everest Base Camp Trek with Helicopter Return',
                'slug' => 'everest-base-camp-trek-with-helicopter-return',
                'image' => 'everest-base-camp.jpg',
                'duration' => '11 days',
                'difficulty' => TripDifficulty::Moderate,
                'altitude' => '5,545m',
                'overview' => '<p>All the highlights of the classic Everest Base Camp Trek, in less time. You walk up through Sagarmatha National Park and the Sherpa villages of Namche Bazaar and Tengboche to Everest Base Camp and Kala Patthar, then skip the multi-day walk back down — a helicopter flies you from the high Khumbu straight back to Kathmandu.</p><p>This itinerary keeps every highlight of the ascent — the suspension bridges over the Dudh Koshi, the Sherpa capital of Namche Bazaar, Tengboche Monastery, and the dramatic high country around Dingboche and Lobuche — while cutting three to four days off the standard round trip. Instead of retracing your steps back to Lukla, you fly out from the mountains after your summit-morning hike to Kala Patthar, landing in Kathmandu the same day.</p><p>It\'s a great option for trekkers who want the full Everest Base Camp experience without the time commitment of the return walk, or who\'d simply rather see the Khumbu from the air on the way out.</p>',
                'highlights' => [
                    'Trek the classic route to Everest Base Camp (5,364m) through Sagarmatha National Park',
                    'Climb Kala Patthar (5,545m) at sunrise for the best close-up view of Everest on the trek',
                    'Skip the multi-day walk back down — fly out of the high Khumbu by helicopter instead',
                    'Cross the Dudh Koshi on high suspension bridges, including the Hillary Bridge',
                    'Visit Tengboche Monastery, the spiritual heart of the Khumbu',
                    'Acclimatize properly with a dedicated rest day in Namche Bazaar',
                    'Enjoy an unforgettable scenic helicopter flight over the Khumbu on the way home',
                    'Save 3-4 days compared to the standard round-trip itinerary',
                ],
                'detailed_itinerary' => [
                    ['day' => 'Day 1', 'title' => 'Arrival in Kathmandu', 'altitude' => '1,300m', 'meals' => 'Welcome Dinner', 'description' => 'You arrive at Tribhuvan International Airport in Kathmandu, where an Everest Trip Planner staff member greets you and transfers you to your hotel. Meet your guide for a trip briefing and gear check, then rest overnight in Kathmandu.'],
                    ['day' => 'Day 2', 'title' => 'Fly from Kathmandu to Lukla and trek to Phakding', 'altitude' => 'Lukla (2,840m)', 'duration' => '30-45 min flight, 3 hrs trek', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A scenic mountain flight to Lukla (or via Manthali in peak season), then a gentle 3-hour trek down through pine forest and Sherpa hamlets along the Dudh Koshi to Phakding.'],
                    ['day' => 'Day 3', 'title' => 'Trek to Namche Bazaar', 'altitude' => '3,440m', 'duration' => '7 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A steep but scenic climb crossing the Dudh Koshi on high suspension bridges, entering Sagarmatha National Park at Monjo, and continuing through Jorsale to Namche Bazaar, the bustling gateway to Everest.'],
                    ['day' => 'Day 4', 'title' => 'Acclimatization day in Namche', 'altitude' => '3,440m', 'duration' => '3-4 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A short acclimatization hike, often up to the Everest View Hotel for panoramic mountain views, before returning to Namche to explore its shops, bakeries, and museum.'],
                    ['day' => 'Day 5', 'title' => 'Trek to Tengboche', 'altitude' => '3,860m', 'duration' => '5 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A gradual ascent through pine forest and a crossing of the Dudh Koshi River, with views of Ama Dablam, Lhotse, and Everest, ending at Tengboche Monastery, the largest in the Khumbu.'],
                    ['day' => 'Day 6', 'title' => 'Trek to Dingboche', 'altitude' => '4,410m', 'duration' => '4-5 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The trail follows the Dudh Koshi beneath Ama Dablam and Lhotse, passing above Pangboche before climbing into the wide Imja Valley to the windswept village of Dingboche.'],
                    ['day' => 'Day 7', 'title' => 'Trek to Lobuche', 'altitude' => '4,910m', 'duration' => '5 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'An uphill hike to Thukla, past stone memorials honouring climbers lost on Everest, then along the Khumbu Glacier\'s lateral moraine to Lobuche.'],
                    ['day' => 'Day 8', 'title' => 'Trek to Everest Base Camp, return to Gorak Shep', 'altitude' => 'Everest Base Camp (5,364m)', 'duration' => '8 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The trail climbs to Gorak Shep, then continues past the Khumbu Glacier to Everest Base Camp itself, before returning to Gorak Shep for the night.'],
                    ['day' => 'Day 9', 'title' => 'Kala Patthar sunrise hike, helicopter to Kathmandu', 'altitude' => 'Kala Patthar (5,545m)', 'duration' => '3-4 hrs hike + helicopter flight', 'meals' => 'Breakfast', 'description' => 'A pre-dawn climb to Kala Patthar for a sunrise view of Everest\'s summit pyramid, the definitive photo opportunity of the trip. Back at Gorak Shep, your helicopter lifts off for a spectacular flight down the length of the Khumbu, landing in Kathmandu by early afternoon.'],
                    ['day' => 'Day 10', 'title' => 'Leisure day in Kathmandu', 'altitude' => '1,300m', 'meals' => 'Breakfast', 'description' => 'A free day to rest, explore Kathmandu\'s UNESCO World Heritage sites, or shop in Thamel, followed by a farewell dinner.'],
                    ['day' => 'Day 11', 'title' => 'Departure', 'meals' => 'Breakfast', 'description' => 'A final breakfast, then a transfer to Tribhuvan International Airport for your onward flight.'],
                ],
                'checklist' => [
                    'Broken-in waterproof trekking boots and camp shoes',
                    'Warm base layers, a fleece, and an insulated down jacket',
                    'Waterproof/windproof jacket and trousers',
                    'Thermal gloves and a warm hat',
                    'UV-protection sunglasses and sunscreen',
                    'Trekking poles',
                    'Headlamp with spare batteries',
                    'Sleeping bag rated to -15°C or colder',
                    'Water bottles or a hydration bladder and purification tablets',
                    'First aid kit and personal medication',
                    'Duffel bag for porter-carried luggage and a daypack',
                    'Passport, permits, and insurance documents showing helicopter-evacuation cover',
                ],
                'know_before_you_go' => '<h3>Best Time to Go</h3><p>Spring (March-May) and autumn (September-November) offer the most stable weather, clearest views, and the most reliable helicopter flying conditions. Winter and monsoon trips are possible but carry a higher chance of weather delays.</p><h3>How This Differs From the Standard Trek</h3><p>The ascent follows the exact same route, pacing, and acclimatization schedule as the standard Everest Base Camp Trek — nothing about the walk up is rushed. The only change is the return: instead of three to four days retracing your steps to Lukla, a helicopter flies you out from the high Khumbu after your Kala Patthar sunrise hike.</p><h3>Weather and Flight Reliability</h3><p>Helicopter flights depend on weather, and delays are occasionally necessary for safety. We recommend avoiding a same-day onward international flight, in case the helicopter needs to be rescheduled by a day.</p>',
                'do_not_do' => [
                    'Don\'t skip the acclimatization day in Namche, even with the shorter itinerary',
                    'Don\'t book a same-day onward international flight — helicopter timing can shift with weather',
                    'Don\'t travel without insurance that covers helicopter evacuation and trekking above 5,000m',
                    'Don\'t ignore symptoms of altitude sickness — descent and evacuation always take priority over the schedule',
                ],
                'includes' => [
                    'Airport transfers in Kathmandu',
                    'Kathmandu-Lukla flight',
                    'Helicopter flight from the Everest region back to Kathmandu (weight-shared; private charter available)',
                    'Accommodation in teahouses during the trek',
                    'Three meals per day during the trek',
                    'English-speaking trekking guide',
                    'Porter service (usually 1 porter for 2 trekkers)',
                    'Necessary trekking permits and national park fees',
                    'First aid kit, trekking map, and duffel bag',
                    'Sleeping bag and down jacket rental',
                    'Salary for all staff and government taxes',
                ],
                'excludes' => [
                    'International airfare to and from Kathmandu',
                    'Nepal entry visa fees',
                    'Travel insurance (including emergency helicopter evacuation)',
                    'Personal expenses (phone calls, internet, laundry, etc.)',
                    'Meals and accommodation in Kathmandu outside those included',
                    'Alcoholic and cold drinks during the trek',
                    'Hot showers and battery charging at teahouses',
                    'Tips for guide, porters, and helicopter crew',
                    'Extra costs from flight delays or weather-related rescheduling',
                ],
                'faq' => [
                    ['question' => 'How is this different from the standard Everest Base Camp Trek?', 'answer' => 'The route up is identical, with the same acclimatization schedule. The difference is the return: a helicopter flight from the high Khumbu instead of three to four days walking back to Lukla, shortening the trip from 14 to 11 days.'],
                    ['question' => 'Is the helicopter flight safe?', 'answer' => 'Yes. We work with experienced high-altitude helicopter operators, flying weather-permitting morning slots when conditions are calmest.'],
                    ['question' => 'What if the helicopter is delayed by weather?', 'answer' => 'Your guide manages the schedule and rebooking. Occasionally this means an extra night at altitude, so we recommend not booking a same-day onward international flight.'],
                    ['question' => 'Do I need travel insurance?', 'answer' => 'Yes — it is mandatory and must cover emergency helicopter evacuation and trekking above 5,000m.'],
                    ['question' => 'How high do I go?', 'answer' => 'You reach Everest Base Camp at 5,364m and climb Kala Patthar at 5,545m for the classic sunrise view of Everest.'],
                ],
                'reviews' => [
                    ['reviewer_name' => 'Laura Simmons', 'reviewer_role' => 'Trekked October 2025, USA', 'quote' => 'Getting to skip the walk back down and fly over the Khumbu instead was the perfect way to end an already amazing trek.'],
                    ['reviewer_name' => 'Marco Rossi', 'reviewer_role' => 'Trekked April 2025, Italy', 'quote' => 'All the classic base camp highlights without the extra days of walking back. Great value for the time saved.'],
                    ['reviewer_name' => 'Chloe Dubois', 'reviewer_role' => 'Trekked November 2024, France', 'quote' => 'The helicopter flight after Kala Patthar was breathtaking — you see the whole route you just walked from the air.'],
                    ['reviewer_name' => 'Ahmed Hassan', 'reviewer_role' => 'Trekked March 2025, UAE', 'quote' => 'Perfect for anyone short on time but who still wants the full base camp experience.'],
                    ['reviewer_name' => 'Rachel Adams', 'reviewer_role' => 'Trekked September 2025, Australia', 'quote' => 'My knees thanked me for skipping the descent. Still got every highlight of the trek up.'],
                    ['reviewer_name' => 'Tobias Lindqvist', 'reviewer_role' => 'Trekked May 2025, Sweden', 'quote' => 'Great itinerary — the pacing up was identical to the standard trek, so acclimatization wasn\'t rushed at all.'],
                    ['reviewer_name' => 'Yuki Tanaka', 'reviewer_role' => 'Trekked October 2024, Japan', 'quote' => 'Landing back in Kathmandu the same afternoon after being at Kala Patthar that morning still feels unreal.'],
                    ['reviewer_name' => 'Nadia Petrova', 'reviewer_role' => 'Trekked April 2025, Russia', 'quote' => 'A brilliant compromise between adventure and convenience. Our guide managed the helicopter timing flawlessly.'],
                    ['reviewer_name' => 'Sean Murphy', 'reviewer_role' => 'Trekked November 2025, Ireland', 'quote' => 'Highly recommend if you\'ve got 11 days rather than 14. Didn\'t feel like we missed anything.'],
                    ['reviewer_name' => 'Fatima Al-Farsi', 'reviewer_role' => 'Trekked September 2024, Oman', 'quote' => 'The helicopter portion was the icing on the cake after a genuinely well-run trek up to base camp.'],
                ],
            ],
            [
                'title' => 'Gokyo Valley Trek',
                'slug' => 'gokyo-valley-trek',
                'image' => 'gokyo-valley-trek.jpg',
                'duration' => '14 days',
                'difficulty' => TripDifficulty::Moderate,
                'altitude' => '5,357m',
                'overview' => '<p>One of the most popular alternatives to the Everest Base Camp Trek, the Gokyo Valley Trek leads to a quiet, tranquil valley of 19 lakes of religious importance to both Hindus and Buddhists. Set at high altitude, the valley is a spiritually uplifting experience in itself, alongside its unique geology as a high, glacier-fed lake system.</p><p>The Gokyo Valley Trek is an exciting but strenuous 14-day journey, gaining around 2,500 metres in altitude while passing through Sagarmatha National Park and Sherpa villages including Dole, Machhermo, Thame, and Sansa, with views of Cho Oyu (8,201m), Makalu (8,475m), and Mount Everest (8,848m). The trek\'s highest point, Gokyo Ri (5,357m), offers a panoramic view of the Everest massif and the wide Gokyo valley below.</p>',
                'highlights' => [
                    'Visit the sacred Gokyo Lakes, a system of glacier-fed lakes held holy by Hindus and Buddhists',
                    'Climb Gokyo Ri (5,357m) for one of the best panoramic views in the Everest region, including 4 of the world\'s 6 highest peaks',
                    'Walk alongside the Ngozumpa Glacier, the longest glacier in the Himalaya',
                    'Take a quieter, less-crowded alternative to the main Everest Base Camp trail',
                    'Pass through remote Sherpa villages including Phortse, Machherma, and Dole',
                    'See close-up views of Cho Oyu (8,201m), Makalu (8,475m), and Mount Everest (8,848m)',
                    'Spot Himalayan wildlife, including tahr, in Sagarmatha National Park',
                    'Acclimatize gradually through the forested lower valleys of Sagarmatha National Park',
                ],
                'detailed_itinerary' => [
                    ['day' => 'Day 1', 'title' => 'Arrive in Kathmandu, Nepal, and transfer to the hotel by private vehicle', 'altitude' => '1,300m', 'meals' => 'Welcome Dinner', 'description' => 'A private vehicle and representative meet you at Tribhuvan International Airport for the transfer to your hotel. Meet your guide for a trip briefing and gear check, then rest overnight in Kathmandu.'],
                    ['day' => 'Day 2', 'title' => 'Kathmandu sightseeing and trek preparation', 'altitude' => '1,300m', 'meals' => 'Breakfast', 'description' => 'A day to explore Kathmandu\'s UNESCO World Heritage sites, sort last-minute trekking gear, and go over the route ahead with your guide.'],
                    ['day' => 'Day 3', 'title' => 'Flight from Kathmandu to Lukla and trek to Phakding', 'altitude' => '2,800m', 'duration' => '3-4 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A scenic mountain flight into Lukla, followed by a gentle walk down to Phakding along the Dudh Koshi river.'],
                    ['day' => 'Day 4', 'title' => 'Trek from Phakding to Namche Bazaar', 'altitude' => '3,440m', 'duration' => '5-6 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The trail crosses the Dudh Koshi on high suspension bridges and enters Sagarmatha National Park at Monjo, before climbing steadily to Namche Bazaar.'],
                    ['day' => 'Day 5', 'title' => 'Acclimatization day in Namche Bazaar', 'altitude' => '3,440m', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A short acclimatization hike for mountain views, with free time to explore Namche\'s market, bakeries, and shops.'],
                    ['day' => 'Day 6', 'title' => 'Trek from Namche Bazaar to Phortse Village', 'altitude' => '3,810m', 'duration' => '5-6 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A quieter trail branches off the main Everest Base Camp route, climbing through forest with a good chance of spotting Himalayan tahr, to the village of Phortse.'],
                    ['day' => 'Day 7', 'title' => 'Trek from Phortse to Machherma', 'altitude' => '4,470m', 'duration' => '5-6 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A gradually climbing valley walk along the Dudh Koshi, with views opening up towards Cho Oyu as the trail reaches Machherma.'],
                    ['day' => 'Day 8', 'title' => 'Trek from Machherma to Gokyo', 'altitude' => '4,800m', 'duration' => '5-6 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The trail climbs above the Ngozumpa Glacier before arriving at the shores of Dudh Pokhari, the first of the sacred Gokyo lakes.'],
                    ['day' => 'Day 9', 'title' => 'Hike to Gokyo Ri and back to Gokyo', 'altitude' => '5,357m', 'duration' => '5-6 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A pre-dawn climb to Gokyo Ri for a panoramic sunrise view over the Ngozumpa Glacier, with Everest, Lhotse, Makalu, and Cho Oyu all visible at once, before returning to Gokyo.'],
                    ['day' => 'Day 10', 'title' => 'Trek from Gokyo to Dole', 'altitude' => '4,200m', 'duration' => '3-4 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'A descent back down the valley, retracing the trail past Machherma to Dole.'],
                    ['day' => 'Day 11', 'title' => 'Trek from Dole to Namche', 'altitude' => '3,440m', 'duration' => '5-6 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The trail descends through forest back to Namche Bazaar for a final night in the Khumbu\'s largest town.'],
                    ['day' => 'Day 12', 'title' => 'Trek from Namche Bazaar to Lukla', 'altitude' => '2,650m', 'duration' => '6-7 hrs', 'meals' => 'Breakfast, Lunch, Dinner', 'description' => 'The final trekking day retraces the route back down to Lukla.'],
                    ['day' => 'Day 13', 'title' => 'Fly back from Lukla to Kathmandu', 'altitude' => '1,345m', 'duration' => '30 min', 'meals' => 'Breakfast, Lunch', 'description' => 'A morning flight back to Kathmandu, with the rest of the day free to relax or explore.'],
                    ['day' => 'Day 14', 'title' => 'Transfer to the airport for your onward journey', 'meals' => 'Breakfast', 'description' => 'A final breakfast, then a transfer to Tribhuvan International Airport for your onward flight.'],
                ],
                'checklist' => [
                    'Broken-in waterproof trekking boots and camp shoes',
                    'Warm base layers, a fleece, and an insulated down jacket',
                    'Waterproof/windproof jacket and trousers',
                    'Thermal gloves and a warm hat',
                    'UV-protection sunglasses and sunscreen',
                    'Trekking poles',
                    'Headlamp with spare batteries',
                    'Sleeping bag rated to -15°C or colder',
                    'Water bottles or a hydration bladder and purification tablets',
                    'First aid kit and personal medication',
                    'Duffel bag for porter-carried luggage and a daypack',
                    'Passport, permits, and travel insurance documents',
                ],
                'know_before_you_go' => '<h3>Best Time to Trek</h3><p>Spring (March-May) and autumn (September-November) offer the most stable weather and the clearest views of the lakes and surrounding peaks. Winter is possible for well-prepared trekkers, though some lodges close; the summer monsoon (June-August) brings rain and unreliable flights.</p><h3>Fitness and Experience</h3><p>The trek is rated strenuous, gaining around 2,500m over 14 days with several long, steep days. Good cardiovascular fitness, built up over 2-3 months beforehand, is recommended, though no technical climbing skills are needed.</p><h3>Altitude and Safety</h3><p>The route includes a dedicated acclimatization day in Namche Bazaar and a gradual altitude gain through the lower valleys. Your guide monitors for symptoms of altitude sickness throughout, and travel insurance covering emergency evacuation is required.</p>',
                'do_not_do' => [
                    'Don\'t skip the acclimatization day in Namche Bazaar, even if you\'re feeling strong',
                    'Don\'t rush the ascent to Gokyo — the altitude gain is significant over a short distance',
                    'Don\'t drink untreated water — use purification tablets or a filter',
                    'Don\'t travel without insurance that covers trekking above 5,000m and emergency evacuation',
                    'Don\'t ignore headaches, nausea, or dizziness — tell your guide immediately',
                ],
                'includes' => [
                    'Airport transfers in Kathmandu',
                    'Domestic flights (Kathmandu/Manthali to Lukla and back)',
                    'Accommodation in Kathmandu before and after the trek',
                    'Accommodation in teahouses during the trek',
                    'Three meals per day during the trek',
                    'English-speaking trekking guide',
                    'Porter service (usually 1 porter for 2 trekkers)',
                    'Necessary trekking permits and national park fees',
                    'First aid kit, trekking map, and duffel bag',
                    'Sleeping bag and down jacket rental',
                    'Salary for all staff and government taxes',
                ],
                'excludes' => [
                    'International airfare to and from Kathmandu',
                    'Nepal entry visa fees',
                    'Travel insurance (including emergency helicopter evacuation)',
                    'Personal expenses (phone calls, internet, laundry, etc.)',
                    'Meals in Kathmandu outside those included',
                    'Alcoholic and cold drinks during the trek',
                    'Hot showers and battery charging at teahouses',
                    'Tips for guides and porters',
                    'Personal trekking gear and clothing',
                ],
                'faq' => [
                    ['question' => 'How difficult is the Gokyo Valley Trek?', 'answer' => 'It\'s rated strenuous, gaining around 2,500m in altitude over 14 days with several long, steep days. Good fitness is recommended, though no technical climbing skills are needed.'],
                    ['question' => 'How does it compare to the Everest Base Camp Trek?', 'answer' => 'It\'s a quieter alternative that reaches a similar altitude over a similar duration, but follows a different valley to the Gokyo Lakes and Gokyo Ri rather than Base Camp itself.'],
                    ['question' => 'When is the best time to go?', 'answer' => 'Spring (March-May) and autumn (September-November) offer the most stable weather and clearest views.'],
                    ['question' => 'Do I need prior trekking experience?', 'answer' => 'No prior experience is required if you have a good level of fitness, though training beforehand is recommended given the trek\'s strenuous rating.'],
                    ['question' => 'What permits are required?', 'answer' => 'The Sagarmatha National Park Entry Permit and the Khumbu Pasang Lhamu Rural Municipality Entrance Permit.'],
                ],
                'reviews' => [
                    ['reviewer_name' => 'Nora Andersen', 'reviewer_role' => 'Trekked October 2025, Denmark', 'quote' => 'So much quieter than the main Everest Base Camp trail. Gokyo Ri at sunrise was jaw-dropping.'],
                    ['reviewer_name' => 'Liam O\'Brien', 'reviewer_role' => 'Trekked April 2025, Ireland', 'quote' => 'The turquoise Gokyo Lakes were unlike anything I\'d seen before. A brilliant alternative to the standard EBC route.'],
                    ['reviewer_name' => 'Ana Silva', 'reviewer_role' => 'Trekked November 2024, Brazil', 'quote' => 'Walking beside the Ngozumpa Glacier was a real highlight. Our guide knew the area incredibly well.'],
                    ['reviewer_name' => 'Erik Johansson', 'reviewer_role' => 'Trekked March 2025, Sweden', 'quote' => 'Fewer crowds than I expected on the main trail, and the views from Gokyo Ri rival anything in the Khumbu.'],
                    ['reviewer_name' => 'Wei Zhang', 'reviewer_role' => 'Trekked September 2025, China', 'quote' => 'A genuinely spiritual place — 19 lakes and total silence at 4,800m. Loved every day of this trek.'],
                    ['reviewer_name' => 'Hannah Fischer', 'reviewer_role' => 'Trekked May 2025, Germany', 'quote' => 'Phortse and Machherma were beautiful, quiet villages. Great choice if you want to avoid the busier EBC trail.'],
                    ['reviewer_name' => 'Carlos Mendoza', 'reviewer_role' => 'Trekked October 2024, Mexico', 'quote' => 'Seeing Everest, Lhotse, Makalu, and Cho Oyu together from Gokyo Ri made the whole 14 days worth it.'],
                    ['reviewer_name' => 'Emma Watson', 'reviewer_role' => 'Trekked April 2025, New Zealand', 'quote' => 'A strenuous but incredibly rewarding trek. Our guide paced the acclimatization perfectly.'],
                    ['reviewer_name' => 'Youssef El-Amin', 'reviewer_role' => 'Trekked November 2025, Egypt', 'quote' => 'Loved the sense of remoteness. Definitely recommend this over the standard EBC trek if you want fewer crowds.'],
                    ['reviewer_name' => 'Ingrid Larsen', 'reviewer_role' => 'Trekked September 2024, Norway', 'quote' => 'The lakes, the glacier, the views from Gokyo Ri — this trek delivers on every level.'],
                ],
            ],
            [
                'title' => 'Everest Heli Tour',
                'slug' => 'everest-heli-tour',
                'image' => 'everest-heli-tour.jpg',
                'duration' => '4 days',
                'difficulty' => TripDifficulty::Easy,
                'altitude' => '5,545m',
                'overview' => '<p>Frequently listed as one of the best holiday destinations in the world, the Everest Heli Tour is a dreamer\'s must-do bucket list item. This tour offers a personalized and uniquely pampered experience, combining immersive cultural encounters with stunning visual delights. The adventure begins with an hour-long helicopter flight through Sagarmatha National Park to reach the Khumbu region, landing at Kala Patthar (5,545m). Here, you can enjoy breathtaking panoramic views of Mt. Everest (8,848m), Cho Oyu (8,201m), Lhotse (8,516m), Nuptse (7,861m), Ama Dablam (6,856m), and other Himalayan peaks. Breakfast is served at one of the world\'s highest star-rated hotels in Syangboche (3,880m), with Everest as your backdrop.</p><p>Everest Trip Planner advises caution due to the sudden altitude gain, as the helicopter tour reaches up to 5,545 metres, potentially causing altitude sickness. While the Everest Heli Tour is available year-round, the best views are typically during the spring and autumn seasons. Winter and monsoon seasons may bring weather challenges such as fog, heavy rain, and snowfall, which can delay or cancel flights to and from Lukla.</p>',
                'highlights' => [
                    'Fly by helicopter over Sagarmatha National Park and land near Everest Base Camp',
                    'Land at Kala Patthar (5,545m) for an up-close view of Mount Everest without days of trekking',
                    'Enjoy breakfast at one of the world\'s highest-altitude hotels in Syangboche, with Everest as the backdrop',
                    'See Everest, Lhotse, Nuptse, Ama Dablam, Cho Oyu, and Makalu in a single flight',
                    'Experience the Khumbu region in a single day — ideal for travelers short on time',
                    'Explore Kathmandu\'s UNESCO World Heritage sites, including Pashupatinath and Boudhanath',
                    'Skip the physical demands and multi-week commitment of trekking to base camp on foot',
                ],
                'detailed_itinerary' => [
                    ['day' => 'Day 1', 'title' => 'Arrival in Kathmandu', 'altitude' => '1,300m/4,264ft', 'meals' => 'Welcome Dinner', 'description' => 'You arrive at Tribhuvan International Airport in Kathmandu, where Everest Trip Planner staff meet you and transfer you to your hotel. Your guide briefs you on the days ahead over a welcome dinner, and you rest overnight at the hotel.'],
                    ['day' => 'Day 2', 'title' => 'Helicopter flight to Everest Base Camp and Kala Patthar', 'altitude' => 'Kala Patthar (5,545m/17,598ft)', 'duration' => '4 hrs', 'meals' => 'Breakfast', 'description' => 'An early start takes you to the domestic airport for a helicopter flight to Lukla, the gateway to the Everest region, then onward to Kala Patthar. En route you\'ll see Everest (8,848m), Ama Dablam (6,856m), Cho Oyu (8,201m), and Makalu (8,475m), before flying over Everest Base Camp itself. The helicopter then continues to Syangboche for breakfast with Everest as a backdrop and time for photos, before returning to Kathmandu by late morning.'],
                    ['day' => 'Day 3', 'title' => 'Kathmandu sightseeing', 'altitude' => '1,300m', 'meals' => 'Breakfast, Lunch', 'description' => 'A guided tour of Kathmandu\'s UNESCO World Heritage sites: Swayambhunath Stupa (the "Monkey Temple"), Kathmandu Durbar Square and the Kumari House, Boudhanath Stupa, and Pashupatinath Temple on the banks of the Bagmati River. The rest of the evening is free to explore before departure the next day.'],
                    ['day' => 'Day 4', 'title' => 'Departure', 'meals' => 'Breakfast', 'description' => 'After breakfast at the hotel, Everest Trip Planner staff assist with your transfer to Tribhuvan International Airport for your onward flight.'],
                ],
                'checklist' => [
                    'Warm layers (fleece and a windproof jacket) — it\'s cold at altitude even briefly',
                    'Sunglasses and sunscreen',
                    'Camera or phone with a full battery for photos at altitude',
                    'Comfortable, warm footwear',
                    'Personal medication and any altitude-sickness tablets your doctor recommends',
                    'Passport and travel insurance documents',
                    'Cash (NPR) for personal expenses in Kathmandu',
                ],
                'know_before_you_go' => '<h3>Best Time to Fly</h3><p>The Everest Heli Tour runs year-round, but spring (March-May) and autumn (September-November) offer the clearest skies and best mountain visibility. Winter and monsoon months can bring fog, snow, or rain that delay or cancel flights.</p><h3>Altitude</h3><p>Although the tour only lasts a few hours, it reaches 5,545m almost instantly by air, which can cause altitude sickness in some travelers. Let your guide know immediately if you feel unwell.</p><h3>Flight Reliability</h3><p>Mountain flights depend on weather, and delays or rescheduling are occasionally necessary for safety. We recommend building at least one buffer day into your Kathmandu itinerary.</p>',
                'do_not_do' => [
                    'Don\'t schedule an onward international flight for the same day as your heli tour, in case of weather delays',
                    'Don\'t skip travel insurance — it should cover high-altitude flights',
                    'Don\'t ignore dizziness or nausea at altitude, even during a short visit',
                    'Don\'t forget warm layers — temperatures at Kala Patthar can be well below freezing',
                ],
                'includes' => [
                    'Return helicopter flight from Kathmandu to the Kala Patthar/Everest Base Camp area',
                    'Breakfast at Syangboche',
                    'Airport transfers in Kathmandu',
                    'English-speaking guide for the Kathmandu sightseeing day',
                    'All applicable permits and park fees',
                ],
                'excludes' => [
                    'International airfare to and from Kathmandu',
                    'Nepal entry visa fees',
                    'Travel insurance',
                    'Accommodation and meals in Kathmandu beyond those listed',
                    'Personal expenses and tips',
                ],
                'faq' => [
                    ['question' => 'How long is the helicopter flight?', 'answer' => 'The flight itself takes roughly an hour each way, with time on the ground at Kala Patthar and Syangboche for photos and breakfast.'],
                    ['question' => 'Is it safe?', 'answer' => 'Yes. We fly with experienced high-altitude helicopter operators, only in weather-permitting conditions.'],
                    ['question' => 'Will I get altitude sickness?', 'answer' => 'It\'s possible, since the flight reaches 5,545m almost instantly rather than gradually. Most travelers feel fine for the short time on the ground, but let your guide know immediately if you feel unwell.'],
                    ['question' => 'Can weather cancel the flight?', 'answer' => 'Yes — mountain weather can delay or reschedule flights for safety. We recommend not booking an onward flight for the same day.'],
                    ['question' => 'Is this suitable for older travelers or those who can\'t trek?', 'answer' => 'Yes, since no walking is required. Let us know about any specific health conditions when booking so we can advise accordingly.'],
                ],
                'reviews' => [
                    ['reviewer_name' => 'Peter Bailey', 'reviewer_role' => 'Flew October 2025, United Kingdom', 'quote' => 'Saw Everest up close without days of trekking. Perfect for our limited time in Nepal.'],
                    ['reviewer_name' => 'Sofia Martinez', 'reviewer_role' => 'Flew April 2025, Spain', 'quote' => 'Breakfast at Syangboche with Everest as the backdrop was surreal. An incredible half-day adventure.'],
                    ['reviewer_name' => 'Ben Coleman', 'reviewer_role' => 'Flew November 2024, USA', 'quote' => 'My parents got to see Everest up close without any trekking at all. Such a thoughtful option for us.'],
                    ['reviewer_name' => 'Aiko Yamamoto', 'reviewer_role' => 'Flew March 2025, Japan', 'quote' => 'Flying over the Khumbu Icefall was breathtaking. Highly recommend if you\'re short on time.'],
                    ['reviewer_name' => 'Karim Aziz', 'reviewer_role' => 'Flew September 2025, Egypt', 'quote' => 'A fantastic taste of the Himalaya in just a few hours. The views of Ama Dablam alone were worth it.'],
                    ['reviewer_name' => 'Julia Novak', 'reviewer_role' => 'Flew May 2025, Czech Republic', 'quote' => 'Perfectly organized — smooth pickup, an amazing flight, and back in Kathmandu by lunch.'],
                    ['reviewer_name' => 'Trevor Adams', 'reviewer_role' => 'Flew October 2024, Canada', 'quote' => 'We didn\'t have time for a full trek but still got to see Everest up close. Exceeded expectations.'],
                    ['reviewer_name' => 'Meera Iyer', 'reviewer_role' => 'Flew April 2025, India', 'quote' => 'A wonderful option for older travelers in our group who couldn\'t trek. Everyone loved it.'],
                    ['reviewer_name' => 'Oliver Grant', 'reviewer_role' => 'Flew November 2025, Australia', 'quote' => 'The Kathmandu sightseeing day was a great addition — Pashupatinath and Boudhanath were fascinating.'],
                    ['reviewer_name' => 'Zara Ahmed', 'reviewer_role' => 'Flew September 2024, Pakistan', 'quote' => 'Short, sweet, and unforgettable. Landing near Everest Base Camp still feels like a dream.'],
                ],
            ],
        ];

        foreach ($trips as $tripData) {
            $image = $this->importMedia($tripData['image'], $tripData['title']);
            unset($tripData['image']);

            $itineraryPage = $tripData['itinerary_page'] ?? true;
            unset($tripData['itinerary_page']);

            $reviews = $tripData['reviews'] ?? [];
            unset($tripData['reviews']);

            $trip = Trip::factory()->published()->create([
                ...$tripData,
                'category_id' => $trekCategory->id,
                'nest_route_under_category' => true,
                'gallery' => [$image->id],
                // No pricing is published on the source site, so don't let the
                // factory's random default price leak into real content.
                'price' => null,
            ]);

            foreach ($reviews as $index => $review) {
                $trip->reviews()->create([
                    ...$review,
                    'rating' => 5,
                    'is_published' => true,
                    'is_featured' => $index < 2,
                ]);
            }

            $mainRoute = $this->routes->sync($trip, $trip->slug, 'trip', $trekCategory->route);

            if ($itineraryPage && filled($trip->detailed_itinerary)) {
                $this->routes->sync($trip, 'itinerary', 'trip-itinerary', $mainRoute, 'itineraryRoute');
            }
        }
    }

    /**
     * @param  array<int, string>  $paragraphs
     * @return array<int, array<string, mixed>>
     */
    private function simpleContent(string $title, array $paragraphs): array
    {
        $blocks = [
            ['type' => 'heading', 'data' => ['text' => $title, 'level' => 'h1']],
        ];

        foreach ($paragraphs as $html) {
            $blocks[] = ['type' => 'text', 'data' => ['content' => $html]];
        }

        $blocks[] = [
            'type' => 'featured_trips',
            'data' => ['heading' => 'Trek the Everest Region', 'count' => 5, 'category_slug' => 'everest-trek'],
        ];

        return $blocks;
    }

    /**
     * Import an image already placed in storage/app/public/seed into the
     * Curator media library, if it hasn't been imported already.
     */
    private function importMedia(string $filename, string $alt): Media
    {
        $path = 'seed/'.$filename;

        return Media::firstOrCreate(
            ['disk' => 'public', 'path' => $path],
            function () use ($path, $filename, $alt) {
                $absolutePath = Storage::disk('public')->path($path);
                [$width, $height] = getimagesize($absolutePath) ?: [null, null];

                return [
                    'directory' => 'seed',
                    'visibility' => 'public',
                    'name' => pathinfo($filename, PATHINFO_FILENAME),
                    'path' => $path,
                    'width' => $width,
                    'height' => $height,
                    'size' => filesize($absolutePath),
                    'type' => 'image',
                    'ext' => pathinfo($filename, PATHINFO_EXTENSION),
                    'alt' => $alt,
                ];
            },
        );
    }
}

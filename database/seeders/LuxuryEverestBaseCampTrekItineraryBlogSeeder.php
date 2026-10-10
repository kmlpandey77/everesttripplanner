<?php

namespace Database\Seeders;

use App\Enums\PageStatus;
use App\Models\Blog;
use App\Models\Page;
use App\Services\RouteGenerator;
use Illuminate\Database\Seeder;

class LuxuryEverestBaseCampTrekItineraryBlogSeeder extends Seeder
{
    use ResolvesTripLinks;

    private const SLUG = 'luxury-everest-base-camp-trek-itinerary';

    public function run(): void
    {
        $blog = Blog::query()->updateOrCreate(['slug' => self::SLUG], [
            'title' => 'The Luxury Everest Base Camp Trek Itinerary: Why 12 Days Works',
            'excerpt' => 'Why is the luxury Everest Base Camp trek itinerary 12 days? A calm, day-by-day look at the pacing, the two acclimatization days and the helicopter home, and why each one is there.',
            'content' => $this->resolveTripLinks($this->content()),
            'status' => PageStatus::Published,
            'published_at' => Blog::query()->where('slug', self::SLUG)->value('published_at') ?? now(),
        ]);

        $blogsHub = Page::query()->where('slug', 'blogs')->firstOrFail();
        app(RouteGenerator::class)->sync($blog, $blog->slug, 'blog', $blogsHub->route);

        $blog->seo()->updateOrCreate([], [
            'meta_title' => 'Luxury Everest Base Camp Trek Itinerary: Why 12 Days Works',
            'meta_description' => 'The luxury Everest Base Camp trek itinerary explained day by day: why the pace is gentle, where you rest, and how the helicopter home saves three days.',
        ]);
    }

    private function content(): string
    {
        return <<<'HTML'
<p>When people look at a luxury Everest Base Camp trek itinerary for the first time, they often ask the same thing: why does it take so long to walk up a valley? The honest answer is that the walking isn't the hard part. The altitude is, and a good itinerary is really a plan for letting your body adjust, one comfortable night at a time. In this guide we walk through the 12 days of our <a href="trip:luxury-everest-base-camp-trek">Luxury Everest Base Camp Trek</a> and explain why each day is there, so you know exactly what you're signing up for and why it works.</p>

<h2>The short answer</h2>
<p>Twelve days gives you nine days in the mountains: eight days of walking up to Everest Base Camp and Kala Patthar, with two gentle acclimatization days built in, then a helicopter flight back to Kathmandu. The slow climb keeps you safe and well at altitude, and the helicopter removes the three or four days most trekkers spend walking back down. You get every highlight of the classic route without the long, repetitive descent.</p>

<h2>The 12 days at a glance</h2>
<p>Here's the whole trip on one page. The "why" column is the part most itineraries leave out.</p>
<table>
<thead>
<tr><th>Day</th><th>Route</th><th>Sleep altitude</th><th>Why this day is here</th></tr>
</thead>
<tbody>
<tr><td>1</td><td>Arrive in Kathmandu</td><td>1,400m</td><td>Rest after the long flight, meet your guide, check gear</td></tr>
<tr><td>2</td><td>Fly to Lukla, walk to Phakding</td><td>2,610m</td><td>A short first walk, downhill from Lukla, to ease you in</td></tr>
<tr><td>3</td><td>Phakding to Namche Bazaar</td><td>3,440m</td><td>The first real climb, into the Khumbu's main town</td></tr>
<tr><td>4</td><td>Acclimatization hike to Hotel Everest View</td><td>3,440m</td><td>Climb high, sleep low: the first adjustment day</td></tr>
<tr><td>5</td><td>Namche to Tengboche</td><td>3,860m</td><td>A modest gain in height, with the monastery as a reward</td></tr>
<tr><td>6</td><td>Tengboche to Dingboche</td><td>4,410m</td><td>Into the high valley, where the trees fall away</td></tr>
<tr><td>7</td><td>Acclimatization hike to Nangkartshang Ridge</td><td>4,410m</td><td>The second adjustment day, and the most important one</td></tr>
<tr><td>8</td><td>Dingboche to Lobuche</td><td>4,940m</td><td>A steady day that sets up base camp</td></tr>
<tr><td>9</td><td>Lobuche to Gorak Shep and Everest Base Camp</td><td>5,164m</td><td>The big day: base camp in the afternoon</td></tr>
<tr><td>10</td><td>Kala Patthar sunrise, helicopter to Kathmandu</td><td>1,400m</td><td>The best view of Everest, then straight back to comfort</td></tr>
<tr><td>11</td><td>Leisure day in Kathmandu</td><td>1,400m</td><td>Spa, sightseeing and a quiet buffer for the weather</td></tr>
<tr><td>12</td><td>Departure</td><td>-</td><td>Fly home rested, not rushed</td></tr>
</tbody>
</table>

<h2>Why the pace is slow on purpose</h2>
<p>Above about 3,000 metres the air holds less oxygen, and your body needs time to adapt. This process is called acclimatization. It happens naturally, but it can't be hurried, and that's the whole reason the trek takes as long as it does.</p>
<p>The itinerary follows two simple rules that mountain doctors recommend. First, once you're above 3,000 metres, the height you sleep at rises gently, usually by around 400 to 550 metres a night. Second, every few days you stop and spend two nights in the same place. On those days you hike higher and then come back down to sleep. Guides call this "climb high, sleep low", and it's one of the best tools there is for feeling well at altitude.</p>
<p>Mild headaches and a slower pace are normal and common up high. Your guide checks your oxygen levels each day with a small finger clip called a pulse oximeter, and oxygen is carried above 4,000 metres just in case. The gentle pace means you'll rarely need either.</p>

<h2>Days 1 to 3: Kathmandu to Namche Bazaar</h2>
<p>Day 1 is deliberately quiet. You arrive, settle into a five-star hotel and meet your Sherpa guide over a welcome dinner. Nobody walks well after a long-haul flight, so this day is for rest.</p>
<p>Day 2 starts with the 35-minute mountain flight to Lukla. In the busiest weeks of spring and autumn, Lukla flights leave from Ramechhap, a few hours' drive from Kathmandu, rather than from Kathmandu itself. We sort out the timing either way. From Lukla the trail actually descends to Phakding, so your first walk is three or four hours of easy riverside path. That's on purpose: you sleep lower than Lukla on your first night, which is a kind start for your body.</p>
<p>Day 3 is the first real climb. You cross the high suspension bridges over the Dudh Koshi, including the famous Hillary Bridge, then climb steadily through pine forest to Namche Bazaar. It's a five-to-six-hour day, and the last hour is uphill. Take it slowly. There's a warm luxury lodge, and usually a very good cake, waiting at the top.</p>

<h2>The two "rest days" that aren't really rest</h2>
<p>Day 4 and Day 7 are the two days people most often ask to skip. Please don't. They are the reason this itinerary works so well.</p>
<h3>Day 4: Namche and Hotel Everest View</h3>
<p>You climb about 440 metres to Hotel Everest View at 3,880m, have coffee on the terrace facing Everest, Lhotse and Ama Dablam, then come back down through Khumjung village to sleep in Namche again. It's a three-to-four-hour outing with a long, relaxed lunch, and the afternoon is free for bakeries and the Sherpa Culture Museum.</p>
<h3>Day 7: Dingboche and Nangkartshang Ridge</h3>
<p>This is the most important day on the whole trek. You walk up Nangkartshang Ridge to around 5,083m, higher than you'll sleep at any point, then return to Dingboche for a second night. It's steep in places but short, and the views of Makalu and Island Peak are among the best of the trip. Bodies that do this day well tend to do base camp well.</p>

<h2>Days 5 to 9: into the high valley</h2>
<p>Day 5 to Tengboche is one of the most beautiful days on the trail. You follow a high path with Ama Dablam ahead, drop to the river, then climb to the <a href="/about-everest/tengboche">monastery at Tengboche</a>. If you arrive in time, the afternoon prayers are worth sitting in on. This is also your last night in a true luxury lodge.</p>
<p>From Day 6, the comfort changes, and we'd rather tell you now than surprise you. There are no genuine luxury lodges above Dingboche. Instead we book the best premium teahouses in Dingboche, Lobuche and Gorak Shep, with the warmest rooms and private bathrooms wherever they exist. The step down is gentle, and with a heated dining room and good food in the evening, most people find it cosy rather than tough. Our post on the <a href="/blogs/best-luxury-lodges-everest-base-camp">best luxury lodges on the trail</a> goes into the detail.</p>
<p>Day 8 to Lobuche is around five hours, passing the climbers' memorials at Thukla. Day 9 is the longest day of the trek, seven to eight hours in total. You walk to Gorak Shep, drop your bags and have lunch, then continue along the edge of the Khumbu Glacier to Everest Base Camp at 5,364m (17,598ft). You walk slowly, rest often and have your guide beside you the whole way. Reaching base camp in the afternoon light, with the Khumbu Icefall right in front of you, is a moment most people never forget.</p>

<h2>Day 10: sunrise, then a helicopter home</h2>
<p>Before dawn, you climb Kala Patthar at 5,545m (18,192ft). It takes about two hours, and it's where you get the full view of Everest's summit, which you can't actually see from base camp. Watching the sun light up the peak is the photograph most people came for. Our <a href="/about-everest/kala-patthar">Kala Patthar page</a> explains why the view is so special.</p>
<p>Then, instead of walking back down for three or four days, you fly. The helicopter leaves from Gorak Shep in the calm morning air and has you in Kathmandu in about an hour, with mountain views the whole way. This is what lets the trip fit into 12 days. You still walk every step up, which is the part that matters for acclimatization and for the experience, but you skip the long, repetitive descent.</p>
<p>Mountain weather sometimes delays morning flights. It's a normal part of travel in the Himalaya. If it happens, your guide manages the plan, and you wait somewhere warm until the skies clear.</p>

<h2>Days 11 and 12: the day you'll be glad you kept</h2>
<p>Day 11 is a free day in Kathmandu. Most people spend it at the hotel spa, wandering the old squares or simply sleeping late, then join a farewell dinner in the evening. It also does a quiet second job: if weather holds the helicopter back by a day, this is the day that absorbs it, so your flight home stays safe.</p>
<p>We'd gently suggest booking your international flight for the evening of Day 12 or later. One more spare day at the end is the cheapest peace of mind you can buy on a Himalayan trip.</p>

<h2>Can the itinerary be shorter or longer?</h2>
<p>Longer, yes, easily. Because the trek is private, we can add a third acclimatization night, a slower day to Lobuche, or an extra night in Kathmandu. Some people like an extra night in Namche, simply because it's such a lovely place to be.</p>
<p>Shorter is a different matter. We don't remove the acclimatization days, because they're what keep the trek safe and enjoyable. If you're short on time, the helicopter is already doing the time-saving for you.</p>

<h2>The bottom line</h2>
<p>A good Everest itinerary isn't about speed. It's about arriving at base camp feeling well, sleeping properly along the way and having the energy to enjoy the view. Twelve days does that: a gentle start, two thoughtful rest days, a full day for base camp, a sunrise on Kala Patthar and a helicopter that turns the long walk out into an hour of scenery.</p>
<p>If this pace sounds right for you, have a look at the full <a href="trip:luxury-everest-base-camp-trek">day-by-day luxury itinerary</a>. If you're still at the "could I really do this?" stage, our <a href="/blogs/everest-base-camp-in-comfort">first-timer's guide to Everest Base Camp in comfort</a> is a gentle next read. When you're ready, <a href="/start-planning">plan your trip with us</a> and we'll shape the dates around you.</p>

<h2>Frequently asked questions</h2>
<h3>Is 12 days enough to acclimatize for Everest Base Camp?</h3>
<p>Yes. The walk up takes eight days with two dedicated acclimatization days, the same as the classic 14-day trek. The helicopter only shortens the way down, which doesn't affect acclimatization.</p>
<h3>How many hours a day will I walk?</h3>
<p>Most days are four to six hours at a gentle pace with plenty of stops. The two acclimatization hikes are three to four hours, and the base camp day is the longest at seven to eight hours.</p>
<h3>What happens if the helicopter can't fly on Day 10?</h3>
<p>You wait in a warm lodge for the weather to clear, which usually happens within a day. The leisure day in Kathmandu is there to absorb that kind of delay.</p>
<h3>Can I add extra days to the itinerary?</h3>
<p>Yes. Because the trek is private, we can add rest days on the trail or extra nights in Kathmandu. Just tell us when you plan your trip.</p>
HTML;
    }
}

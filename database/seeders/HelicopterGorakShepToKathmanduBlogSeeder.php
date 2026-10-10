<?php

namespace Database\Seeders;

use App\Enums\PageStatus;
use App\Models\Blog;
use App\Models\Page;
use App\Services\RouteGenerator;
use Illuminate\Database\Seeder;

class HelicopterGorakShepToKathmanduBlogSeeder extends Seeder
{
    use ResolvesTripLinks;

    private const SLUG = 'helicopter-gorak-shep-to-kathmandu';

    public function run(): void
    {
        $blog = Blog::query()->updateOrCreate(['slug' => self::SLUG], [
            'title' => 'Flying Home from Gorak Shep: The Helicopter Day Explained',
            'excerpt' => 'What is the helicopter from Gorak Shep to Kathmandu really like? A calm walk through the morning, the stop in Lukla, your bags, the views and what happens if the weather says wait.',
            'content' => $this->resolveTripLinks($this->content()),
            'status' => PageStatus::Published,
            'published_at' => Blog::query()->where('slug', self::SLUG)->value('published_at') ?? now(),
        ]);

        $blogsHub = Page::query()->where('slug', 'blogs')->firstOrFail();
        app(RouteGenerator::class)->sync($blog, $blog->slug, 'blog', $blogsHub->route);

        $blog->seo()->updateOrCreate([], [
            'meta_title' => 'Helicopter from Gorak Shep to Kathmandu: What to Expect',
            'meta_description' => 'The helicopter from Gorak Shep to Kathmandu, explained: the morning plan, the Lukla stop, bags, views and weather delays, so the day feels easy.',
        ]);
    }

    private function content(): string
    {
        return <<<'HTML'
<p>The helicopter from Gorak Shep to Kathmandu is the moment many trekkers look forward to most, and the one they know least about. Where does it land? What happens to my bags? What if the weather turns? These are fair questions, and the answers are reassuring. On our <a href="trip:luxury-everest-base-camp-trek">Luxury Everest Base Camp Trek</a>, the flight comes on Day 10, straight after sunrise on Kala Patthar, and it turns three or four days of walking down into a little over an hour in the air. This guide walks you through that morning, step by step, so you know exactly what to expect.</p>

<h2>The short answer</h2>
<p>After the Kala Patthar sunrise, you have breakfast in Gorak Shep and board a helicopter in the calm morning air. It flies down the Khumbu valley to Lukla, usually stops there briefly to refuel and rearrange passengers, then continues to Kathmandu. Expect about an hour to an hour and a half from take-off to landing, with your guide handling every detail on the ground.</p>

<h2>Your helicopter morning at a glance</h2>
<p>Times move a little with the season and the weather, but a typical Day 10 looks like this.</p>
<table>
<thead>
<tr><th>Time</th><th>What happens</th></tr>
</thead>
<tbody>
<tr><td>4:30am</td><td>A hot drink in the lodge, then a slow walk up Kala Patthar by headtorch</td></tr>
<tr><td>6:00am</td><td>Sunrise over Everest from 5,545m (18,192ft)</td></tr>
<tr><td>7:30am</td><td>Back in Gorak Shep for a warm breakfast, bags packed and weighed</td></tr>
<tr><td>8:00 to 10:00am</td><td>The usual flying window, while the air is still calm</td></tr>
<tr><td>About 20 minutes later</td><td>Touch down in Lukla for fuel and passenger changes</td></tr>
<tr><td>Late morning</td><td>Land in Kathmandu and transfer to your five-star hotel</td></tr>
</tbody>
</table>
<p>By lunchtime, most people are in a hot shower they've been dreaming about for a week. The afternoon is yours for the spa, a long nap or a celebration drink.</p>

<h2>Why mornings, and why the stop in Lukla?</h2>
<p>Helicopters fly best in cool, still air, and in the Himalaya that means the morning. By late morning the wind usually builds in the high valleys, so pilots like to finish the high-altitude part of the day early. That's why the sunrise on Kala Patthar and the flight fit together so neatly.</p>
<p>The stop in Lukla is normal, and it's for a simple reason. Air at 5,000 metres is thin, so a helicopter can lift fewer people up there than it can lower down. Operators often fly small loads from Gorak Shep to Lukla, then combine passengers there for the longer leg to Kathmandu, which is also where the aircraft refuels. You might change helicopters, or you might simply stretch your legs on the helipad for a few minutes. Either way, your guide or our team in Lukla takes care of it. If you'd like to know more about the town itself, our <a href="/about-everest/lukla">Lukla guide</a> has the background.</p>

<h2>What the flight feels like</h2>
<p>It's quick, loud and wonderful. You wear a headset or ear protection, the pilot gives a short safety briefing, and within seconds you're lifting away from the moraine, the ridge of rock and gravel left behind by the glacier.</p>
<ul>
<li><strong>The views:</strong> you fly down the valley you just walked up, past Lobuche, Dingboche, Pangboche and Tengboche, with Ama Dablam close beside you. People often spot their own lodges from the air.</li>
<li><strong>Seats:</strong> the pilot decides where everyone sits, based on weight and balance. Every seat has a window, and the views are good on both sides.</li>
<li><strong>Your ears:</strong> you drop several thousand metres quickly, so your ears may pop. Swallowing, yawning or chewing a sweet helps.</li>
<li><strong>How you'll feel:</strong> most people feel better within minutes. Coming down is the best cure for any lingering altitude headache, so you often land feeling lighter than you did at breakfast.</li>
</ul>

<h2>Your bags and the weight limit</h2>
<p>Because the helicopter's lifting power depends on altitude, weight matters more here than on any other flight you'll take. It's easy to get right, and your guide will help.</p>
<ul>
<li><strong>Your duffel</strong> is the same bag your porter carried up the trail. Pack it the night before so it's ready to weigh in the morning.</li>
<li><strong>Your daypack</strong> stays with you on your lap or at your feet. Keep your camera, phone, passport and a warm layer in it.</li>
<li><strong>The limit</strong> is set by the operator on the day and depends on the aircraft and the conditions. Your guide confirms it before you leave Lobuche, and it's normally generous enough for the bag you trekked with.</li>
<li><strong>Pack light from the start</strong>, and leave anything you won't need on the trail at your Kathmandu hotel. Souvenirs from Namche are best kept small.</li>
</ul>
<p>Our post on <a href="/blogs/luxury-everest-trek-whats-included">what's included in a luxury Everest trek</a> explains which gear we lend you and how it's returned at the end.</p>

<h2>When the weather says wait</h2>
<p>Sometimes cloud sits in the valley, or the wind picks up early, and the pilot decides not to fly. This is the honest part of mountain travel, and it's also completely normal. Good pilots wait. That's exactly what you want them to do.</p>
<p>If it happens, you stay somewhere warm, often with a hot drink and a book, while your guide keeps in touch with the operator. Most delays are a few hours, sometimes a day. That's one reason the Day 11 leisure day in Kathmandu is in the plan: it quietly absorbs a delay without touching your flight home. We also suggest booking your international flight for the evening of Day 12 or later, for extra peace of mind. Our <a href="/blogs/luxury-everest-base-camp-trek-itinerary">12-day itinerary explained</a> shows how the whole schedule is built around this.</p>

<h2>Shared or private helicopter?</h2>
<p>The helicopter on our trip is weight-shared as standard. You fly with other trekkers heading the same way, which keeps the cost sensible and makes good use of every flight. It's comfortable, well organised and the views are just as good.</p>
<p>If you'd prefer the aircraft to yourselves, a private charter can be arranged. It suits couples, families and photographers who want a little more space or a more flexible departure time. Just tell us when you plan your trip.</p>

<h2>The bottom line</h2>
<p>The helicopter day is the gentlest end to a big adventure. You stand on Kala Patthar at sunrise, eat breakfast in Gorak Shep and, a couple of hours later, you're back in Kathmandu with a week of mountain memories and a bed with fresh sheets. Your guide handles the timing, the bags and the weather, so all you need to do is look out of the window.</p>
<p>To see how this fits into the whole trip, have a look at the <a href="trip:luxury-everest-base-camp-trek">day-by-day luxury itinerary</a>. When you're ready, <a href="/start-planning">plan your trip with us</a> and we'll arrange the flight, shared or private, around your dates.</p>

<h2>Frequently asked questions</h2>
<h3>How long is the helicopter flight from Gorak Shep to Kathmandu?</h3>
<p>About an hour to an hour and a half in total, including a short stop in Lukla. Weather and the day's routing can change it a little.</p>
<h3>Is the helicopter flight safe?</h3>
<p>Yes. Flights run with experienced high-altitude pilots in the calm morning air, and they only go when the pilot is happy with the conditions. A delay is a sign the system is working.</p>
<h3>Can I fly straight from Everest Base Camp?</h3>
<p>No. The flight leaves from Gorak Shep, a short walk from base camp, after your Kala Patthar sunrise. That way you get the best view of Everest before you fly.</p>
<h3>What if I feel unwell before Day 10?</h3>
<p>Tell your guide straight away. Coming down is the best treatment for altitude sickness, and your guide will arrange a safe descent, by helicopter if that's what's needed.</p>
HTML;
    }
}

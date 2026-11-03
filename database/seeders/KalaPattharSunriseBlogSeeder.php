<?php

namespace Database\Seeders;

use App\Enums\PageStatus;
use App\Models\Blog;
use App\Models\Page;
use App\Services\RouteGenerator;
use Illuminate\Database\Seeder;

class KalaPattharSunriseBlogSeeder extends Seeder
{
    use ResolvesTripLinks;

    private const SLUG = 'kala-patthar-sunrise';

    public function run(): void
    {
        $blog = Blog::query()->updateOrCreate(['slug' => self::SLUG], [
            'title' => 'Kala Patthar Sunrise: A Gentle Guide to the Best View of Everest',
            'excerpt' => 'What the Kala Patthar sunrise hike from Gorak Shep is really like: the altitude, what time you start, how long it takes, what to wear and what you see from the top.',
            'content' => $this->resolveTripLinks($this->content()),
            'status' => PageStatus::Published,
            'published_at' => Blog::query()->where('slug', self::SLUG)->value('published_at') ?? now(),
        ]);

        $blogsHub = Page::query()->where('slug', 'blogs')->firstOrFail();
        app(RouteGenerator::class)->sync($blog, $blog->slug, 'blog', $blogsHub->route);

        $blog->seo()->updateOrCreate([], [
            'meta_title' => 'Kala Patthar Sunrise: Timing, Altitude & What to Expect',
            'meta_description' => 'Kala Patthar sunrise from Gorak Shep: start time, the 5,545m altitude, how long the hike takes, what to wear and why it is the best view of Everest.',
        ]);
    }

    private function content(): string
    {
        return <<<'HTML'
<p>The Kala Patthar sunrise is the morning most Everest trekkers remember best, and it's natural to wonder whether you'll manage it. You start in the dark, at the highest point of the whole trek, in the cold. Put like that, it sounds like a lot. In practice it's a slow, steady walk up a sandy hill with a guide beside you, and by the time the sky turns gold you're standing in front of Everest. On our <a href="trip:luxury-everest-base-camp-trek">Luxury Everest Base Camp Trek</a> this is the morning of Day 10, and it's the last thing you do on foot before the helicopter takes you home. Below we cover the altitude, the timing, what to wear and what you'll see, so the morning holds no surprises.</p>

<h2>The short answer</h2>
<p>Kala Patthar is a viewpoint at 5,545m (18,192ft) above Gorak Shep, about 380m higher than the village. Most trekkers leave around 4:00 to 4:45am with head torches, take 1.5 to 2.5 hours to walk up, and reach the top just before sunrise. It's the best place on the trek to see the summit of Everest, which you can't see from base camp itself.</p>

<h2>What and where is Kala Patthar?</h2>
<p>Kala Patthar means "black rock" in Nepali. It's a dark, rounded shoulder on the south ridge of Pumori, rising straight behind Gorak Shep (about 5,164m), the last settlement before Everest Base Camp. You can read more about the place itself on our <a href="/about-everest/kala-patthar">Kala Patthar page</a>.</p>
<p>Everyone climbs it for the view. From Everest Base Camp (5,364m), the summit of Everest is hidden behind its own lower slopes and the wall of Nuptse. From Kala Patthar you're far enough back, and high enough, to see the whole summit pyramid, the Khumbu Icefall and the glacier you walked beside the day before.</p>
<table>
<thead>
<tr><th>Fact</th><th>Kala Patthar</th></tr>
</thead>
<tbody>
<tr><td>Altitude</td><td>5,545m (18,192ft)</td></tr>
<tr><td>Starting point</td><td>Gorak Shep, about 5,164m</td></tr>
<tr><td>Height gained</td><td>About 380m</td></tr>
<tr><td>Distance</td><td>Roughly 2km each way</td></tr>
<tr><td>Time up</td><td>1.5 to 2.5 hours</td></tr>
<tr><td>Time down</td><td>About 1 hour</td></tr>
<tr><td>Best time</td><td>Sunrise, in spring (March to May) or autumn (September to November)</td></tr>
</tbody>
</table>

<h2>What is the sunrise morning like?</h2>
<h3>The early start</h3>
<p>Your guide will wake you with a hot drink at around 4am. Sunrise is roughly 5:30am in late spring and closer to 6:15am in late autumn, so the exact time moves with your dates. You'll have a few biscuits or a cereal bar, wrap up warmly and step out under a sky full of stars. Breakfast proper waits until you're back.</p>
<h3>The walk up</h3>
<p>The path starts right behind the lodges. It crosses a flat, sandy lake bed, then climbs in steady zigzags up the hillside. The trail is clear and well used, and there's no scrambling or technical climbing.</p>
<p>The challenge is the thin air. At this height every step takes more breath than it would at home, and that's true for everyone, including very fit trekkers. The answer is to go slowly. Your guide will set a gentle pace of small steps with short stops to breathe, and that pace is what gets people to the top comfortably. Other trekkers' head torches bob above and below you on the path.</p>
<h3>Reaching the top</h3>
<p>The main viewpoint has a cluster of prayer flags and a few big rocks to sit behind out of the wind. A little higher, the ridge carries on to a rockier high point. You don't need to go further than the prayer flags: the view of Everest from there is just as good, and most trekkers stop at that spot.</p>

<h2>What will you see?</h2>
<p>As the sky lightens, the shapes of the mountains come out of the dark one by one. Everest stands in the east, with its dark summit pyramid against a sky that changes from deep blue to orange. To its right is the long wall of Nuptse, and below you lies the Khumbu Glacier, with the Icefall tumbling down from the Western Cwm. In spring you can often pick out the yellow tents of the expedition camp at its foot.</p>
<p>Turn around and Pumori rises almost directly above you, catching the first warm light. Further round you'll see Changtse, Lhotse peeking over the ridge and Ama Dablam far to the south. A good guide will name every peak for you, and it's lovely to hear the stories behind them.</p>
<h3>Sunrise or sunset?</h3>
<p>Both are beautiful, and you'll find people who prefer each. At sunrise the sun comes up behind Everest, so the mountain appears as a dark silhouette with glowing edges. At sunset the light falls on Everest's face and turns it gold.</p>
<p>We choose sunrise for one practical reason: mornings in the Khumbu are usually clear and still, and cloud often builds through the afternoon. A sunrise climb also fits the itinerary well, because you're back at Gorak Shep for breakfast in time for the calm morning air the helicopter flies best in.</p>

<h2>How cold is it, and what should you wear?</h2>
<p>This is the coldest hour of the whole trek. Before dawn in autumn it's often around -10°C to -15°C at the top, and colder in winter or when the wind picks up. It warms quickly once the sun is up, but you'll want proper layers while you wait for it.</p>
<p>For the climb, we suggest:</p>
<ul>
<li>A thermal base layer, a warm fleece and your down jacket on top.</li>
<li>Thermal leggings under your trekking trousers, plus windproof over-trousers if you have them.</li>
<li>A warm hat, a buff or neck gaiter, and thin gloves inside warm mittens.</li>
<li>Thick socks and your usual trekking boots.</li>
<li>A head torch with fresh batteries, because the cold drains them quickly.</li>
<li>A small flask of hot water or tea, and a snack for the top.</li>
<li>Your phone or camera in an inside pocket, close to your body, so the battery lasts.</li>
</ul>
<p>Your guide and porter carry spare layers and extra water, so you only need a light daypack.</p>

<h2>Is it hard, and what if you don't feel up to it?</h2>
<p>By the time you reach Gorak Shep you've spent eight days walking up the valley, with acclimatization days in Namche Bazaar and Dingboche along the way. Your body has had time to adjust, and on our luxury trek your guide checks your oxygen levels with a pulse oximeter every day, so you'll know how you're doing before you start.</p>
<p>It's normal to feel tired, breathless or to have a mild headache at this height. Walk slowly, sip water, and tell your guide straight away if a headache gets worse or you feel unwell. We carry supplemental oxygen above 4,000m, and turning back is always fine. The view from halfway up is already wonderful.</p>
<p>Some trekkers decide to rest in the lodge instead, especially if they've already enjoyed a clear evening at base camp. That's a perfectly good choice too. If you'd like to see the mountains from the air without the climb at all, the <a href="trip:everest-heli-tour">Everest Heli Tour</a> lands at Kala Patthar by helicopter.</p>

<h2>What happens after the sunrise?</h2>
<p>Once you've taken your photos, you walk back down to Gorak Shep in about an hour. Going down feels much easier, and the morning sun on the peaks makes it a lovely walk. A hot breakfast is waiting at the lodge.</p>
<p>On many Everest itineraries, this is followed by three or four days of walking back to Lukla. On ours, a helicopter picks you up from Gorak Shep and has you in Kathmandu in time for lunch and a hot shower at your five-star hotel. Our guide to the <a href="/blogs/helicopter-gorak-shep-to-kathmandu">helicopter day from Gorak Shep</a> explains how that part works, and the <a href="/blogs/luxury-everest-base-camp-trek-itinerary">12-day itinerary</a> shows how the whole trip builds up to this morning.</p>

<h2>The bottom line</h2>
<p>The Kala Patthar sunrise is an early, cold, slow walk that most trekkers manage well, and it gives you the best view of Everest on the whole trek. With warm layers, a gentle pace and a guide who knows the hill, you watch the light reach Everest and are back in Gorak Shep for breakfast before the helicopter.</p>
<p>See how it fits into the <a href="trip:luxury-everest-base-camp-trek">Luxury Everest Base Camp Trek</a> day by day. When you're ready, <a href="/start-planning">plan your trip with us</a> and we'll help you pick the dates with the best chance of a clear sunrise.</p>

<h2>Frequently asked questions</h2>
<h3>How high is Kala Patthar?</h3>
<p>The main viewpoint on Kala Patthar is 5,545m (18,192ft), about 380m above Gorak Shep and 180m higher than Everest Base Camp. It's the highest point reached on the classic Everest Base Camp trek.</p>
<h3>How long does it take to walk from Gorak Shep to Kala Patthar?</h3>
<p>Most trekkers take 1.5 to 2.5 hours to walk up and about an hour to walk down. Your guide will set a slow, steady pace to suit you.</p>
<h3>What time should I leave Gorak Shep for sunrise?</h3>
<p>Plan to leave around 4:00 to 4:45am, depending on the season. Your guide will work back from the sunrise time on your dates.</p>
<h3>Can you see Everest from Everest Base Camp?</h3>
<p>Not the summit. From base camp, Everest's top is hidden behind its own lower slopes and Nuptse. Kala Patthar is where you get the full, close view of the summit.</p>
HTML;
    }
}

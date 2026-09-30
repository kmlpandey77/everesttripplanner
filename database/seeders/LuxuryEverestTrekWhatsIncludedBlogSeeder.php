<?php

namespace Database\Seeders;

use App\Enums\PageStatus;
use App\Models\Blog;
use App\Models\Page;
use App\Services\RouteGenerator;
use Illuminate\Database\Seeder;

class LuxuryEverestTrekWhatsIncludedBlogSeeder extends Seeder
{
    use ResolvesTripLinks;

    private const SLUG = 'luxury-everest-trek-whats-included';

    public function run(): void
    {
        $blog = Blog::query()->updateOrCreate(['slug' => self::SLUG], [
            'title' => "What's Included in a Luxury Everest Base Camp Trek (and What Isn't)",
            'excerpt' => 'Hotels, flights, the helicopter, meals, your guide and gear: a clear, friendly look at what a luxury Everest trek includes, and the few things you pay for yourself.',
            'content' => $this->resolveTripLinks($this->content()),
            'status' => PageStatus::Published,
            'published_at' => Blog::query()->where('slug', self::SLUG)->value('published_at') ?? now(),
        ]);

        $blogsHub = Page::query()->where('slug', 'blogs')->firstOrFail();
        app(RouteGenerator::class)->sync($blog, $blog->slug, 'blog', $blogsHub->route);

        $blog->seo()->updateOrCreate([], [
            'meta_title' => "Luxury Everest Trek Inclusions: What's Included",
            'meta_description' => 'Luxury Everest trek inclusions explained: hotels, flights, helicopter, meals, guide, permits and gear, plus the few extras to budget for yourself.',
        ]);
    }

    private function content(): string
    {
        return <<<'HTML'
<p>Luxury Everest trek inclusions can look like a long list of small print, and when you're comparing trips it's hard to know what really matters. The good news is that a well-built luxury package covers almost everything, so once you've booked there's very little left to organise. This post walks through what's included in our <a href="trip:luxury-everest-base-camp-trek">Luxury Everest Base Camp Trek</a>, what isn't, and what to check when you compare trips.</p>

<h2>The short answer</h2>
<p>A luxury Everest Base Camp trek usually includes your Kathmandu hotel, the flight to Lukla, the helicopter home, every lodge night and meal on the trail, a private Sherpa guide with porters, your permits, and safety kit such as oxygen. What you pay for yourself is mostly small and personal: international flights, your visa, insurance, tips and a few extras.</p>

<h2>In Kathmandu: hotels, transfers and dinners</h2>
<p>Your trip starts the moment you land. A private vehicle and one of our team meet you at Tribhuvan International Airport and take you straight to your hotel. No taxi queues, no haggling.</p>
<p>Three nights in a five-star hotel are included, with breakfast each morning. One night is on arrival. The other two come after the trek, when you fly back and then have a free leisure day to rest, wander or enjoy the spa.</p>
<p>A welcome dinner on the first evening, where you meet your guide and go through your gear, and a farewell dinner at the end are both included too. Other lunches and dinners in Kathmandu are up to you, which most people enjoy. The city has lovely restaurants.</p>

<h2>Getting in and out of the mountains</h2>
<p>This is where much of the cost of a luxury trip goes, and it's all taken care of.</p>
<ul>
<li><strong>The flight to Lukla.</strong> A short, scenic mountain flight to Lukla (2,860m), where the trail begins. In the busiest months, flights often leave from Ramechhap, a few hours' drive from Kathmandu. We arrange that too. A private helicopter to Lukla is available as an upgrade.</li>
<li><strong>The helicopter home.</strong> After sunrise on Kala Patthar, you fly from Gorak Shep back to Kathmandu. This saves three days of walking back down. The flight is shared by weight with other trekkers, and a private charter is available if you'd like the helicopter to yourself.</li>
</ul>
<p>Mountain flights depend on the weather, even in the best seasons. If a flight waits, your guide handles the new plan. That's why we always suggest keeping one spare day at the end of your trip.</p>

<h2>Where you sleep and what you eat</h2>
<p>Every night on the trail and every meal on the trek is included. Here's how the nights look.</p>
<table>
<thead>
<tr><th>Days</th><th>Where</th><th>Accommodation</th><th>Meals included</th></tr>
</thead>
<tbody>
<tr><td>Day 1</td><td>Kathmandu</td><td>Five-star hotel</td><td>Welcome dinner</td></tr>
<tr><td>Days 2-5</td><td>Phakding, Namche Bazaar, Tengboche</td><td>Luxury lodges</td><td>Breakfast, lunch and dinner</td></tr>
<tr><td>Days 6-8</td><td>Dingboche, Lobuche</td><td>Premium teahouses</td><td>Breakfast, lunch and dinner</td></tr>
<tr><td>Day 9</td><td>Gorak Shep</td><td>Best available teahouse</td><td>Breakfast, lunch and dinner</td></tr>
<tr><td>Days 10-11</td><td>Kathmandu</td><td>Five-star hotel</td><td>Breakfast, plus farewell dinner</td></tr>
<tr><td>Day 12</td><td>Departure</td><td>-</td><td>Breakfast</td></tr>
</tbody>
</table>
<p>In the lower valley you'll sleep in luxury lodges with heated rooms and attached bathrooms. Higher up, true luxury lodges don't exist, so we book the best premium rooms available. Comfort steps down gently rather than suddenly, and we'd rather tell you that now than surprise you. Our guide to the <a href="/blogs/best-luxury-lodges-everest-base-camp">best luxury lodges on the Everest trail</a> goes into the rooms in more detail.</p>
<p>Unlimited tea and coffee and boiled drinking water are included all the way up. You won't need to buy bottled water, which is better for the mountains too.</p>

<h2>Your guide, porters and permits</h2>
<p>You'll walk with a private, licensed, English-speaking Sherpa guide. Since April 2023, a licensed guide has been required for trekkers in the Everest region, so this isn't just a comfort. Your guide's salary, insurance, equipment, meals and lodging are all covered in the price.</p>
<p>Porters carry your main bag, with one porter for every two trekkers and up to 20 kg shared between you. You carry only a light daypack with water, a layer and your camera.</p>
<p>Both permits are arranged for you: the <a href="/about-everest/sagarmatha-national-park">Sagarmatha National Park</a> entry permit and the Khumbu Pasang Lhamu Rural Municipality permit. Government taxes and service charges are included as well, so there are no surprise fees on the trail.</p>

<h2>Safety and gear</h2>
<p>Altitude is the part most people worry about, and it's the part we plan for most carefully. Feeling a little breathless or sleeping lightly up high is completely normal. Your guide checks how you're doing every day and carries what's needed if you ever need more help.</p>
<ul>
<li>A comprehensive medical kit</li>
<li>A pulse oximeter for daily oxygen checks</li>
<li>Supplemental oxygen, carried above 4,000m</li>
<li>A four-season sleeping bag, down jacket and duffel bag on loan, returned after the trek</li>
<li>A trek completion certificate to take home</li>
</ul>
<p>Having the sleeping bag and down jacket provided means less to buy and less to fly with, which many trekkers appreciate.</p>

<h2>What isn't included</h2>
<p>A few costs sit outside the package. None of them are large, and it helps to know about them early.</p>
<ul>
<li><strong>International flights</strong> to and from Kathmandu.</li>
<li><strong>Your Nepal visa,</strong> which you can get on arrival at the airport.</li>
<li><strong>Travel insurance.</strong> This is required, and it must cover helicopter evacuation and trekking up to 6,000m. We check your policy before departure.</li>
<li><strong>Tips</strong> for your guide, porters and drivers. Tipping is customary in Nepal, but it's always your choice.</li>
<li><strong>Personal extras,</strong> such as laundry, drinks other than tea and coffee, and spa treatments or sightseeing fees in Kathmandu.</li>
<li><strong>Wi-Fi, charging and hot showers</strong> at the simpler teahouses higher up. In the luxury lodges these are usually part of your stay.</li>
<li><strong>Extra hotel nights</strong> if you come back early or a flight is delayed, and costs from events outside anyone's control, such as weather. This is what your insurance is for.</li>
</ul>
<p>For a rough idea of what these extras add up to, our <a href="/blogs/luxury-everest-base-camp-trek-cost">luxury Everest trek cost breakdown</a> gives typical market ranges.</p>

<h2>What to check when you compare trips</h2>
<p>Two trips at a similar price can include very different things. These are the questions worth asking any operator:</p>
<ul>
<li>Is the helicopter home included, and is it shared or private?</li>
<li>How many nights are in true luxury lodges, and where?</li>
<li>Is the guide private, and is each porter carrying a fair weight?</li>
<li>Are oxygen and a pulse oximeter carried, and from what height?</li>
<li>Are all meals on the trek included, or only breakfast?</li>
<li>Are there two acclimatization days, in Namche and Dingboche?</li>
</ul>
<p>If an answer is vague, ask again. A good operator is happy to explain.</p>

<h2>The bottom line</h2>
<p>A luxury Everest trip should feel simple once it's booked, and ours is built that way. Hotels, flights, the helicopter, lodges, meals, your guide, porters, permits and safety kit are all included, and what's left is mostly personal. You can spend your energy on the walk and the views, not on logistics.</p>
<p>See the full <a href="trip:luxury-everest-base-camp-trek">day-by-day luxury itinerary</a> to see how it all fits together, then <a href="/start-planning">plan your trip with us</a> and we'll send you a clear quote for your dates.</p>

<h2>Frequently asked questions</h2>
<h3>Is the helicopter flight included?</h3>
<p>Yes. The helicopter from Gorak Shep to Kathmandu is part of the trip, shared by weight with other trekkers. A private charter is available as an upgrade.</p>
<h3>Are meals included on a luxury Everest trek?</h3>
<p>All meals on the trek are included, with unlimited tea, coffee and boiled drinking water. In Kathmandu, breakfast and the welcome and farewell dinners are included.</p>
<h3>Do I need to bring a sleeping bag and down jacket?</h3>
<p>No. A four-season sleeping bag, a down jacket and a duffel bag are provided on loan and returned after the trek.</p>
<h3>Is travel insurance included?</h3>
<p>No, you arrange it yourself. It must cover helicopter evacuation and trekking up to 6,000m, and we check your policy before departure.</p>
HTML;
    }
}

---
name: write-blog
description: Write an SEO blog post for the Everest Trip Planner site (a luxury Everest Base Camp trek operator) and ship it as an idempotent Laravel seeder that creates the Blog record, its /blogs/{slug} route and SEO meta. Use whenever the user asks for a blog, blog post, article, guide or "post about X" for this site, gives a blog idea or topic (e.g. "write #5", "luxury lodges on EBC blog"), or says /write-blog — even if they don't mention seeders or the database.
---

# Write Blog

Every post exists to rank for a search people make before booking, and then send them to the **Luxury Everest Base Camp Trek** (`/luxury-everest-base-camp-trek`). That trip is the site's main product. Keep that goal in mind when making every decision below.

## 1. Pin down the post

From the user's topic, decide:
- **Primary keyword**: the exact phrase a traveller would search, e.g. "luxury lodges everest base camp".
- **Search intent**: comparing, costing, can-I-do-it, planning or inspiration. The intent decides the structure. A cost post needs a breakdown table. A "can I do it" post needs reassurance plus concrete preparation.
- **Slug**: the keyword, lowercase and hyphenated, 3–6 words, with no year unless the post is about that year.

Check the existing posts so you don't write a duplicate and so you can link between them:

```bash
php artisan tinker --execute 'App\Models\Blog::with("route")->get()->each(fn($b) => print($b->title." -> /".$b->route?->full_path."\n"));'
```

If the topic mostly overlaps an existing post, tell the user and offer to update that post instead.

## 2. Get the facts right

Readers are spending $4k+ and check the details, so a wrong altitude or a made-up lodge costs trust. Use the figures below, which match the trip page, and use WebSearch for anything else (lodge names, permit fees, flight rules, prices).

- Everest Base Camp 5,364m (17,598ft) · Kala Patthar 5,545m (18,192ft) · Namche Bazaar 3,440m · Tengboche 3,860m · Dingboche 4,410m · Lobuche 4,940m · Gorak Shep ~5,164m · Lukla 2,860m · Kathmandu ~1,400m
- The luxury trip: 12 days, luxury lodges in Phakding/Namche/Tengboche, the best premium teahouses above Dingboche (no true luxury lodges exist up there, so say so honestly), private Sherpa guide, acclimatization days in Namche and Dingboche, oximeter checks, oxygen carried above 4,000m, helicopter from Gorak Shep after the Kala Patthar sunrise, five-star Kathmandu hotel with a leisure day.
- Best seasons: March–May and September–November.

The site has no prices yet (`price` is null). Don't state a price for our trip. Give market ranges and label them clearly as typical market prices, or leave them out.

## 3. Write it

**Voice: simple, positive, soft.** Write like a calm, experienced guide talking to one person over tea. The reader can afford comfort but is probably a little nervous. They should finish the post feeling "I can do this", not "this sounds scary". The tone reference is https://everestbasecamptrails.com/blog/ebc-trek-difficulty. Match its feel, not its words.

- **Simple:** use plain words and short paragraphs of 2–4 sentences. Mix short sentences with longer ones ("Altitude is the whole story."). Explain any term the first time you use it (acclimatization, moraine). Write "you" and "we" throughout.
- **Positive:** open each section with what goes right, then the honest detail. Say "a hard walk, not a hard climb" rather than "gruelling and dangerous".
- **Soft with scary topics:** handle altitude, cold, flights and difficulty in three steps. Name the issue honestly, say it's normal and common, then give the practical fix. For example: "Walk slowly, drink plenty of water, and tell your guide early if you get a headache." Never frighten the reader, and never pretend a risk doesn't exist.
- **Concrete over hype:** "heated rooms with attached bathrooms" beats "unforgettable luxury". Leave out exclamation marks and stacked superlatives.
- **Headings:** mix question headings ("What's easier than people expect?") with plain statement headings ("The three hardest days"). Start with a "The short answer" section and end with a "The bottom line" section before the FAQ.
- **Calls to action:** keep them gentle and in steps. Start with a soft one ("see the day-by-day itinerary"), then a direct one ("plan your trip with us").
- **Spelling:** British/Nepal spelling, to match the existing posts ("specialise", "colour", "metres").

**Length:** 1,200–1,800 words. It should be complete enough to answer the search without padding.

**Structure** (as an HTML string, the way `Blog::content` stores it):
- Opening `<p>`: start from the question the reader has in mind, then give an honest, reassuring reframe, then a one-line preview of what the post covers. Put the primary keyword in the first sentence.
- `<h2>The short answer</h2>`: 2–3 sentences that answer the headline directly. AI answer engines and featured snippets quote this section.
- 4–7 `<h2>` sections, with `<h3>` inside them only where needed. The page already renders the title as `<h1>`, so never use `<h1>` in the content.
- At least one scannable element where it fits: a `<table>` for comparisons or costs, or `<ul>` for lists.
- `<h2>The bottom line</h2>` (before the FAQ): a warm summary, a confident closing line, and a link to the luxury trip with natural anchor text, plus `/start-planning` when a planning call to action fits.
- A short "Frequently asked questions" `<h2>` with 3–4 `<h3>` questions, each answered in 1–3 sentences.

**Links** (use relative URLs so they work locally and in production):
- **Trips:** always link as `href="trip:{slug}"`, never as a path. Trip URLs differ between environments: live uses `/luxury-everest-base-camp-trek`, while local uses `/everest-trek/luxury-everest-base-camp-trek`. The seeder swaps in the real path when it runs.
- The luxury trip: 2–3 times, the first within the first 200 words.
- 1–2 relevant `/about-everest/...` pages: `everest-khumbu-region`, `sagarmatha-national-park`, `sherpas`, `lukla`, `namchebazar`, `tengboche`, `everest-base-camp`, `kala-patthar`, `buddhist-culture`.
- Related blog posts from step 1.
- Other trips (`trip:everest-base-camp-trek`, `trip:everest-base-camp-trek-with-helicopter-return`, `trip:gokyo-valley-trek`, `trip:everest-heli-tour`) only when the comparison is the point of the post.

**Meta:**
- Meta title of 60 characters or fewer, starting with the keyword.
- Meta description of 155 characters or fewer that includes the keyword and a benefit.
- Excerpt of 1–2 sentences, used on the `/blogs` listing.

## 4. Ship it as a seeder

Production is updated by running seeders, and nobody copies SQL by hand, so every post goes into its own file: `database/seeders/{StudlySlug}BlogSeeder.php` (e.g. `LuxuryLodgesEverestBaseCampBlogSeeder`). Create it with `php artisan make:seeder {Name} --no-interaction`, then fill it in following this shape:

```php
use ResolvesTripLinks; // database/seeders/ResolvesTripLinks.php

public function run(): void
{
    $blog = Blog::query()->updateOrCreate(['slug' => 'the-slug'], [
        'title' => '...',
        'excerpt' => '...',
        'content' => $this->resolveTripLinks($this->content()), // content() returns a nowdoc: <<<'HTML' ... HTML
        'status' => PageStatus::Published,
        'published_at' => now(),
    ]);

    $blogsHub = Page::query()->where('slug', 'blogs')->firstOrFail();
    app(RouteGenerator::class)->sync($blog, $blog->slug, 'blog', $blogsHub->route);

    $blog->seo()->updateOrCreate([], [
        'meta_title' => '...',
        'meta_description' => '...',
    ]);
}
```

- `updateOrCreate` on the slug means re-running the seeder updates the post instead of duplicating it.
- Only set `published_at` if the post doesn't already have one, so that re-running doesn't bump the date: `'published_at' => Blog::where('slug', ...)->value('published_at') ?? now()`.
- Leave `image_id` unset. Images are chosen in `/admin` through Curator. Don't guess a media ID, because IDs differ between environments.
- If the user asks for a draft, use `PageStatus::Draft`.

Then run `vendor/bin/pint --dirty --format agent`.

## 5. Verify locally

```bash
php artisan db:seed --class={Name} --no-interaction
curl -s http://everesttripplanner.test/blogs/{slug} | grep -oE '<title>[^<]*|<h1[^>]*>[^<]*'
```

Also check that every internal link in the content returns 200:

```bash
php artisan tinker --execute 'preg_match_all("/href=\"(\/[^\"]*)\"/", App\Models\Blog::where("slug","{slug}")->value("content"), $m); echo implode("\n", array_unique($m[1]));' \
  | while read p; do echo "$(curl -s -o /dev/null -w '%{http_code}' http://everesttripplanner.test$p) $p"; done
```

Fix any link that doesn't return 200 before reporting.

## 6. Report

Keep the report short:
- the URL,
- the primary keyword,
- the word count,
- the production command: `php artisan db:seed --class={Name} --force`,
- a note to pick a featured image in `/admin` → Blogs.

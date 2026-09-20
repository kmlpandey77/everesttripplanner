# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Primarily premium, time-constrained international travelers: higher-income trekkers with limited vacation time who want comfort (better lodges, heli options) rather than the budget teahouse-trekking experience. The catalog still carries a standard Everest Base Camp trek and a Gokyo Valley trek for a broader mixed-tier audience, but the luxury and heli products (Luxury Everest Base Camp Trek, EBC Trek return via heli, Everest Heli Tour) are the flagship draw and anchor positioning and pricing.

## Product Purpose

Everest Trip Planner helps trekkers plan and book guided treks in Nepal's Everest (Khumbu) region — from the classic Base Camp trek to shorter heli tours. Every trip page carries a full day-by-day itinerary, transparent pricing (includes/excludes), and practical trip details, so a visitor knows exactly what to expect before contacting the operator to book.

## Positioning

Local, Nepal-based operating expertise: guides based in the region they operate in, structured acclimatization and safety practices, and direct on-the-ground logistics — the kind of ground-level knowledge a foreign reseller can't credibly claim. Operating since 2015, licensed by the Nepal Tourism Board, and a TAAN (Trekking Agencies' Association of Nepal) member.

## Operating Context

- Content is split across three route-driven content types: **Pages** (informational/region content — Everest geography, Sherpa culture, key villages like Namche/Lukla/Tengboche — plus Home, About, Contact, Terms), **Categories** (hierarchical trek groupings, e.g. `everest-trek`), and **Trips** (bookable treks with duration, difficulty, altitude, price/price tiers, gallery, overview, day-by-day itinerary, includes/excludes, checklist, "know before you go", "do not do", FAQ, and an optional separate detailed-itinerary page).
- Booking is **inquiry-based only** — phone and WhatsApp (`App\Settings\GeneralSettings`), no online payment or checkout. Trip pages already surface this via an inline "Instant Help" call/WhatsApp widget; the homepage does not yet.
- Editors manage all of this through a Filament admin at `/admin`; the public site is server-rendered Blade with a JSON content-block builder for Page content.

## Capabilities and Constraints

- No online payment/checkout exists or is planned as of this record — every conversion path ends at a phone call or WhatsApp message, not a cart.
- Price tiers exist per trip (by group size) but are informational, resolved through the inquiry conversation, not a self-serve price calculator.
- SEO is a first-class concern: per-page meta/OG/Twitter/schema.org JSON-LD, auto-generated breadcrumbs, `/llms.txt` and `/sitemap.xml`.
- No online testimonials, review aggregator, or verified rating source is wired in yet — do not fabricate star ratings, review counts, or quotes.

## Brand Commitments

- Name: **Everest Trip Planner**. Logo at `public/images/logo.webp`.
- Frontend palette (`resources/css/app.css`): `--color-primary: #086972` (teal), `--color-secondary: #17b978` (green), `--color-action: #ff4500` (orange). Typeface: Instrument Sans.

## Evidence on Hand

- Operating since 2015, licensed by the Nepal Tourism Board, TAAN member. These are the only verified credibility facts on record — safe to state directly.
- Real trip catalog: Luxury Everest Base Camp Trek, Everest Base Camp Trek, EBC Trek return via heli, Everest Heli Tour, Gokyo Valley Trek.
- **Absences future work must not fabricate:** no confirmed testimonials/reviews, no confirmed guide count or names, no confirmed completed-trek count, no confirmed real trip photography beyond whatever is already uploaded to Curator media.

## Product Principles

1. Every trip page shows a transparent day-by-day itinerary and clear includes/excludes — no hidden costs, no bespoke quotes required to compare options.
2. Credibility comes from stated, verifiable local expertise (in-region guides, NTB license, TAAN membership, operating since 2015) — never from invented social proof.
3. Comfort and time-efficiency (premium lodges, heli options) are sold as deliberately as summit-style achievement, because the primary visitor is time-constrained, not budget-constrained.
4. The path to booking is always a human conversation (call/WhatsApp) — every surface that asks a visitor to act should make that contact path obvious, the way trip pages already do.
5. Safety is a stated operational commitment (structured acclimatization, experienced staff), not just marketing language.

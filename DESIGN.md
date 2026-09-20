---
name: Everest Trip Planner
description: A clean, dossier-like booking site for Everest region treks — trust through transparency, not decoration.
colors:
  glacier-teal: "#086972"
  rhododendron-green: "#17b978"
  signal-orange: "#ff4500"
  ink: "#0f172a"
  body-text: "#475569"
  meta-text: "#64748b"
  label-text: "#94a3b8"
  hairline: "#f1f5f9"
  surface: "#f8fafc"
  paper: "#ffffff"
typography:
  display:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "clamp(2.25rem, 4vw, 3rem)"
    fontWeight: 700
    lineHeight: 1.1
    letterSpacing: "-0.02em"
  headline:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.875rem"
    fontWeight: 700
    lineHeight: 1.25
    letterSpacing: "-0.02em"
  title:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.25rem"
    fontWeight: 600
    lineHeight: 1.4
  body:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.6
  label:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    letterSpacing: "0.05em"
rounded:
  sm: "8px"
  md: "12px"
  lg: "16px"
  full: "9999px"
spacing:
  xs: "8px"
  sm: "16px"
  md: "24px"
  lg: "48px"
  xl: "64px"
components:
  button-primary:
    backgroundColor: "{colors.glacier-teal}"
    textColor: "{colors.paper}"
    rounded: "{rounded.full}"
    padding: "12px 32px"
  button-secondary:
    backgroundColor: "{colors.rhododendron-green}"
    textColor: "{colors.paper}"
    rounded: "{rounded.full}"
    padding: "12px 24px"
  button-ghost:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    rounded: "{rounded.full}"
    padding: "12px 24px"
---

# Design System: Everest Trip Planner

## Overview

**Creative North Star: "The Basecamp Briefing"**

Every screen reads like a well-organized trip dossier handed to you before departure: the facts up front (duration, difficulty, altitude, price), the day-by-day story laid out as a scannable timeline, and nothing left for the visitor to guess at. This is a booking-inquiry site for a real Nepal-based operator, not a brochure — the design's job is to make a stranger trust a phone call or a WhatsApp message enough to make it, so every surface leans toward clarity and evidence over atmosphere.

The palette stays almost monochrome (slate ink on white/near-white paper) with exactly one accent pair doing all the color work — glacier teal for primary actions and links, rhododendron green for the second, more urgent action (Book Now, WhatsApp). Signal orange is held in reserve for a single, rare use: marking something as genuinely exceptional (Best Seller) or something to explicitly avoid (Do Not Do). The mood is clean, trustworthy, and unfussy — never touristy or kitschy (no cartoon mountains, no gradient-heavy "adventure brand" clichés) and never corporate-cold; the local, human, on-the-ground register (real guides, real logistics) should always show through.

**Key Characteristics:**
- Fact-first hierarchy: a `dl`-style label/value pattern (duration, difficulty, altitude) appears above prose everywhere it's relevant.
- Short Q&A content (FAQ) carries density behind an accordion; sequential, already-brief content (itinerary days) stays always-visible in a timeline instead — hiding a one-sentence description behind a click costs more than it saves.
- Checklists (includes/excludes, packing, do-not-do) use a consistent small checkmark/cross vocabulary, never bullets; Highlights reuses the same checkmark but at a larger, single-column scale, since it's the sales pitch rather than reference material.
- Structural surfaces are flat and bordered; shadows are reserved for things that float or respond to your cursor.
- One accent color pair only — a second or third accent is never introduced for decoration.

## Colors

Near-monochrome slate on white, with one deliberate accent pair and a single reserve color.

### Primary
- **Glacier Teal** (#086972): Links, primary icon accents, focus rings, the leading half of the primary CTA gradient, hover states on interactive text.

### Secondary
- **Rhododendron Green** (#17b978): The more urgent/committed action — "Book Now," the WhatsApp contact button, and every checkmark in an includes/checklist/highlights list. Reads as "yes, this is included / this is the next step."

### Tertiary
- **Signal Orange** (#ff4500): Reserved for exactly three roles — the "Best Seller" badge, "Do Not Do" cross-marks, and filled star-rating icons (`<x-rating>`). Never used for a fourth purpose.

### Neutral
- **Ink** (#0f172a / `slate-900`): Headings and primary text.
- **Body** (#475569 / `slate-600`): Paragraph and body copy.
- **Meta** (#64748b / `slate-500`): Secondary metadata (durations, dates, related-trip captions).
- **Label** (#94a3b8 / `slate-400`): Uppercase micro-labels ("From," "Instant Help," fact labels) and disabled iconography.
- **Hairline** (#f1f5f9 / `slate-100`): Borders, dividers, accordion rules.
- **Surface** (#f8fafc / `slate-50`): Subtle section backgrounds (trust strip, sub-nav under-layer, table stripes).
- **Paper** (#ffffff): Card and page background.

### Named Rules
**The One Accent Pair Rule.** Only glacier teal and rhododendron green do color work on any given screen. If a screen needs a third color signal, it borrows the reserved signal orange rather than introducing a new hue.

## Typography

**Display/Body Font:** Instrument Sans (with `ui-sans-serif, system-ui, sans-serif` fallback). No secondary or mono face — the system is single-family by design.

**Character:** Instrument Sans reads clean and slightly rounded — confident enough for headlines, quiet enough not to compete with trip photography. Weight (400/500/600) carries almost all the emphasis; the system rarely reaches for size alone.

### Hierarchy
- **Display** (700, `text-4xl` → `text-5xl` responsive, tight tracking): Hero headline only.
- **Headline** (700, `text-3xl`, tight tracking): Major section headings — "Why Trek With Us," "Frequently Asked Questions," page H1s (category name, trip title at the smaller end of Display).
- **Title** (600–700, `text-xl`–`text-2xl`): Subsection headings within a page (Highlights, Itinerary, Includes/Excludes).
- **Body** (400, `text-base`, `text-slate-600`): Paragraph copy, capped near 65–75ch measure via `prose` where rich text is involved.
- **Label** (600, `text-xs`, uppercase, `tracking-wide`, `text-slate-400`): Micro-labels above a value — "From," fact `dt`s, "Instant Help," "Group Discount."

### Named Rules
**The Weight-Over-Size Rule.** Distinguish emphasis with font-weight (400/500/600) before reaching for a larger size — the type scale stays tight (5 steps) on purpose.

## Layout

Two container widths carry the whole site: `max-w-6xl` (1152px) for anything with a grid — feature cards, trip cards, the trip page's two-thirds/one-third content-plus-sidebar split — and `max-w-3xl`/`max-w-4xl` for reading-focused sections (headings, prose blocks, hero copy, FAQ). Everything sits on `px-6` horizontal padding and is centered with `mx-auto`.

Vertical rhythm has two registers: `py-16` for a standalone major section (features, featured trips, FAQ), and a tighter `py-4`–`py-6` for a block that's visually coupled to the heading directly above it (a text paragraph following its own heading, the trust strip as a thin band between hero and content). Grids respond at `sm:` (2 columns) and `lg:` (3–4 columns), never introducing a third breakpoint.

Two structural patterns recur: a **sticky sub-nav** (trip page's in-page anchor nav, `sticky top-0` with `backdrop-blur`) for long detail pages, and a **sticky sidebar** (`sticky top-24` booking card) alongside a `lg:grid-cols-3` two-thirds/one-third split — the pattern to reuse whenever a page pairs long-form content with a persistent action.

## Elevation & Depth

**Flat-by-default, shadow-as-response.** Structural surfaces — accordions, dividers, list rows, bordered content containers — stay flat with a 1px neutral hairline border and no shadow. A shadow appears only when an element floats above the page or responds to the visitor's cursor: the sticky sidebar booking card, a trip card on hover, the hero's primary CTA, the gallery carousel's nav buttons, the sticky sub-nav's blur-backed bar.

### Shadow Vocabulary
- **Ambient card** (`shadow-sm`): Resting elevation for a floating surface that isn't being interacted with (sidebar booking card, feature icon badges tinted to their accent).
- **Hover lift** (`shadow-lg`/`shadow-xl`, tinted `shadow-slate-200` or `shadow-black/30`–`/40` on dark surfaces): The response to hover on a trip card or the hero CTA — always paired with a small `-translate-y-0.5` or scale, never shadow alone.

### Named Rules
**The Response, Not Rest Rule.** A shadow is earned by interactivity or floating position, never applied to a resting structural container.

## Shapes

Two radius tiers plus full pills. **Pills** (`rounded-full`) for every button and small badge (CTAs, subcategory chips, the Best Seller tag) — this is the system's most consistent form signature. **`rounded-lg`** (8px) for content-density containers: trip cards, accordions, bordered list boxes. **`rounded-2xl`** (16px) reserved for the few prominent standalone surfaces: the sidebar booking card and the trip gallery image. Borders are always a thin (1px) neutral hairline — never a colored or thick border, and never a colored `border-left`/`border-right` accent.

## Components

### Buttons
- **Shape:** Full pill (`rounded-full`) always.
- **Primary:** Teal→green gradient fill, white text, `px-6 py-3` (`px-8` at `md:`), used for the single most-wanted action on a screen (hero CTA, header "Plan Your Trip"). Carries a soft dark shadow that deepens on hover — the one button allowed a shadow at rest, because it always sits on the dark hero.
- **Secondary (solid):** Flat rhododendron-green fill, white text — the commit action ("Book Now"), never paired with the primary gradient on the same screen.
- **Ghost/Outline:** Transparent fill, neutral or white border depending on background, text-colored border-hover — the lower-commitment sibling action ("Make an Enquiry," hero secondary button).
- **Hover/Focus:** Border or shadow intensifies; no color hue change on hover, only opacity/shadow/translate.

### Cards
- **Trip card:** `rounded-lg`, 1px hairline border, white background, `shadow-sm` at rest, lifts to `shadow-lg` with a `-translate-y-0.5` and the cover image scales slightly on hover. Title, rating (stars + number, only when set), duration/difficulty meta, max altitude, price (in glacier teal) in that order. One shared component (`<x-trip-card>`, `resources/views/components/trip-card.blade.php`) renders every instance — the featured-trips block, category listing, and trip-page related-trips grid all use it; extend the component rather than re-implementing a card inline.
- **Sidebar booking card:** `rounded-2xl`, hairline border, `shadow-sm`, `sticky top-24`. Price-led (`From $X / person`), then optional group-tier pricing table, then CTAs, then a trust checklist, then the "Instant Help" call/WhatsApp row — always in that order, most-committed information first.

### Badges / Chips
- **Best Seller:** Small `rounded-full` signal-orange pill, positioned absolutely at a card/panel's top corner — the only badge that breaks its container's edge.
- **Subcategory chip:** `rounded-full`, outlined (not filled), hairline border, hover shifts border+text to glacier teal.

### Rating
Shared component (`<x-rating>`, `resources/views/components/rating.blade.php`), used on the trip page and the trip card. Five signal-orange stars with fractional fill (outline track + a clipped solid overlay, so 4.5 renders as 4 full stars and one half), a glacier-teal `rounded-full` pill giving the exact number ("4.5 stars"), and an optional "&ndash; based on N reviews" in meta-gray shown only when a review count is set. Both `rating` and `reviews_count` are editorial fields on `Trip` (nullable, edited in Filament) — the component renders nothing when `rating` is blank; never default or estimate a value.

### Accordion (details/summary)
Used for FAQ. `rounded-lg` bordered container, `divide-y` hairline rows, no native marker — a drawn heroicon chevron (`chevron-down`) rotates 180° open, with visible `focus-visible` outline for keyboard users. This is the canonical expand/collapse pattern; do not substitute a unicode glyph (`▼`) or an unstyled native `<details>` marker.

### Itinerary Table (signature component)
Used for the day-by-day summary on the trip page: a `rounded-lg border border-slate-100` table with a `bg-slate-50` header row (uppercase label styling) and `odd:bg-slate-50` striped body rows, columns Day / Activity / Walking Hr / Max Altitude — always visible, no click required, horizontally scrollable on narrow viewports rather than reflowing. Chosen over the accordion pattern because the day facts are structured and comparable across rows; reach for this whenever content is inherently sequential, brief, and multi-attribute, reach for the accordion when content is long-form or a genuine Q&A. The standalone detailed-itinerary sub-page (`trip-itinerary` route type) trades the table for one full-prose card per day — same day/fact vocabulary, narrative width instead of scan width.

### Trust Strip (signature component)
A thin, full-bleed band (`border-y`, `bg-slate-50`) of small monoline-icon + label pairs, stacked with horizontal dividers on mobile and laid out in a single row with vertical dividers at `sm:`. Deliberately distinct from the icon-badge feature grid below it — this is a credibility signal, not a benefits pitch, so it never grows icon circles, headings, or descriptions.

### Navigation
Header nav: plain text links, `hover:text-primary`, with dropdown submenus revealed on hover (`invisible`/`opacity-0` → visible on `group-hover`, shadow-lg dropdown panel). In-page sticky sub-nav on long trip pages: underline-on-hover tabs (`border-b-2 border-transparent` → `border-primary`), scroll-spy highlights the current section automatically via `IntersectionObserver`, in a horizontally-scrollable, blurred sticky bar.

## Do's and Don'ts

### Do:
- **Do** keep all color work inside the glacier-teal/rhododendron-green pair; reach for signal orange only for "exceptional" or "avoid" signals, never as a third neutral accent.
- **Do** lead every card and sidebar with the fact/price a visitor needs first, and put narrative prose second.
- **Do** use the drawn heroicon chevron for any expand/collapse control, with a visible focus ring.
- **Do** apply a shadow only in response to hover, sticky/floating position, or the single primary CTA on a dark hero — never as resting decoration on a flat container.
- **Do** use `rounded-full` for every clickable button/badge and reserve `rounded-2xl` for the handful of prominent standalone surfaces (sidebar, gallery).

### Don't:
- **Don't** introduce a second display typeface, a third accent hue, or a bigger type scale than the five defined steps.
- **Don't** use a unicode glyph or emoji as an icon — every icon is a drawn Heroicon at a consistent stroke weight. (The trip page's itinerary accordion still has an old unicode `▼` left over from before this rule was established on the FAQ block — migrate it the next time that file is touched.)
- **Don't** add a colored `border-left`/`border-right` accent to a card, list item, or callout.
- **Don't** fabricate testimonials, review counts, star ratings, or guide/completed-trek counts anywhere in the UI — per `PRODUCT.md`, none are confirmed yet.
- **Don't** stack two icon-badge grids back-to-back (e.g. a second "features" style block directly beside the trust strip or the feature list) — pick one pattern per page region.

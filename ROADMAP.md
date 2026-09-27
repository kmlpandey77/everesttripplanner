# Roadmap

Status of the CMS spec (Route-driven Pages/Categories/Trips, "every object
owns its route"). Legend: ✅ done · 🟡 partial · ⬜ not started.

## Core Architecture

- ✅ `Route` model — polymorphic (`routable_type`/`routable_id`), `slug`,
  `full_path`, `type`, `parent_id`, `redirect_to`, `redirect_code`,
  `is_active`.
- ✅ `Routable` contract — content models own exactly one `Route` via
  `morphOne`.
- ✅ `SEO` as a first-class model — `Seo` (polymorphic `seoable_type`/
  `seoable_id`), `HasSeo` contract, shared `SeoForm` schema, `SeoService`
  (see SEO below).
- ✅ `Media` as a first-class model — `awcodes/filament-curator` (see
  below).

## Page Module

- ✅ Title, slug, status (draft/scheduled/published), published-at, route.
- 🟡 Content Builder — 5 block types (heading, text, image, cta, html) of the
  spec's 16 (missing hero, gallery, video, accordion, faq, features,
  testimonials, map, markdown, custom Livewire block). Extensible via
  `@includeFirst` fallback.
- ✅ SEO — full `Seo` record (meta, canonical, robots, OpenGraph, Twitter
  card, schema JSON) via shared `SeoForm`/`SeoService`.
- ⬜ Versioning ("Version ready").
- ✅ Redirect support — `Route` has `redirect_to`/`redirect_code`, editable
  manually, plus automatic redirect-on-slug-change (see Redirect Manager
  below).

## Category Module

- ✅ Hierarchical tree via `parent_id`, unlimited nesting, generated
  nested URLs (`/nepal-trekking/everest-region`).
- ⬜ Reparenting cascades to descendant routes — currently only the
  reparented category's own route updates; children keep stale `full_path`.
- ⬜ Nested set model (using adjacency list instead).

## Trip Module

- ✅ `Trip` model with all spec fields (title, route, category, duration,
  difficulty, altitude, price, gallery, overview, itinerary, includes,
  excludes, map, FAQ, SEO, status), `TripResource` (form/table/pages),
  frontend `trip` dispatch in `FrontendController`, configurable
  "flat vs. nested under category" URL via `nest_route_under_category`.
- ✅ Gallery uses `CuratorPicker` (Media Library), resolved via
  `Trip::galleryMedia()`.

## Route Engine

- ✅ Route model, `RouteGenerator` (sync, reserved-slug guard, uniqueness
  guard, automatic redirect-stub creation on path change), `RouteResolver`.
- ✅ Reserved routes enforced (`admin`, `storage`, `api`, `livewire`,
  `build`, `assets`).
- ⬜ Automatic child-route regeneration on reparent (see Category above).
- ⬜ Slug history.

## Frontend Resolver

- ✅ Single catch-all `/{path?}` → `FrontendController` → `RouteResolver` →
  type-based render, 404 on miss, redirect-following.
- ✅ `type === 'trip'` branch.

## SEO

- ✅ `Seo` polymorphic model with meta title/description, canonical URL,
  robots index/follow, OpenGraph, Twitter card, schema.org type + raw
  JSON-LD overrides.
- ✅ Auto-generated JSON-LD via `SeoService`: `Product` for trips (with
  `Offer`/price), `WebPage` for pages/categories, `FAQPage` graph node for
  trips with FAQ content, `BreadcrumbList` built from route ancestry.
- ✅ AI/answer-engine optimization — `/llms.txt` (dynamic content map) and
  `robots.txt` explicitly allowing GPTBot/ClaudeBot/PerplexityBot/
  Google-Extended/etc.
- ✅ XML sitemap (`/sitemap.xml`, referenced from `robots.txt`).
- ⬜ Dedicated `SEOResource` (fields are edited in-context on each owning
  resource instead — may not be needed).

## Filament Resources

- ✅ `RouteResource` (read/edit only, no create — routes are generated),
  `PageResource`, `CategoryResource`, `TripResource`, Curator's `MediaResource`.
- ✅ `MenuResource` (menus + nested dropdown items, drag-to-reorder).
- ✅ `ManageGeneralSettings` (Spatie settings page, not a `Resource`).
- ⬜ `RedirectResource`, `SEOResource`.

## Navigation

- ✅ "CMS" group holds Pages, Categories, Routes, Trips, Media, Menus.
- ✅ "Settings" group holds "Manage General Settings".
- ⬜ SEO, Redirects nav items (blocked on their resources).

## Content Builder

- 🟡 See Page Module above — extensible block architecture in place,
  block library is a starter set. Image block now uses `CuratorPicker`.

## Media

- ✅ `awcodes/filament-curator` installed and configured — custom Filament
  theme (`resources/css/filament/admin/theme.css`) wired via
  `->viteTheme()`, `MediaResource` auto-registered in the "CMS" nav group,
  folders/alt text/title/caption/description, curations (crop presets),
  responsive image variants, Glide-based transforms.
- ✅ Trip gallery and Page `image` block both use `CuratorPicker` instead of
  raw `FileUpload`.
- ⬜ WebP conversion/optimization not explicitly configured (Glide supports
  it on request via URL params; no default policy set).

## Search

- ⬜ Not started. No global search, no Scout integration.

## Menus

- ✅ `Menu`/`MenuItem` models, `MenuResource` (nested repeater builder, one
  level of dropdown nesting, drag-to-reorder via `sort_order`), header nav
  (`main` menu slug) with dropdowns and a configurable CTA button
  (`cta_label`/`cta_url`).
- ⬜ No `MenuService`; querying/rendering is done directly in the layout
  Blade. No multi-location UI beyond the single `main` slug convention.

## Redirect Manager

- 🟡 301/302 supported as fields on `Route`, manually editable in
  `RouteResource`. `RouteGenerator::sync()` now automatically leaves a
  routable-less `type = redirect` stub at a routable's old `full_path`
  whenever its path changes (301, chain-flattened so a second slug change
  never produces a double-hop, and reclaimable if the slug is reverted).
- ⬜ No dedicated `redirects` table/resource — reuses the existing `routes`
  table/`RouteResource` (filterable by `type = redirect`) instead.
- ✅ Manual old-URL redirects — "Add redirect" action on the Routes list
  creates a `type = redirect` row (accepts a path or full URL).

## Permissions

- ⬜ Not started. No Filament Shield.

## Settings

- ✅ `spatie/laravel-settings` + `filament/spatie-laravel-settings-plugin`
  installed. `App\Settings\GeneralSettings` (group `general`) holds
  `phone`/`whatsapp_number`, managed via the "Manage General Settings" page
  (`admin/manage-general-settings`, nav group "Settings"). Trip page's
  "Instant Help" block reads from it.
- ⬜ No other settings migrated in yet (e.g. site-wide SEO defaults).

## Services

- ✅ `RouteGenerator`, `RouteResolver`, `SeoService` (also builds
  breadcrumb JSON-LD — no separate `BreadcrumbService`).
- ⬜ `SlugService`, `RedirectService`, `MenuService`, `SitemapService`,
  `SearchService`.

## Events

- ⬜ Not started. No `PageSaved`, `CategoryMoved`, `TripSaved`,
  `RouteUpdated`, `SlugChanged` events; no automatic redirect generation.

## Database

- ✅ `routes`, `pages`, `categories`, `trips`, `seos`, `curator`, `menus`,
  `menu_items`.
- ⬜ `redirects`.

## Filament UX / Performance

- ⬜ Clusters, widgets, global search config, relation managers, import/
  export, soft deletes, activity log, route/slug/search caching, queued
  image processing and sitemap generation — none started.

## Suggested Order

1. ✅ Trip module (completes the three core content types + route dispatch).
2. ✅ SEO model + fields expansion, sitemap.
3. ✅ Media (Curator) — Trip gallery and Page image block migrated.
4. ✅ Redirect automation (slug-change → auto redirect). Dedicated
   `RedirectResource` deferred — not needed yet.
5. Menus, Search, Settings, Permissions (Shield).
6. Filament UX polish (clusters, widgets, soft deletes, activity log) and
   performance (caching, queues) once the content surface is complete.

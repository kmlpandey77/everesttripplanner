<?php

namespace App\Services;

use App\Contracts\HasSeo;
use App\Contracts\Routable;
use App\Models\Blog;
use App\Models\Route;
use App\Settings\GeneralSettings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class SeoService
{
    /**
     * Resolve the effective SEO tag values for a routable model, applying
     * fallbacks (OG → meta, Twitter → OG, meta → model defaults).
     *
     * @return array<string, mixed>
     */
    public function resolve(HasSeo&Routable $model, string $url): array
    {
        $seo = $model->seo;

        $title = trim($seo?->meta_title ?: $model->defaultMetaTitle());
        $description = trim((string) ($seo?->meta_description ?: $model->defaultMetaDescription())) ?: null;
        $ogTitle = trim($seo?->og_title ?: $title);
        $ogDescription = trim((string) ($seo?->og_description ?: $description)) ?: null;
        // ponytail: reuses the schema image (trip gallery, blog featured image) so models need no extra method; site logo as last resort
        $ogImage = $this->imageUrl($seo?->og_image)
            ?: Arr::first(Arr::wrap($model->defaultSchemaData()['image'] ?? null))
            ?: asset('images/logo.webp');

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $seo?->canonical_url ?: $url,
            'robots' => $this->robotsContent($seo?->robots_index ?? true, $seo?->robots_follow ?? true),
            'og_type' => $model instanceof Blog ? 'article' : 'website',
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_image' => $ogImage,
            'twitter_card' => $seo?->twitter_card?->value ?? 'summary_large_image',
            'twitter_title' => trim($seo?->twitter_title ?: $ogTitle),
            'twitter_description' => trim((string) ($seo?->twitter_description ?: $ogDescription)) ?: null,
            'twitter_image' => $this->imageUrl($seo?->twitter_image) ?: $ogImage,
            'schema_ld' => $this->schemaLd($model, $seo?->schema_type, $seo?->schema_json, $seo?->canonical_url ?: $url),
        ];
    }

    private function robotsContent(bool $index, bool $follow): string
    {
        return ($index ? 'index' : 'noindex').','.($follow ? 'follow' : 'nofollow');
    }

    private function imageUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    /**
     * @param  array<string, mixed>|null  $overrides
     */
    private function schemaLd(HasSeo&Routable $model, ?string $type, ?array $overrides, string $url): string
    {
        $mainNode = [
            '@type' => $type ?: $model->defaultSchemaType(),
            'url' => $url,
            ...$model->defaultSchemaData(),
            ...($overrides ?? []),
        ];

        return $this->graph([
            $mainNode,
            $this->breadcrumbNode($model),
            ...$model->additionalSchemaGraph(),
        ]);
    }

    /**
     * JSON-LD @graph for a page: the given nodes plus the site-wide Organization and WebSite
     * that other nodes point at through organizationId().
     *
     * @param  array<int, array<string, mixed>|null>  $nodes
     */
    public function graph(array $nodes): string
    {
        $organization = array_filter([
            '@type' => 'TravelAgency',
            '@id' => self::organizationId(),
            'name' => config('app.name'),
            'url' => url('/'),
            'logo' => asset('images/logo.webp'),
            'image' => asset('images/logo.webp'),
            'telephone' => app(GeneralSettings::class)->phone,
        ], fn (mixed $value): bool => filled($value));

        $website = [
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'name' => config('app.name'),
            'url' => url('/'),
            'publisher' => ['@id' => self::organizationId()],
        ];

        return json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [...array_values(array_filter($nodes)), $organization, $website],
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public static function organizationId(): string
    {
        return url('/').'#organization';
    }

    /**
     * @return array<string, mixed>|null
     */
    private function breadcrumbNode(HasSeo&Routable $model): ?array
    {
        $route = $model->route;

        if (! $route) {
            return null;
        }

        $ancestors = [];

        for ($node = $route; $node !== null; $node = $node->parent) {
            $ancestors[] = $node;
        }

        $items = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ];

        foreach (array_reverse($ancestors) as $index => $ancestor) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 2,
                'name' => $ancestor->is($route) ? $model->defaultMetaTitle() : $this->routableLabel($ancestor),
                'item' => url($ancestor->full_path),
            ];
        }

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    private function routableLabel(Route $route): string
    {
        $routable = $route->routable;

        return $routable->title ?? $routable->name ?? $route->slug;
    }
}

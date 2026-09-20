<?php

namespace App\Services;

use App\Contracts\HasSeo;
use App\Contracts\Routable;
use App\Models\Route;
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

        $title = $seo?->meta_title ?: $model->defaultMetaTitle();
        $description = $seo?->meta_description ?: $model->defaultMetaDescription();
        $ogTitle = $seo?->og_title ?: $title;
        $ogDescription = $seo?->og_description ?: $description;
        $ogImage = $this->imageUrl($seo?->og_image);

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $seo?->canonical_url ?: $url,
            'robots' => $this->robotsContent($seo?->robots_index ?? true, $seo?->robots_follow ?? true),
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_image' => $ogImage,
            'twitter_card' => $seo?->twitter_card?->value,
            'twitter_title' => $seo?->twitter_title ?: $ogTitle,
            'twitter_description' => $seo?->twitter_description ?: $ogDescription,
            'twitter_image' => $this->imageUrl($seo?->twitter_image) ?: $ogImage,
            'schema_ld' => $this->schemaLd($model, $seo?->schema_type, $seo?->schema_json),
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
    private function schemaLd(HasSeo&Routable $model, ?string $type, ?array $overrides): string
    {
        $mainNode = [
            '@type' => $type ?: $model->defaultSchemaType(),
            ...$model->defaultSchemaData(),
            ...($overrides ?? []),
        ];

        $additionalNodes = array_filter([
            $this->breadcrumbNode($model),
            ...$model->additionalSchemaGraph(),
        ]);

        $schema = $additionalNodes === []
            ? ['@context' => 'https://schema.org', ...$mainNode]
            : ['@context' => 'https://schema.org', '@graph' => [$mainNode, ...$additionalNodes]];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
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

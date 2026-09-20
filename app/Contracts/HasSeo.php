<?php

namespace App\Contracts;

use App\Models\Seo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface HasSeo
{
    /**
     * @return MorphOne<Seo, $this>
     */
    public function seo(): MorphOne;

    /**
     * The fallback title/description used when no SEO override is set.
     */
    public function defaultMetaTitle(): string;

    public function defaultMetaDescription(): ?string;

    /**
     * The schema.org @type used when no SEO override is set.
     */
    public function defaultSchemaType(): string;

    /**
     * The auto-generated schema.org properties for this model, merged
     * underneath any manual overrides from its Seo record.
     *
     * @return array<string, mixed>
     */
    public function defaultSchemaData(): array;

    /**
     * Extra schema.org nodes (e.g. FAQPage) to include alongside the
     * primary entity via @graph, for richer AI/answer-engine indexing.
     *
     * @return array<int, array<string, mixed>>
     */
    public function additionalSchemaGraph(): array;
}

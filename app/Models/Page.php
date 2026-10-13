<?php

namespace App\Models;

use App\Contracts\HasSeo;
use App\Contracts\Routable;
use App\Enums\PageStatus;
use Awcodes\Curator\Models\Media;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Page extends Model implements HasSeo, Routable
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'is_home',
        'published_at',
        'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'status' => PageStatus::class,
            'is_home' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function route(): MorphOne
    {
        return $this->morphOne(Route::class, 'routable');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function isPublished(): bool
    {
        return $this->status === PageStatus::Published
            && ($this->published_at === null || $this->published_at->isPast());
    }

    public function defaultMetaTitle(): string
    {
        return $this->title;
    }

    public function defaultMetaDescription(): ?string
    {
        return null;
    }

    public function defaultSchemaType(): string
    {
        return 'WebPage';
    }

    public function defaultSchemaData(): array
    {
        $firstMediaId = collect($this->content ?? [])->pluck('data.media_id')->filter()->first();

        return array_filter([
            'name' => $this->title,
            'description' => $this->defaultMetaDescription(),
            'image' => $firstMediaId ? Media::find($firstMediaId)?->url : null,
        ], fn (mixed $value): bool => filled($value));
    }

    public function additionalSchemaGraph(): array
    {
        $questions = collect($this->content ?? [])
            ->where('type', 'faq')
            ->flatMap(fn (array $block) => $block['data']['items'] ?? [])
            ->filter(fn (array $item): bool => filled($item['question'] ?? null) && filled($item['answer'] ?? null))
            ->map(fn (array $item): array => [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['answer'],
                ],
            ])
            ->values();

        if ($questions->isEmpty()) {
            return [];
        }

        return [[
            '@type' => 'FAQPage',
            'mainEntity' => $questions->all(),
        ]];
    }
}

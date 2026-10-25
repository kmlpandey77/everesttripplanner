<?php

namespace App\Models;

use App\Contracts\HasSeo;
use App\Contracts\Routable;
use App\Enums\PageStatus;
use App\Services\MediaOptimizer;
use App\Services\SeoService;
use Awcodes\Curator\Models\Media;
use Database\Factories\BlogFactory;
use DOMDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Blog extends Model implements HasSeo, Routable
{
    /** @use HasFactory<BlogFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'image_id',
        'content',
        'status',
        'published_at',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $model): void {
            if ($model->wasRecentlyCreated || $model->wasChanged(['image_id', 'slug'])) {
                app(MediaOptimizer::class)->optimize($model->mediaIds(), (string) $model->slug);
            }
        });
    }

    /**
     * Curator media ids used by this blog post, in display order.
     *
     * @return array<int, int|string|null>
     */
    public function mediaIds(): array
    {
        return [$this->image_id];
    }

    protected function casts(): array
    {
        return [
            'status' => PageStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function route(): MorphOne
    {
        return $this->morphOne(Route::class, 'routable');
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
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
        if ($this->excerpt) {
            return Str::limit($this->excerpt, 160);
        }

        return $this->content ? Str::limit(html_entity_decode(strip_tags($this->content)), 160) : null;
    }

    public function defaultSchemaType(): string
    {
        return 'BlogPosting';
    }

    public function defaultSchemaData(): array
    {
        return array_filter([
            'headline' => $this->title,
            'description' => $this->defaultMetaDescription(),
            'image' => $this->image?->url,
            'datePublished' => $this->published_at?->toIso8601String(),
            'dateModified' => $this->updated_at?->toIso8601String(),
            'author' => ['@id' => SeoService::organizationId()],
            'publisher' => ['@id' => SeoService::organizationId()],
        ], fn (mixed $value): bool => filled($value));
    }

    /**
     * FAQPage built from the post's "Frequently asked questions" section (each <h3> question and the <p> after it).
     */
    public function additionalSchemaGraph(): array
    {
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8"?><body>'.$this->content.'</body>');

        $questions = [];
        $inFaq = false;
        $question = null;

        foreach ($dom->getElementsByTagName('body')->item(0)?->childNodes ?? [] as $node) {
            $text = trim($node->textContent);

            if ($node->nodeName === 'h2') {
                $inFaq = str_contains(strtolower($text), 'frequently asked');
            } elseif ($inFaq && $node->nodeName === 'h3') {
                $question = $text;
            } elseif ($inFaq && $node->nodeName === 'p' && $question !== null && $text !== '') {
                $questions[] = ['@type' => 'Question', 'name' => $question, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $text]];
                $question = null;
            }
        }

        return $questions === [] ? [] : [['@type' => 'FAQPage', 'mainEntity' => $questions]];
    }
}

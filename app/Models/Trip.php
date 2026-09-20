<?php

namespace App\Models;

use App\Contracts\HasSeo;
use App\Contracts\Routable;
use App\Enums\PageStatus;
use App\Enums\TripDifficulty;
use Awcodes\Curator\Models\Media;
use Database\Factories\TripFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Trip extends Model implements HasSeo, Routable
{
    /** @use HasFactory<TripFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'short_description',
        'slug',
        'category_id',
        'nest_route_under_category',
        'duration',
        'difficulty',
        'altitude',
        'price',
        'price_tiers',
        'gallery',
        'overview',
        'highlights',
        'itinerary',
        'detailed_itinerary',
        'includes',
        'excludes',
        'checklist',
        'know_before_you_go',
        'do_not_do',
        'map_url',
        'faq',
        'departures',
        'status',
        'is_best_seller',
        'rating',
        'reviews_count',
        'currently_viewing',
        'last_booked_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'nest_route_under_category' => 'boolean',
            'difficulty' => TripDifficulty::class,
            'price' => 'decimal:2',
            'price_tiers' => 'array',
            'is_best_seller' => 'boolean',
            'rating' => 'decimal:1',
            'reviews_count' => 'integer',
            'currently_viewing' => 'integer',
            'last_booked_at' => 'datetime',
            'gallery' => 'array',
            'highlights' => 'array',
            'itinerary' => 'array',
            'detailed_itinerary' => 'array',
            'includes' => 'array',
            'excludes' => 'array',
            'checklist' => 'array',
            'do_not_do' => 'array',
            'faq' => 'array',
            'departures' => 'array',
            'status' => PageStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function route(): MorphOne
    {
        return $this->morphOne(Route::class, 'routable')->where('type', 'trip');
    }

    /**
     * The Route for this trip's separate detailed-itinerary page, if any.
     */
    public function itineraryRoute(): MorphOne
    {
        return $this->morphOne(Route::class, 'routable')->where('type', 'trip-itinerary');
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function publishedReviews(): HasMany
    {
        return $this->reviews()->published();
    }

    /**
     * The rating to display: the live average of published reviews when any exist,
     * otherwise the manually-entered editorial `rating`. Never guessed.
     */
    public function displayRating(): ?float
    {
        $reviews = $this->relationLoaded('publishedReviews') ? $this->publishedReviews : $this->publishedReviews()->get();

        if ($reviews->isNotEmpty()) {
            return round((float) $reviews->avg('rating'), 1);
        }

        return $this->rating !== null ? (float) $this->rating : null;
    }

    /**
     * The review count to display: the live count of published reviews when any exist,
     * otherwise the manually-entered editorial `reviews_count`.
     */
    public function displayReviewsCount(): ?int
    {
        $reviews = $this->relationLoaded('publishedReviews') ? $this->publishedReviews : $this->publishedReviews()->get();

        return $reviews->isNotEmpty() ? $reviews->count() : $this->reviews_count;
    }

    /**
     * Upcoming fixed departure dates, soonest first.
     *
     * @return Collection<int, array{date: string, seats_available: int|null, price: string|null, is_guaranteed: bool}>
     */
    public function upcomingDepartures(): Collection
    {
        return collect($this->departures ?? [])
            ->filter(fn (array $departure): bool => filled($departure['date'] ?? null) && ! now()->startOfDay()->isAfter($departure['date']))
            ->sortBy('date')
            ->values();
    }

    /**
     * The trip's gallery images, in the order they were picked.
     *
     * @return Collection<int, Media>
     */
    public function galleryMedia(): Collection
    {
        $ids = $this->gallery ?? [];

        return Media::query()->findMany($ids)->sortBy(fn (Media $media): int => array_search($media->id, $ids))->values();
    }

    /**
     * Other published trips in the same category, for cross-linking.
     *
     * @return Collection<int, Trip>
     */
    public function relatedTrips(int $limit = 3): Collection
    {
        if (! $this->category_id) {
            return collect();
        }

        return static::query()
            ->with(['route', 'publishedReviews'])
            ->where('category_id', $this->category_id)
            ->whereKeyNot($this->id)
            ->get()
            ->filter->isPublished()
            ->take($limit)
            ->values();
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
        if ($this->short_description) {
            return Str::limit($this->short_description, 160);
        }

        return $this->overview ? Str::limit(html_entity_decode(strip_tags($this->overview)), 160) : null;
    }

    public function defaultSchemaType(): string
    {
        return 'Product';
    }

    public function defaultSchemaData(): array
    {
        return array_filter([
            'name' => $this->title,
            'description' => $this->defaultMetaDescription(),
            'image' => $this->galleryMedia()->map(fn (Media $media): string => $media->url)->all(),
            'offers' => $this->price ? [
                '@type' => 'Offer',
                'price' => (string) $this->price,
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock',
            ] : null,
        ], fn (mixed $value): bool => filled($value));
    }

    public function additionalSchemaGraph(): array
    {
        if (blank($this->faq)) {
            return [];
        }

        return [[
            '@type' => 'FAQPage',
            'mainEntity' => collect($this->faq)
                ->filter(fn (array $entry): bool => filled($entry['question'] ?? null) && filled($entry['answer'] ?? null))
                ->map(fn (array $entry): array => [
                    '@type' => 'Question',
                    'name' => $entry['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $entry['answer'],
                    ],
                ])
                ->values()
                ->all(),
        ]];
    }
}

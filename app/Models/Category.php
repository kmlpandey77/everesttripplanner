<?php

namespace App\Models;

use App\Contracts\HasSeo;
use App\Contracts\Routable;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Category extends Model implements HasSeo, Routable
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    public function route(): MorphOne
    {
        return $this->morphOne(Route::class, 'routable');
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function defaultMetaTitle(): string
    {
        return $this->name;
    }

    public function defaultMetaDescription(): ?string
    {
        return $this->description;
    }

    public function defaultSchemaType(): string
    {
        return 'WebPage';
    }

    public function defaultSchemaData(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    public function additionalSchemaGraph(): array
    {
        return [];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    /** Ürün kategorisi: telefon, tablet… — il × hizmet sayfaları yalnız bunlar için üretilir. */
    public const GROUP_CATEGORY = 'category';

    /** Ürün durumu / alım şekli: stok fazlası, arızalı, tasfiye… */
    public const GROUP_CONDITION = 'condition';

    public const GROUPS = [
        self::GROUP_CATEGORY => 'Ürün kategorisi',
        self::GROUP_CONDITION => 'Ürün durumu / alım şekli',
    ];

    protected $fillable = [
        'title', 'slug', 'group', 'icon', 'image', 'image_alt', 'short_description', 'description',
        'what_we_do', 'suitable_for', 'process_steps', 'faqs',
        'meta_title', 'meta_description', 'is_featured', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'what_we_do' => 'array',
            'suitable_for' => 'array',
            'process_steps' => 'array',
            'faqs' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function scopeCategories(Builder $query): Builder
    {
        return $query->where('group', self::GROUP_CATEGORY);
    }

    public function scopeConditions(Builder $query): Builder
    {
        return $query->where('group', self::GROUP_CONDITION);
    }

    public function isCategory(): bool
    {
        return $this->group === self::GROUP_CATEGORY;
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function getUrlAttribute(): string
    {
        return url('/'.$this->slug);
    }

    public function getImageUrlAttribute(): string
    {
        return media_url($this->image, asset('images/placeholder.svg'));
    }

    public function getIconClassAttribute(): string
    {
        return $this->icon ?: 'fa-solid fa-microchip';
    }
}

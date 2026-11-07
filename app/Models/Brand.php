<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Toplu alım yaptığımız markalar.
 *
 * Marka sayfaları hizmet sayfalarından AYRI bir eksendir: ziyaretçi "iphone toplu
 * alımı" diye arar, "toplu telefon alımı" diye değil. İkisini tek modelde toplamak
 * her iki tarafı da zayıflatırdı.
 */
class Brand extends Model
{
    protected $fillable = [
        'name', 'slug', 'service_id', 'category_service_id', 'logo', 'description', 'content', 'products', 'faqs',
        'meta_title', 'meta_description', 'is_active', 'is_featured', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'products' => 'array',
            'faqs' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * service_id DOLUYSA marka ayrı sayfa açmaz, o hizmet sayfasına yönlenir.
     * İki sayfa aynı sorguda yarışır ve ikisi de kaybeder (spec §3.4).
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /** Markanın ait olduğu ürün grubu (Apple → Toplu Telefon Alımı). Sayfa yönlendirmesi yapmaz. */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'category_service_id');
    }

    public function hasOwnPage(): bool
    {
        return blank($this->service_id);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /** Kartın ve menünün gideceği adres: hizmet sayfası varsa oraya. */
    public function path(): string
    {
        if ($slug = $this->service?->slug) {
            return '/'.$slug;
        }

        return '/markalar/'.$this->slug;
    }

    public function getUrlAttribute(): string
    {
        return url($this->path());
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? media_url($this->logo) : null;
    }

    /** Sayfa başlığı: marka adı tek başına arama karşılığı vermez. */
    public function getHeadingAttribute(): string
    {
        return $this->name.' Toplu Alımı';
    }
}

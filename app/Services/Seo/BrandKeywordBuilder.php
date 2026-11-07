<?php

namespace App\Services\Seo;

use App\Models\Brand;
use App\Models\SeoKeyword;
use App\Models\SeoTarget;
use App\Support\PathNormalizer;
use App\Support\TurkishText;

/**
 * Marka adı içeren anahtar kelimeleri üretir — DETERMİNİSTİK, AI kullanmaz.
 *
 * Marka kelimeleri bölge kelimelerinden AYRI bir eksendir: ziyaretçi "iphone toplu
 * alımı" diye arar ve bu, "toplu telefon alımı" ile aynı sayfaya düşmemelidir.
 *
 * Sahiplik dağılımı (spec §3.1 — tek sahip kuralı):
 *   "{marka} toplu alımı"         → MARKA SAYFASI sahiplenir (ticari)
 *   "{il} {marka} toplu alımı"    → SAHİPSİZ bırakılır; blog üretim hattının hedefi olur
 *
 * Bölge × marka çarpımı YALNIZ öne çıkan markalar × öne çıkan iller
 * (config/site.featured_provinces) için üretilir. 973 ilçeyi her markayla çarpmak
 * on binlerce, birbirinin aynısı konu üretirdi.
 */
class BrandKeywordBuilder
{
    private const NOTE_PREFIX = 'Marka otomasyonu';

    /** Marka sayfasının sahipleneceği ticari kalıplar. */
    private const OWNED_PATTERNS = [
        '%s toplu alımı' => 'COMMERCIAL_PRIMARY',
        '%s toptan alımı' => 'COMMERCIAL_VARIANT',
        'toplu %s alan yerler' => 'COMMERCIAL_VARIANT',
    ];

    public function __construct(private LocationKeywordBuilder $regions) {}

    /** @return array{created: int, assigned: int, deactivated: int, brands: array<int, string>} */
    public function build(): array
    {
        $created = $assigned = 0;
        $brands = [];
        $activeHashes = [];

        $featured = (array) config('site.featured_provinces', []);
        $reachableRegions = array_values(array_filter(
            $this->regions->reachableRegions(),
            fn (array $r) => $r['type'] === 'province' && in_array(ltrim($r['url'], '/'), $featured, true),
        ));

        foreach (Brand::query()->where('is_active', true)->orderBy('sort_order')->get() as $brand) {
            $brands[] = $brand->name;
            $target = SeoTarget::query()->where('url_hash', PathNormalizer::hash($brand->path()))->first();

            $name = TurkishText::searchLower($brand->name);

            foreach (self::OWNED_PATTERNS as $pattern => $type) {
                // DİKKAT: mb_strtolower "İ" → "i̇" üretir (birleşik nokta
                // U+0307 kalır) ve kelime aranan hâlinden farklı kaydedilir.
                $keyword = sprintf($pattern, $name);
                $activeHashes[] = md5(TurkishText::lower($keyword));

                [$row, $isNew] = $this->upsert($keyword, [
                    'keyword_type' => $type,
                    'search_intent' => 'transactional',
                    'priority' => 2,
                    'note' => self::NOTE_PREFIX.' · '.$brand->name,
                ]);

                $created += $isNew ? 1 : 0;

                // Sahipsizse ata; elle YAŞAYAN başka bir sahibe verilmişse DOKUNMA.
                // Sahibi pasife düşmüş bir hedefse (marka hizmet sayfasına bağlandığında
                // olur) kelime öksüz kalmasın diye doğru hedefe taşınır.
                if ($target && $target->status && $this->needsOwner($row, $target)) {
                    $row->update(['target_id' => $target->getKey()]);
                    $assigned++;
                }
            }

            if (! $brand->is_featured) {
                continue;
            }

            // Bölge × marka — blog hattı için SAHİPSİZ kalır.
            foreach ($reachableRegions as $region) {
                $keyword = TurkishText::searchLower($region['name']).' '.$name.' toplu alımı';
                $activeHashes[] = md5(TurkishText::lower($keyword));

                [, $isNew] = $this->upsert($keyword, [
                    'keyword_type' => 'BLOG_PRIMARY',
                    'search_intent' => 'commercial',
                    'priority' => 3,
                    'note' => self::NOTE_PREFIX.' · '.$brand->name.' · '.$region['label'],
                ]);

                $created += $isNew ? 1 : 0;
            }
        }

        // Marka veya bölge kapatıldıysa kelimeleri pasife alınır — SİLİNMEZ (spec §10.9).
        $deactivated = SeoKeyword::query()
            ->where('note', 'like', self::NOTE_PREFIX.'%')
            ->whereNotIn('keyword_hash', $activeHashes)
            ->where('status', true)
            ->update(['status' => false]);

        return ['created' => $created, 'assigned' => $assigned, 'deactivated' => $deactivated, 'brands' => $brands];
    }

    /** Sahibi yok ya da sahibi artık yayında olmayan bir hedef mi? */
    private function needsOwner(SeoKeyword $row, SeoTarget $target): bool
    {
        if (! $row->hasOwner()) {
            return true;
        }

        if ($row->owner_blog_id || $row->target_id === $target->getKey()) {
            return false;
        }

        return ! SeoTarget::query()->whereKey($row->target_id)->where('status', true)->exists();
    }

    /** @return array{0: SeoKeyword, 1: bool} */
    private function upsert(string $keyword, array $attributes): array
    {
        $hash = md5(TurkishText::lower($keyword));
        $existing = SeoKeyword::query()->where('keyword_hash', $hash)->first();

        if ($existing) {
            if (! $existing->status && str_starts_with((string) $existing->note, self::NOTE_PREFIX)) {
                $existing->update(['status' => true]);
            }

            return [$existing, false];
        }

        return [SeoKeyword::create(['keyword' => $keyword, 'status' => true] + $attributes), true];
    }
}

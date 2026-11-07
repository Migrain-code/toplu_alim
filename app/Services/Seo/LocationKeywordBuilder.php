<?php

namespace App\Services\Seo;

use App\Models\Province;
use App\Models\SeoKeyword;
use App\Models\SeoTarget;
use App\Models\Service;
use App\Support\PathNormalizer;
use App\Support\TurkishText;

/**
 * İl adı içeren anahtar kelimeleri üretir — DETERMİNİSTİK, AI kullanmaz.
 *
 * Kaynak: müşterinin anahtar kelime listesi (toplu_elektronik_seo_anahtar_kelime_listesi.xlsx,
 * "İl Bazlı Liste" sayfası): 81 il × 30 kalıp. Eksik kalan hizmetler için birer kalıp eklenir.
 *
 * YALNIZ İLLER: ilçeler bot SEO kapsamı dışındadır (kelime, hedef, iç link, AI metni yok).
 * Yalnız aktif iller için üretilir; kapalı ilin kelimesi pasife alınır, silinmez.
 *
 * Sahiplik (tek sahip kuralı): her kalıp TEK bir sayfaya aittir. Yakın varyasyonlar
 * ("toplu telefon alanlar" / "toplu telefon alımı" / "toplu iPhone alanlar") aynı il ×
 * hizmet sayfasında toplanır; her kelime için ayrı adres açılmaz.
 */
class LocationKeywordBuilder
{
    /**
     * kalıp ({il} yer tutucu) => [hedef hizmet slug'ı (null = il sayfası), kelime tipi]
     * Her hedefin bir COMMERCIAL_PRIMARY kelimesi vardır; gerisi varyanttır.
     */
    public const PATTERNS = [
        // İl sayfası: /istanbul
        '{il} toplu elektronik alımı' => [null, 'COMMERCIAL_PRIMARY'],
        '{il} toplu elektronik alanlar' => [null, 'COMMERCIAL_VARIANT'],
        '{il} toptan elektronik alımı' => [null, 'COMMERCIAL_VARIANT'],
        '{il} elektronik eşya alan firmalar' => [null, 'COMMERCIAL_VARIANT'],

        // Telefon
        '{il} toplu telefon alımı' => ['toplu-telefon-alimi', 'COMMERCIAL_PRIMARY'],
        '{il} toplu telefon alanlar' => ['toplu-telefon-alimi', 'COMMERCIAL_VARIANT'],
        '{il} ikinci el telefon alan firmalar' => ['toplu-telefon-alimi', 'COMMERCIAL_VARIANT'],
        '{il} toplu iphone alanlar' => ['toplu-telefon-alimi', 'COMMERCIAL_VARIANT'],
        '{il} toplu samsung telefon alanlar' => ['toplu-telefon-alimi', 'COMMERCIAL_VARIANT'],
        '{il} toplu xiaomi telefon alanlar' => ['toplu-telefon-alimi', 'COMMERCIAL_VARIANT'],

        // Bilgisayar
        '{il} toplu bilgisayar alımı' => ['toplu-bilgisayar-alimi', 'COMMERCIAL_PRIMARY'],
        '{il} toplu bilgisayar alanlar' => ['toplu-bilgisayar-alimi', 'COMMERCIAL_VARIANT'],
        '{il} ikinci el bilgisayar alan firmalar' => ['toplu-bilgisayar-alimi', 'COMMERCIAL_VARIANT'],
        '{il} toplu laptop alanlar' => ['toplu-bilgisayar-alimi', 'COMMERCIAL_VARIANT'],

        // Tablet
        '{il} toplu tablet alımı' => ['toplu-tablet-alimi', 'COMMERCIAL_PRIMARY'],
        '{il} toplu tablet alanlar' => ['toplu-tablet-alimi', 'COMMERCIAL_VARIANT'],

        // Robot süpürge
        '{il} toplu robot süpürge alımı' => ['toplu-robot-supurge-alimi', 'COMMERCIAL_PRIMARY'],
        '{il} toplu robot süpürge alanlar' => ['toplu-robot-supurge-alimi', 'COMMERCIAL_VARIANT'],
        '{il} robot süpürge toplu alımı' => ['toplu-robot-supurge-alimi', 'COMMERCIAL_VARIANT'],
        '{il} toplu dyson alanlar' => ['toplu-robot-supurge-alimi', 'COMMERCIAL_VARIANT'],

        // Diğer elektronik cihazlar
        '{il} toplu elektronik cihaz alımı' => ['toplu-elektronik-cihaz-alimi', 'COMMERCIAL_PRIMARY'],
        '{il} toplu playstation alanlar' => ['toplu-elektronik-cihaz-alimi', 'COMMERCIAL_VARIANT'],
        '{il} toplu televizyon alanlar' => ['toplu-elektronik-cihaz-alimi', 'COMMERCIAL_VARIANT'],
        '{il} toplu yazıcı alanlar' => ['toplu-elektronik-cihaz-alimi', 'COMMERCIAL_VARIANT'],

        // Kurumsal
        '{il} kurumsal elektronik alımı' => ['kurumsal-elektronik-alimi', 'COMMERCIAL_PRIMARY'],
        '{il} şirket bilgisayarlarını alan firmalar' => ['kurumsal-elektronik-alimi', 'COMMERCIAL_VARIANT'],
        '{il} şirket telefonlarını alan firmalar' => ['kurumsal-elektronik-alimi', 'COMMERCIAL_VARIANT'],

        // Ürün durumları
        '{il} stok fazlası elektronik alanlar' => ['stok-fazlasi-elektronik-alimi', 'COMMERCIAL_PRIMARY'],
        '{il} depo tasfiyesi elektronik alımı' => ['elektronik-depo-bosaltma', 'COMMERCIAL_PRIMARY'],
        '{il} tasfiye elektronik alımı' => ['tasfiye-elektronik-alimi', 'COMMERCIAL_PRIMARY'],
        '{il} elektronik hurda alan firmalar' => ['arizali-hurda-elektronik-alimi', 'COMMERCIAL_PRIMARY'],
        '{il} arızalı elektronik cihaz alanlar' => ['arizali-hurda-elektronik-alimi', 'COMMERCIAL_VARIANT'],
        '{il} teşhir elektronik ürün alanlar' => ['teshir-iade-elektronik-alimi', 'COMMERCIAL_PRIMARY'],
        '{il} iade elektronik ürün alanlar' => ['teshir-iade-elektronik-alimi', 'COMMERCIAL_VARIANT'],
        '{il} ikinci el elektronik toplu alımı' => ['ikinci-el-elektronik-toplu-alimi', 'COMMERCIAL_PRIMARY'],
    ];

    /** @return array{created: int, assigned: int, deactivated: int, regions: array<int, string>} */
    public function build(): array
    {
        $created = $assigned = 0;
        $regions = [];
        $activeHashes = [];
        $activeSlugs = Service::query()->where('is_active', true)->pluck('slug')->all();

        foreach ($this->reachableRegions() as $region) {
            $regions[] = $region['label'];
            // TurkishText::searchLower: mb_strtolower "İ" harfinde birleşik nokta bırakır.
            $name = TurkishText::searchLower($region['name']);

            foreach (self::PATTERNS as $pattern => [$serviceSlug, $type]) {
                // Kapalı hizmetin il sayfası 404 döner; kelimesi üretilmez.
                if ($serviceSlug && ! in_array($serviceSlug, $activeSlugs, true)) {
                    continue;
                }

                $keyword = str_replace('{il}', $name, $pattern);
                $activeHashes[] = md5(TurkishText::lower($keyword));

                [$row, $isNew] = $this->upsert($keyword, [
                    'keyword_type' => $type,
                    'search_intent' => 'transactional',
                    'priority' => $type === 'COMMERCIAL_PRIMARY' ? 1 : 2,
                    'note' => 'Bölge otomasyonu · '.$region['label'],
                ]);

                $url = $region['url'].($serviceSlug ? '/'.$serviceSlug : '');
                $target = SeoTarget::query()->where('url_hash', PathNormalizer::hash($url))->first();

                $created += $isNew ? 1 : 0;
                $assigned += $this->assign($row, $target);
            }
        }

        // Kapanan il/hizmetin ve eski ilçe kalıplarının kelimeleri pasife alınır — SİLİNMEZ.
        $deactivated = SeoKeyword::query()
            ->where('note', 'like', 'Bölge otomasyonu%')
            ->whereNotIn('keyword_hash', $activeHashes)
            ->where('status', true)
            ->update(['status' => false]);

        return ['created' => $created, 'assigned' => $assigned, 'deactivated' => $deactivated, 'regions' => $regions];
    }

    /**
     * Bot SEO'nun çalıştığı bölgeler: YALNIZ aktif iller (ilçeler kapsam dışı).
     *
     * @return array<int, array{name: string, label: string, url: string, type: string}>
     */
    public function reachableRegions(): array
    {
        return Province::query()->where('is_active', true)->orderBy('sort_order')->get()
            ->map(fn (Province $province) => [
                'name' => $province->name,
                'label' => $province->name.' (il)',
                'url' => '/'.$province->slug,
                'type' => 'province',
            ])
            ->all();
    }

    /** Sahipsizse hedefe ata; elle başka bir sahibe verilmişse DOKUNMA. */
    private function assign(SeoKeyword $row, ?SeoTarget $target): int
    {
        if ($target && $target->status && ! $row->hasOwner()) {
            $row->update(['target_id' => $target->getKey()]);

            return 1;
        }

        return 0;
    }

    /** @return array{0: SeoKeyword, 1: bool} */
    private function upsert(string $keyword, array $attributes): array
    {
        $hash = md5(TurkishText::lower($keyword));
        $existing = SeoKeyword::query()->where('keyword_hash', $hash)->first();

        if ($existing) {
            // Kapalıyken tekrar aktifleşen ilin kelimesini geri aç.
            if (! $existing->status && str_starts_with((string) $existing->note, 'Bölge otomasyonu')) {
                $existing->update(['status' => true]);
            }

            return [$existing, false];
        }

        return [SeoKeyword::create(['keyword' => $keyword, 'status' => true] + $attributes), true];
    }
}

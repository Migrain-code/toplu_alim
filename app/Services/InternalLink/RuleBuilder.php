<?php

namespace App\Services\InternalLink;

use App\Models\Brand;
use App\Models\InternalLinkRule;
use App\Models\Province;
use App\Models\SeoTarget;
use App\Models\Service;
use App\Support\PathNormalizer;
use App\Support\TurkishText;

/**
 * Aktif hizmet ve bölge sayfalarından iç link kuralı üretir — DETERMİNİSTİK.
 *
 * Anchor metinleri UYDURULMAZ: yalnız gerçek sayfa adlarından gelir.
 * Yalnız erişilebilir hedefler için kural açılır; kapalı sayfalar pasife alınır.
 */
class RuleBuilder
{
    /**
     * Hizmet sayfaları için doğal anchor varyantları.
     *
     * Sayfa başlığı ("Toplu Bilgisayar ve Laptop Alımı") düz metinde nadiren aynen
     * geçer; yazarlar "toplu laptop alımı" der. Varyantlar UYDURULMAZ, her biri gerçek
     * bir sayfaya işaret eder ve elle seçilmiştir. Bir ifade YALNIZ bir sayfada geçer.
     *
     * @var array<string, array<int, string>>
     */
    private const SERVICE_ALIASES = [
        'toplu-telefon-alimi' => ['toplu telefon alımı', 'toplu cep telefonu alımı', 'toptan telefon alımı'],
        'toplu-bilgisayar-alimi' => ['toplu bilgisayar alımı', 'toplu laptop alımı', 'notebook toplu alımı'],
        'toplu-tablet-alimi' => ['toplu tablet alımı', 'toptan tablet alımı'],
        'toplu-robot-supurge-alimi' => ['toplu robot süpürge alımı', 'toptan robot süpürge alımı'],
        'toplu-elektronik-cihaz-alimi' => ['toplu elektronik cihaz alımı', 'toplu elektronik eşya alımı'],
        'kurumsal-elektronik-alimi' => ['kurumsal elektronik alımı', 'şirket demirbaşı alımı', 'kurumsal IT ekipmanı'],
        'stok-fazlasi-elektronik-alimi' => ['stok fazlası elektronik', 'stok fazlası alımı'],
        'elektronik-depo-bosaltma' => ['elektronik depo boşaltma', 'depo boşaltma'],
        'tasfiye-elektronik-alimi' => ['tasfiye ürünleri alımı', 'mağaza kapanış ürünleri'],
        'arizali-hurda-elektronik-alimi' => ['hurda telefon alımı', 'arızalı elektronik alımı', 'hurda elektronik'],
        'teshir-iade-elektronik-alimi' => ['teşhir ürün alımı', 'iade ürün alımı', 'kapalı kutu ürün'],
        'ikinci-el-elektronik-toplu-alimi' => ['ikinci el elektronik toplu alımı', 'parti mal elektronik'],
    ];

    /** @return array{created: int, deactivated: int} */
    public function build(): array
    {
        $created = 0;
        $activeHashes = [];

        // Hizmet sayfaları — başlık + doğal varyantlar.
        $categories = collect();

        foreach (Service::query()->where('is_active', true)->get() as $service) {
            $url = '/'.$service->slug;
            $created += $this->upsert($service->title, $url, 'all', 70, $activeHashes);

            foreach (self::SERVICE_ALIASES[$service->slug] ?? [] as $alias) {
                $created += $this->upsert($alias, $url, 'all', 65, $activeHashes);
            }

            // İl × hizmet sayfaları 12 hizmetin hepsi için var (kategori + durum).
            $categories->push($service);
        }

        /*
         * Marka sayfaları.
         *
         * Anchor olarak yalın marka adı KULLANILMAZ: "Apple", "Oppo" gibi adlar
         * metinde başka bağlamda (meyve, genel ürün sözü) geçer ve yanlış yere link
         * basılır. Tek kelimelik markalarda "{marka} toplu alımı" kalıbı zorunludur.
         */
        foreach (Brand::query()->where('is_active', true)->with('service:id,slug')->get() as $brand) {
            $url = $brand->path();
            $created += $this->upsert($brand->name.' toplu alımı', $url, 'all', 55, $activeHashes);

            if (str_contains(trim($brand->name), ' ')) {
                $created += $this->upsert($brand->name, $url, 'all', 35, $activeHashes);
            }
        }

        /*
         * Bölge sayfaları — YALNIZ dizine açık (kendine özgü metni olan) sayfalar.
         * noindex bir sayfaya iç link basmak link değerini boşa harcar.
         *
         * Yalın il adı anchor YAPILMAZ: bazı il adları günlük kelimedir ("Ordu", "Bolu").
         */
        foreach (Province::query()->where('is_active', true)->get() as $province) {
            if (! $province->isIndexable()) {
                continue;
            }

            $url = '/'.$province->slug;
            $created += $this->upsert($province->name.' toplu elektronik alımı', $url, 'all', 50, $activeHashes);

            // İl × hizmet: "İstanbul toplu telefon alımı" → /istanbul/toplu-telefon-alimi
            foreach ($categories as $service) {
                $created += $this->upsert(
                    $province->name.' '.TurkishText::lower($service->title),
                    $url.'/'.$service->slug,
                    'all',
                    62,
                    $activeHashes,
                );
            }
        }

        // İlçe sayfalarına OTOMATİK iç link kurulmaz: ilçeler bot SEO kapsamı dışında
        // (yalnız iller). Elle metin yazılan ilçe yine de site haritasına girer.

        // Kaynağı kapanan kurallar pasife alınır — silinmez.
        $deactivated = InternalLinkRule::query()
            ->whereNotIn('target_hash', $activeHashes)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return ['created' => $created, 'deactivated' => $deactivated];
    }

    private function upsert(string $anchor, string $url, string $scope, int $priority, array &$hashes): int
    {
        $hash = PathNormalizer::hash($url);
        $hashes[] = $hash;

        // Hedef gerçekten var mı? (SeoTarget senkronu bunu doğrular)
        if (! SeoTarget::query()->where('url_hash', $hash)->where('status', true)->exists()) {
            return 0;
        }

        $existing = InternalLinkRule::query()->where('anchor_text', $anchor)->where('target_hash', $hash)->first();

        if ($existing) {
            $existing->is_active || $existing->update(['is_active' => true]);

            return 0;
        }

        InternalLinkRule::create([
            'anchor_text' => $anchor,
            'target_url' => $url,
            'scope_type' => $scope,
            'priority' => $priority,
            'max_per_article' => 1,
            'is_active' => true,
        ]);

        return 1;
    }
}

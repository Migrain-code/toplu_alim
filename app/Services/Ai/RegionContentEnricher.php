<?php

namespace App\Services\Ai;

use App\Models\Brand;
use App\Models\District;
use App\Models\Province;
use App\Models\Service;
use App\Services\Seo\KeywordSections;
use App\Support\AutomationLog;
use App\Support\BusinessContext;
use App\Support\TurkishText;
use Illuminate\Database\Eloquent\Model;

/**
 * Bölge ve hizmet sayfalarının içeriğini zenginleştirir (spec §1: AI yalnız içerik üretir).
 *
 * KURAL: mevcut benzersiz giriş metni KORUNUR, üzerine bölgeye özgü H2 bölümleri eklenir.
 * Şablonla çoğaltılmış içerik üretilmez; prompt'a yalnız O BÖLGEYE ait gerçek veriler verilir
 * (mahalle listesi, bağlı olduğu il, mevcut metin, sunulan hizmetler).
 *
 * Rakam, ödül, tecrübe yılı gibi doğrulanamaz iddialar YASAKTIR (spec §10.1) ve çıktı
 * ayrıca kod tarafında temizlenir.
 */
class RegionContentEnricher
{
    public function __construct(
        protected AiClient $ai,
        protected ContentSanitizer $sanitizer,
    ) {}

    /** @return array{words_before: int, words_after: int, h2: int} */
    public function enrich(Model $model): array
    {
        [$system, $user, $meta] = match (true) {
            // İlçeler otomatik metin ALMAZ (bot SEO yalnız illerde); elle yazılabilir.
            $model instanceof District => throw new AiException('İlçe sayfaları otomatik zenginleştirilmez; metni panelden elle girin.'),
            $model instanceof Province => $this->provincePrompt($model),
            $model instanceof Service => $this->servicePrompt($model),
            $model instanceof Brand => $this->brandPrompt($model),
            default => throw new AiException('Bu içerik tipi zenginleştirilemiyor: '.$model::class),
        };

        $before = $this->sanitizer->wordCount($this->bodyOf($model));

        $payload = $this->ai->json('seo.content', $system, $user, $meta);

        $body = $this->sanitizer->bodyHtml($payload['body_html'] ?? null);

        if ($this->sanitizer->wordCount($body) < 200) {
            throw new AiException('Üretilen içerik çok kısa, kaydedilmedi.');
        }

        $body = $this->expandIfShort($body, $system, $meta);

        // Fırsat avının eklediği kelime bölümleri yeni metnin sonuna aynen geri konur;
        // yoksa her zenginleştirme o kelimeler için yapılan işi sessizce silerdi.
        [, $keywordBlocks] = KeywordSections::extract($this->bodyOf($model));

        if ($keywordBlocks) {
            $body .= "\n".implode("\n", $keywordBlocks);
        }

        // Marka metninin sonundaki hukuki not ("marka adları sahiplerine aittir…") korunur.
        if ($model instanceof Brand && preg_match('/<p>\s*<small>.*?<\/small>\s*<\/p>\s*$/su', (string) $this->bodyOf($model), $notice)
            && ! str_contains($body, '<small>')) {
            $body .= "\n".trim($notice[0]);
        }

        // Mevcut daha uzun metin daha kısa bir AI metniyle DEĞİŞTİRİLMEZ (canlıda Ankara
        // 433 → 310 kelimeye düşmüştü).
        if ($before > 0 && $this->sanitizer->wordCount($body) < $before) {
            throw new AiException('Yeni metin mevcut metinden kısa ('.$this->sanitizer->wordCount($body).' < '.$before.' kelime), kaydedilmedi.');
        }

        $updates = [$this->bodyField($model) => $body];

        // Meta açıklama 70-155 aralığına çekilir.
        if (filled($payload['meta_description'] ?? null)) {
            $updates['meta_description'] = $this->sanitizer->metaDescription($payload['meta_description'], $model->meta_description);
        }

        if (filled($payload['meta_title'] ?? null)) {
            $updates['meta_title'] = $this->sanitizer->metaTitle($payload['meta_title'], $model->meta_title);
        }

        $model->forceFill($updates)->save();

        $after = $this->sanitizer->wordCount($body);

        AutomationLog::summary('seo.enrich', [
            'type' => class_basename($model),
            'id' => $model->getKey(),
            'words_before' => $before,
            'words_after' => $after,
            'h2' => preg_match_all('/<h2[\s>]/i', $body),
        ], changed: true);

        return ['words_before' => $before, 'words_after' => $after, 'h2' => preg_match_all('/<h2[\s>]/i', $body)];
    }

    /** Skorun "iyi içerik" eşiği 600 kelime; altında kalan metne yeni bölüm eklenir. */
    public const EXPAND_BELOW = 600;

    /**
     * Modeller istenen kelime sayısını tutturmaz (canlıda 650-800 istendi, ~300 geldi) ve
     * "genişlet" denince metni baştan yazıp yine kısa bırakır. Bu yüzden modelden YALNIZ
     * eklenecek yeni H2 bölümleri istenir ve mevcut metnin sonuna eklenir: metin her
     * turda kesin uzar. En fazla iki tur; çağrı başarısız olursa eldeki metin kalır.
     */
    private function expandIfShort(string $body, string $system, array $meta): string
    {
        for ($round = 1; $round <= 2; $round++) {
            $words = $this->sanitizer->wordCount($body);

            if ($words >= self::EXPAND_BELOW) {
                break;
            }

            preg_match_all('/<h2[^>]*>(.*?)<\/h2>/su', $body, $m);
            $need = max(250, 680 - $words);

            $user = implode("\n", [
                'GÖREV: Aşağıdaki sayfa metni '.$words.' kelime. Metnin SONUNA eklenecek 2-3 YENİ bölüm yaz.',
                '- Toplam EN AZ '.$need.' kelime. Her bölüm <h2> başlık + 2-3 paragraf (gerekirse <ul>).',
                '- Mevcut başlıkları TEKRARLAMA: '.implode(' | ', array_map(fn ($h) => trim(strip_tags($h)), $m[1] ?? [])),
                '- Konu önerileri: satış öncesi hazırlık, veri silme ve hesap kilitleri, lot/stok listesi hazırlama,',
                '  ürün durumuna göre değerlendirme, teslim alma ve paketleme, fatura/evrak, sık yapılan hatalar.',
                '- Yeni bilgi UYDURMA; rakam, fiyat, süre VERME. Mevcut metni çıktıya KOYMA, yalnız yeni bölümler.',
                '- Çıktı yalnız JSON: {"body_html":""}',
                '',
                'MEVCUT METİN (bağlam için):',
                trim(strip_tags($body)),
            ]);

            try {
                $payload = $this->ai->json('seo.content', $system, $user, array_merge($meta, ['batch_key' => ($meta['batch_key'] ?? 'enrich').'.expand']));
                $extra = $this->sanitizer->bodyHtml($payload['body_html'] ?? null);
            } catch (\Throwable) {
                break;
            }

            if ($this->sanitizer->wordCount($extra) < 80) {
                break;
            }

            $body = rtrim($body)."\n".$extra;
        }

        return $body;
    }

    private function bodyField(Model $model): string
    {
        return $model instanceof Service ? 'description' : 'content';
    }

    private function bodyOf(Model $model): ?string
    {
        return $model->{$this->bodyField($model)};
    }

    private function systemPrompt(): string
    {
        return implode("\n", [
            BusinessContext::intro(),
            'SEO içerik yazarısın. Türkçe, sade ve güven veren bir dille yazarsın.',
            '',
            ...BusinessContext::domainLines(),
            '',
            'MUTLAK KURALLAR:',
            ...BusinessContext::factRules(),
            '- Yalnız sana verilen gerçek bilgileri kullan. Verilmeyen mahalle, ilçe veya yer adı UYDURMA.',
            '- O şehirde şube, depo, ofis veya ekip "bulunduğunu" YAZMA; alımın o bölgeye yapıldığını yaz.',
            '- Şehir hakkında doğrulanamayan istatistik (nüfus, firma sayısı, sıralama) VERME.',
            '- <a> etiketi KULLANMA. Hiçbir link ekleme; linkleri ayrı bir sistem basar.',
            '- <h1> kullanma; sayfanın tek H1 başlığını şablon basar. Ara başlıklar <h2>, alt başlıklar <h3>.',
            '- Pazarlama klişesi ve abartı kullanma. Satıcının işine yarayan somut bilgi ver.',
            '',
            'ÇIKTI: yalnızca geçerli JSON. Markdown, kod bloğu veya açıklama ekleme.',
            'Şema: {"body_html":"","meta_title":"","meta_description":""}',
        ]);
    }

    /** Mevcut gövde metni; boşsa kısa açıklama (seed şablonu) bağlam olarak kullanılır. */
    private function existingText(?string $content, ?string $description): string
    {
        $text = trim(strip_tags(KeywordSections::extract($content)[0]));

        return $text !== '' ? $text : '(Henüz gövde metni yok. Kısa açıklama: '.trim((string) $description).')';
    }

    /** @return array{0: string, 1: string, 2: array} */
    private function provincePrompt(Province $province): array
    {
        // İl başına en fazla 40 ilçe adı: İstanbul gibi büyük illerde istem gereksiz şişmesin.
        $districts = $province->activeDistricts()->limit(40)->pluck('name')->all();
        $categories = Service::query()->where('is_active', true)->where('group', Service::GROUP_CATEGORY)->pluck('title')->all();
        $conditions = Service::query()->where('is_active', true)->where('group', Service::GROUP_CONDITION)->pluck('title')->all();

        $lines = [
            'GÖREV: Aşağıdaki il sayfasının gövde metnini yaz (mevcut metin varsa genişlet).',
            '',
            'İL: '.$province->name,
            '',
            'MEVCUT METİN (varsa bilgilerini KORU, üzerine ekleyerek genişlet):',
            $this->existingText($province->content, $province->description),
            '',
            'BU İLİN İLÇELERİ (yalnız bunları kullan, başka ilçe UYDURMA):',
            implode(', ', $districts),
            '',
            'TOPLU ALDIĞIMIZ ÜRÜN GRUPLARI (her biri için bu ilde ayrı sayfa var):',
            '- '.implode("\n- ", $categories),
            'ALIM ŞEKİLLERİ / ÜRÜN DURUMLARI:',
            '- '.implode("\n- ", $conditions),
            '',
            'İSTENEN YAPI (650-800 kelime, EN AZ 600):',
            '1. Giriş paragrafları (H2 başlığı olmadan): '.$province->name.' genelinde toptan ve toplu elektronik alımı.',
            '2. <h2>'.$province->name.' genelinde toplu aldığımız ürünler</h2> — her ürün grubu için 1-2 cümle; durumlar',
            '3. <h2>Kimlerden alım yapıyoruz?</h2> — ilin genel ticari yapısına uygun satıcı tipleri; doğrulanamayan iddia YOK',
            '4. <h2>Hangi ilçelere geliyoruz?</h2> — yalnız yukarıdaki ilçelerden örnek vererek',
            '5. <h2>Teklif süreci</h2> — arama/WhatsApp, ürün listesi, teklif, teslim alma',
            '',
            'Metinde "toplu" ve "toptan" kelimeleri doğal biçimde birlikte geçsin.',
            'Şu ifadelerden en az üçü metinde AYNEN (bu yazımla) geçsin; her biri bu ilin ilgili sayfasına bağlanır:',
            $this->anchorPhrases($province),
            'meta_title: en fazla 60 karakter, "'.$province->name.'" geçsin. meta_description: 120-150 karakter.',
        ];

        return [
            $this->systemPrompt(),
            implode("\n", $lines),
            ['content_type' => 'province', 'content_id' => $province->getKey(), 'batch_key' => 'enrich.province'],
        ];
    }

    /** @return array{0: string, 1: string, 2: array} */
    private function servicePrompt(Service $service): array
    {
        $lines = [
            'GÖREV: Aşağıdaki hizmet sayfasının açıklama metnini genişlet.',
            '',
            'HİZMET: '.$service->title,
            'KISA AÇIKLAMA: '.$service->short_description,
            '',
            'MEVCUT METİN (bilgilerini KORU, üzerine ekleyerek genişlet):',
            strip_tags(KeywordSections::extract($service->description)[0]),
            '',
            'BU HİZMETTE ALDIKLARIMIZ (sayfada ayrı bir liste olarak zaten var, metinde TEKRARLAMA;',
            'bunları bağlam olarak kullan):',
            '- '.implode("\n- ", (array) $service->what_we_do),
            '',
            'HİZMET BÖLGESİ: Türkiye geneli, 81 il.',
            '',
            'İSTENEN YAPI (650-800 kelime, EN AZ 600):',
            '1. Giriş paragrafları (H2 başlığı olmadan)',
            '2. <h2>Nasıl çalışıyoruz?</h2> — ilk aramadan teslim almaya adım adım',
            '3. <h2>Sık karşılaşılan durumlar</h2> — bu ürün grubuna/duruma özgü gerçek sorular (kilit, veri, eksik aksesuar, karışık lot)',
            '4. <h2>Fiyatı ne belirler?</h2> — RAKAM VERME, yalnız fiyatı etkileyen etkenleri say',
            '',
            'meta_title: en fazla 60 karakter. meta_description: 120-150 karakter.',
        ];

        return [
            $this->systemPrompt(),
            implode("\n", $lines),
            ['content_type' => 'service', 'content_id' => $service->getKey(), 'batch_key' => 'enrich.service'],
        ];
    }

    /**
     * İç link kurallarıyla BİREBİR aynı ifadeler (RuleBuilder: il adı + küçük harf hizmet
     * başlığı). Metinde bu yazımla geçerse links:apply ilgili il × hizmet sayfasına bağlar.
     */
    private function anchorPhrases(Province $province): string
    {
        return Service::query()->active()->ordered()
            ->whereIn('slug', ['toplu-telefon-alimi', 'toplu-bilgisayar-alimi', 'toplu-tablet-alimi', 'stok-fazlasi-elektronik-alimi', 'toplu-robot-supurge-alimi'])
            ->get()
            ->map(fn (Service $s) => '"'.$province->name.' '.TurkishText::lower($s->title).'"')
            ->implode(', ').'.';
    }

    /** @return array{0: string, 1: string, 2: array} */
    private function brandPrompt(Brand $brand): array
    {
        $lines = [
            'GÖREV: Aşağıdaki marka sayfasının metnini genişlet. Sayfa, bu markanın ürünlerini TOPLU SATMAK',
            'isteyenlere (bayi, mağaza, şirket, ithalatçı, e-ticaret satıcısı) yöneliktir; biz alıcıyız.',
            '',
            'MARKA: '.$brand->name,
            'KISA AÇIKLAMA: '.$brand->description,
            '',
            'MEVCUT METİN (bilgilerini KORU, üzerine ekleyerek genişlet):',
            $this->existingText($brand->content, $brand->description),
            '',
            'ALDIĞIMIZ ÜRÜN GRUPLARI (sayfada ayrı liste olarak zaten var, metinde TEKRARLAMA; bağlam olarak kullan):',
            '- '.implode("\n- ", (array) $brand->products),
            '',
            'MARKA HAKKINDA İDDİA YASAK: markanın kalitesi, pazar payı, satış rakamı, sıralaması, ödülleri',
            'veya "en iyi/en çok tercih edilen" gibi yorumlar YAZMA. Yetkili satıcı/servis olduğumuzu ima ETME.',
            'Yalnız bu markanın ürünlerini toplu satarken satıcının işine yarayan somut bilgileri yaz.',
            '',
            'İSTENEN YAPI (600-750 kelime, EN AZ 600):',
            '1. Giriş paragrafları (H2 başlığı olmadan): '.$brand->name.' ürünlerini toplu satmak isteyenler için kısa bir çerçeve.',
            '2. <h2>'.$brand->name.' ürünlerinde hangi durumları alıyoruz?</h2> — sıfır/kapalı kutu, teşhir, iade, ikinci el, arızalı',
            '3. <h2>Teklif için hangi bilgiler gerekir?</h2> — model, kapasite/özellik, adet, kutu/aksesuar, hesap/kilit durumu, fatura',
            '4. <h2>Satış öncesi hazırlık</h2> — veri silme, hesaptan çıkarma, lot hazırlama (bu markanın ürünlerine uygun genel adımlar)',
            '',
            'meta_title: en fazla 60 karakter, "'.$brand->name.'" ve "toplu" geçsin. meta_description: 120-150 karakter.',
        ];

        return [
            $this->systemPrompt(),
            implode("\n", $lines),
            ['content_type' => 'brand', 'content_id' => $brand->getKey(), 'batch_key' => 'enrich.brand'],
        ];
    }
}

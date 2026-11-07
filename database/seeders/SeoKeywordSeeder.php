<?php

namespace Database\Seeders;

use App\Models\SeoKeyword;
use App\Support\TurkishText;
use Illuminate\Database\Seeder;

/**
 * Başlangıç kelime havuzu — müşterinin ilettiği toplu alım kelime listesi.
 *
 * Bunlar ARAMA HACMİ İDDİASI TAŞIMAZ: hacim alanı boş bırakılır. Listedeki
 * "Öncelik" sütunu tahminidir (Yüksek → 1, Orta → 2). Gerçek hacim Search
 * Console / Keyword Planner verisiyle güncellenmelidir.
 *
 * Sınıflandırma:
 *  - Soru kalıbı ("nereye satabilirim", "kime satabilir…", "nasıl satarım") → blog
 *  - HEAD listesindekiler → ticari ana kelime (bir sayfanın baş kelimesi)
 *  - Diğerleri → ticari varyant
 */
class SeoKeywordSeeder extends Seeder
{
    /** Sayfaların baş kelimeleri (tek sahip kuralı: her biri bir sayfaya ait). */
    private const HEAD = [
        'toptan elektronik alımı',
        'toplu elektronik alanlar',
        'toplu elektronik alımı',
        'toplu telefon alanlar',
        'toplu bilgisayar alımı',
        'toplu laptop alanlar',
        'toplu tablet alımı',
        'toplu robot süpürge alımı',
        'toplu elektronik cihaz alanlar',
        'kurumsal elektronik alımı',
        'kurumsal IT ekipmanı alımı',
        'stok fazlası elektronik alımı',
        'elektronik depo boşaltma',
        'tasfiye elektronik ürün alımı',
        'toptan hurda telefon alımı',
        'arızalı elektronik ürün alanlar',
        'teşhir elektronik ürün alanlar',
        'iade elektronik ürün alanlar',
        'ikinci el elektronik toplu alımı',
    ];

    public function run(): void
    {
        $seen = [];

        foreach ($this->keywords() as [$keyword, $priority]) {
            $hash = md5(TurkishText::lower($keyword));

            if (isset($seen[$hash])) {
                continue;
            }
            $seen[$hash] = true;

            [$intent, $type] = $this->classify($keyword);

            SeoKeyword::query()->firstOrCreate(
                ['keyword_hash' => $hash],
                [
                    'keyword' => $keyword,
                    'search_intent' => $intent,
                    'keyword_type' => $type,
                    'priority' => $priority,
                    'status' => true,
                    'note' => 'Müşteri kelime listesi',
                ]
            );
        }
    }

    private function classify(string $keyword): array
    {
        $lower = TurkishText::lower($keyword);

        foreach (['nereye satabilir', 'kime satabilir', 'nasıl satarım'] as $question) {
            if (str_contains($lower, $question)) {
                return ['informational', 'BLOG_PRIMARY'];
            }
        }

        $heads = array_map(fn ($h) => TurkishText::lower($h), self::HEAD);

        return ['transactional', in_array($lower, $heads, true) ? 'COMMERCIAL_PRIMARY' : 'COMMERCIAL_VARIANT'];
    }

    /** @return array<int, array{0: string, 1: int}> [kelime, öncelik] */
    private function keywords(): array
    {
        return [
            // Müşterinin kendi ifadeleri
            ['toptan elektronik alımı', 1],
            ['toptan hurda telefon alımı', 1],
            ['toptan robot süpürge alımı', 1],

            // Genel elektronik
            ['toplu elektronik alanlar', 1],
            ['toplu elektronik alan firmalar', 1],
            ['toplu elektronik alımı', 1],
            ['toplu elektronik eşya alan yerler', 1],
            ['elektronik ürünleri toplu alanlar', 1],
            ['elektronik stok fazlası alan firmalar', 1],
            ['stok fazlası elektronik alımı', 1],
            ['elektronik depo boşaltma', 1],
            ['toplu elektronik ürün satmak istiyorum', 1],
            ['elimizdeki elektronik ürünleri satmak istiyoruz', 1],
            ['kurumsal elektronik alımı', 1],
            ['firma elektronik stok alımı', 2],
            ['spot elektronik alanlar', 2],
            ['ikinci el elektronik toplu alımı', 1],
            ['arızalı elektronik ürün alanlar', 2],
            ['teşhir elektronik ürün alanlar', 2],
            ['iade elektronik ürün alanlar', 2],
            ['tasfiye elektronik ürün alımı', 1],
            ['mağaza kapanış ürünleri alanlar', 2],
            ['fazla stok ürünleri alan firmalar', 2],
            ['kullanılmayan elektronik cihazları alanlar', 2],

            // Telefon
            ['toplu telefon alanlar', 1],
            ['toplu telefon alan firmalar', 1],
            ['toplu cep telefonu alımı', 1],
            ['telefonları toplu alan yerler', 1],
            ['çok sayıda telefon alan firma', 2],
            ['ikinci el telefonları toplu alanlar', 1],
            ['sıfır telefon stok fazlası alanlar', 1],
            ['telefon stok fazlası alımı', 1],
            ['telefon deposu boşaltma', 1],
            ['toplu telefon satmak istiyorum', 1],
            ['telefonlarımı toplu satmak istiyorum', 1],
            ['elimde çok sayıda telefon var', 2],
            ['şirket telefonlarını toplu satmak', 1],
            ['kurumsal telefon alımı', 1],
            ['mağaza telefon stoğu alanlar', 2],
            ['spot telefon alanlar', 2],
            ['arızalı telefonları toplu alanlar', 1],
            ['hurda telefon alanlar', 2],
            ['çıkma telefon alanlar', 2],
            ['ekranı kırık telefon alanlar', 2],
            ['iade telefon alanlar', 2],
            ['teşhir telefon alanlar', 2],
            ['kapalı kutu telefon alanlar', 2],
            ['iphone toplu alımı', 1],
            ['samsung telefon toplu alımı', 1],
            ['xiaomi telefon toplu alımı', 1],
            ['telefon partisi alanlar', 2],
            ['telefon lot alımı', 2],
            ['telefon tasfiye alımı', 1],

            // Bilgisayar ve laptop
            ['toplu bilgisayar alanlar', 1],
            ['toplu bilgisayar alan firmalar', 1],
            ['toplu bilgisayar alımı', 1],
            ['bilgisayarları toplu alan yerler', 1],
            ['çok sayıda bilgisayar alan firma', 2],
            ['ikinci el bilgisayar toplu alımı', 1],
            ['sıfır bilgisayar stok fazlası alanlar', 1],
            ['bilgisayar stok fazlası alımı', 1],
            ['bilgisayar depo boşaltma', 1],
            ['toplu bilgisayar satmak istiyorum', 1],
            ['bilgisayarlarımı toplu satmak istiyorum', 1],
            ['şirket bilgisayarlarını toplu satmak', 1],
            ['kurumsal bilgisayar alımı', 1],
            ['ofis bilgisayarları alan firmalar', 2],
            ['iş yeri bilgisayarlarını alanlar', 2],
            ['laptop toplu alımı', 1],
            ['toplu laptop alanlar', 1],
            ['notebook toplu alımı', 1],
            ['masaüstü bilgisayar toplu alımı', 1],
            ['oyuncu bilgisayarı toplu alımı', 1],
            ['ikinci el laptop alan firmalar', 2],
            ['arızalı bilgisayar alanlar', 2],
            ['hurda bilgisayar alan firmalar', 2],
            ['bilgisayar kasası alanlar', 2],
            ['sunucu toplu alımı', 1],
            ['server ekipmanı alanlar', 2],
            ['iş istasyonu alan firmalar', 2],
            ['bilgisayar parçası toplu alımı', 1],
            ['monitör toplu alımı', 1],
            ['kurumsal IT ekipmanı alımı', 1],
            ['bilgisayar tasfiye alımı', 1],

            // Tablet
            ['toplu tablet alanlar', 1],
            ['toplu tablet alan firmalar', 1],
            ['toplu tablet alımı', 1],
            ['tabletleri toplu alan yerler', 1],
            ['çok sayıda tablet alan firma', 2],
            ['ikinci el tablet toplu alımı', 1],
            ['sıfır tablet stok fazlası alanlar', 1],
            ['tablet stok fazlası alımı', 1],
            ['tablet depo boşaltma', 1],
            ['toplu tablet satmak istiyorum', 1],
            ['tabletlerimi toplu satmak istiyorum', 1],
            ['şirket tabletlerini toplu satmak', 1],
            ['kurumsal tablet alımı', 1],
            ['ipad toplu alımı', 1],
            ['samsung tablet toplu alımı', 1],
            ['lenovo tablet toplu alımı', 1],
            ['arızalı tablet alanlar', 2],
            ['hurda tablet alanlar', 2],
            ['teşhir tablet alanlar', 2],
            ['iade tablet alanlar', 2],
            ['tablet tasfiye alımı', 1],

            // Robot süpürge ve süpürge
            ['toplu robot süpürge alanlar', 1],
            ['toplu robot süpürge alan firmalar', 1],
            ['toplu robot süpürge alımı', 1],
            ['robot süpürgeleri toplu alan yerler', 1],
            ['çok sayıda robot süpürge alan firma', 2],
            ['ikinci el robot süpürge toplu alımı', 1],
            ['sıfır robot süpürge stok fazlası alanlar', 1],
            ['robot süpürge stok fazlası alımı', 1],
            ['robot süpürge depo boşaltma', 1],
            ['toplu robot süpürge satmak istiyorum', 1],
            ['robot süpürgelerimi toplu satmak istiyorum', 1],
            ['kurumsal robot süpürge alımı', 1],
            ['arızalı robot süpürge alanlar', 2],
            ['hurda robot süpürge alanlar', 2],
            ['teşhir robot süpürge alanlar', 2],
            ['iade robot süpürge alanlar', 2],
            ['robot süpürge tasfiye alımı', 1],
            ['robot süpürge lot alımı', 2],
            ['xiaomi robot süpürge toplu alımı', 1],
            ['roborock toplu alım', 1],
            ['dreame robot süpürge toplu alımı', 1],
            ['viomi robot süpürge toplu alımı', 1],
            ['iRobot toplu alım', 1],
            ['Dyson süpürge toplu alımı', 1],
            ['dikey süpürge toplu alımı', 1],
            ['şarjlı süpürge toplu alımı', 1],
            ['elektrikli süpürge toplu alımı', 1],
            ['arızalı dikey süpürge alanlar', 2],

            // Diğer elektronik
            ['toplu elektronik cihaz alanlar', 1],
            ['toplu elektronik eşya alımı', 1],
            ['toplu televizyon alanlar', 1],
            ['televizyon toplu alımı', 1],
            ['yazıcı toplu alımı', 1],
            ['fotokopi makinesi toplu alımı', 1],
            ['kamera toplu alımı', 1],
            ['fotoğraf makinesi toplu alımı', 1],
            ['oyun konsolu toplu alımı', 1],
            ['PlayStation toplu alımı', 1],
            ['Xbox toplu alımı', 1],
            ['Nintendo Switch toplu alımı', 1],
            ['akıllı saat toplu alımı', 1],
            ['kulaklık toplu alımı', 1],
            ['hoparlör toplu alımı', 1],
            ['modem router toplu alımı', 1],
            ['network ekipmanı toplu alımı', 1],
            ['POS cihazı toplu alımı', 1],
            ['barkod okuyucu toplu alımı', 1],
            ['projeksiyon cihazı toplu alımı', 1],
            ['güvenlik kamerası toplu alımı', 1],
            ['elektronik kart toplu alımı', 1],
            ['elektronik parça toplu alımı', 1],
            ['arızalı elektronik cihaz alanlar', 2],
            ['hurda elektronik cihaz alanlar', 2],

            // Kurumsal ve tasfiye
            ['şirket demirbaşlarını satın alan firmalar', 2],
            ['ofis elektronik eşyalarını alan firmalar', 2],
            ['kurumsal elektronik cihaz alımı', 1],
            ['şirket bilgisayarlarını satın alan firmalar', 2],
            ['şirket telefonlarını satın alan firmalar', 2],
            ['ofis bilgisayarları toplu satış', 1],
            ['iş yeri elektronik eşya tasfiyesi', 1],
            ['mağaza stok fazlası alımı', 1],
            ['depo stok fazlası elektronik alımı', 1],
            ['ithalatçı stok fazlası alanlar', 1],
            ['distribütör stok fazlası alımı', 1],
            ['e ticaret iade ürünleri alan firmalar', 2],
            ['müşteri iadesi elektronik ürün alımı', 2],
            ['outlet elektronik stok alımı', 2],
            ['iflas tasfiye elektronik ürün alımı', 1],
            ['mağaza kapanış stoğu alanlar', 2],
            ['toplu ürün tasfiye alımı', 1],
            ['palet elektronik ürün alanlar', 2],
            ['karışık elektronik palet alımı', 2],
            ['lot halinde elektronik ürün alımı', 2],
            ['parti mal elektronik alımı', 2],
            ['toptan ikinci el elektronik alımı', 2],

            // Satıcı niyeti / teklif
            ['elimizdeki ürünleri toplu satmak istiyoruz', 1],
            ['toplu ürünlerimizi kime satabiliriz', 1],
            ['telefonları toplu nereye satabilirim', 1],
            ['bilgisayarları toplu nereye satabilirim', 1],
            ['tabletleri toplu nereye satabilirim', 1],
            ['robot süpürgeleri toplu nereye satabilirim', 1],
            ['stok fazlası ürünlerimi kime satabilirim', 1],
            ['depo ürünlerini toplu satmak istiyorum', 1],
            ['şirket demirbaşlarını nasıl satarım', 2],
            ['kullanılmış ofis bilgisayarlarını satmak', 1],
            ['çok sayıda ikinci el telefon satışı', 2],
            ['toplu arızalı elektronik satışı', 1],
            ['kapalı kutu elektronik ürünlerimi satmak', 1],
            ['mağaza stoğunu toplu satmak istiyorum', 1],
            ['toplu elektronik ürünler için teklif al', 1],
            ['elektronik ürünlerime toplu fiyat teklifi', 1],
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 81 il ve 973 ilçe (kaynak: data/turkiye.json — turkey-neighbourhoods, MIT).
 *
 * İÇERİK KURALI: gövde metni (content) YALNIZ elle yazılmış 10 büyük il için
 * doldurulur (data/province_content.php). Diğer il ve tüm ilçelerde content NULL
 * kalır; sayfa, AI zenginleştirici özgün metni yazana kadar noindex render edilir.
 * Şablonla çoğaltılmış gövde metni Google'da "ince içerik" sayılır.
 *
 * Seeder tekrar çalıştırılabilir: var olan kayıtların üzerine yazmaz; yalnız
 * gövdesi boş olan bir ile, elde metni varsa onu ekler.
 */
class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = json_decode(file_get_contents(__DIR__.'/data/turkiye.json'), true, flags: JSON_THROW_ON_ERROR);
        $contents = require __DIR__.'/data/province_content.php';

        DB::transaction(function () use ($regions, $contents) {
            foreach ($regions as $pi => $p) {
                $name = $p['name'];
                $slug = Str::slug($name);

                $province = Province::query()->firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'description' => $this->provinceDescription($name, $pi),
                        'content' => $contents[$slug] ?? null,
                        'faqs' => $this->provinceFaqs($name),
                        'meta_title' => $this->fit([
                            $name.' Toplu Elektronik Alımı | Telefon, Tablet, Laptop',
                            $name.' Toplu Elektronik Alımı | Telefon, Tablet',
                            $name.' Toplu Elektronik Alımı',
                        ], 60),
                        'meta_description' => $this->fit([
                            $name.' genelinde toplu telefon, tablet, bilgisayar ve robot süpürge alımı. Stok fazlası, iade ve arızalı ürünlere hızlı teklif. Hemen arayın.',
                            $name.' genelinde toplu telefon, tablet, bilgisayar ve robot süpürge alımı. Stok fazlası ve arızalı ürünlere hızlı teklif.',
                            $name.' toplu telefon, tablet ve bilgisayar alımı. Stok fazlası ve arızalı ürünlere hızlı teklif.',
                        ], 155),
                        'sort_order' => (int) $p['plate'],
                    ]
                );

                if (blank($province->content) && isset($contents[$slug])) {
                    $province->update(['content' => $contents[$slug]]);
                }

                $existing = $province->districts()->pluck('slug')->flip();

                foreach ($p['districts'] as $di => $d) {
                    $dSlug = Str::slug($d['name']);

                    if ($existing->has($dSlug)) {
                        continue;
                    }

                    District::query()->create([
                        'province_id' => $province->id,
                        'slug' => $dSlug,
                        'name' => $d['name'],
                        'description' => $this->districtDescription($d['name'], $name, $d['neighborhoods'], $pi + $di),
                        'content' => null,
                        'neighborhoods' => $d['neighborhoods'],
                        'faqs' => $this->districtFaqs($d['name'], $name),
                        'meta_title' => $this->fit([
                            $d['name'].' Toplu Elektronik Alımı | '.$name,
                            $d['name'].' Toplu Elektronik Alımı',
                            $d['name'].' Toplu Elektronik Alım',
                        ], 60),
                        'meta_description' => $this->fit([
                            $d['name'].' ('.$name.') bölgesinde toplu telefon, tablet, bilgisayar ve elektronik alımı. Stok fazlası ve arızalı ürünlere teklif. Hemen arayın.',
                            $d['name'].' ('.$name.') toplu telefon, tablet, bilgisayar ve elektronik alımı. Stok fazlası ve arızalı ürünlere teklif.',
                            $d['name'].' toplu telefon, tablet ve bilgisayar alımı. Hızlı teklif için arayın.',
                        ], 155),
                        'sort_order' => $di + 1,
                    ]);

                    $existing->put($dSlug, true);
                }
            }
        });
    }

    /** Sınırı aşmayan ilk adayı döndürür; hiçbiri sığmazsa sonuncuyu keser. */
    private function fit(array $candidates, int $max): string
    {
        foreach ($candidates as $candidate) {
            if (mb_strlen($candidate) <= $max) {
                return $candidate;
            }
        }

        return rtrim(mb_substr(end($candidates), 0, $max));
    }

    private function provinceDescription(string $province, int $i): string
    {
        $variants = [
            $province.' genelinde toplu telefon, tablet, bilgisayar ve robot süpürge alımı yapıyoruz. Ürün listenizi iletin, adet ve duruma göre fiyat teklifimizi bildirelim.',
            $province.' ilindeki bayi, mağaza, depo ve şirketlerden stok fazlası, teşhir, iade ve arızalı elektronik ürünleri toplu olarak satın alıyoruz.',
            'Elinizde '.$province.' içinde satmak istediğiniz çok sayıda telefon, laptop veya tablet varsa bizi arayın; ürün listesine göre toplu alım teklifi verelim.',
            $province.' ve ilçelerinde kurumsal demirbaş, mağaza kapanış stoğu ve depo boşaltma kapsamında toplu elektronik alımı gerçekleştiriyoruz.',
        ];

        return $variants[$i % count($variants)];
    }

    private function provinceFaqs(string $province): array
    {
        return [
            [
                'question' => $province.' genelinde hangi ürünleri toplu alıyorsunuz?',
                'answer' => 'Cep telefonu, tablet, laptop ve masaüstü bilgisayar, monitör, robot ve dikey süpürge, oyun konsolu, akıllı saat, kulaklık ve benzeri elektronik ürünleri toplu olarak alıyoruz. Sıfır stok fazlası, teşhir, iade, ikinci el ve arızalı ürünler için ayrı ayrı fiyat veriyoruz.',
            ],
            [
                'question' => $province.' ilçelerinden de alım yapıyor musunuz?',
                'answer' => 'Evet. '.$province.' merkez ve ilçelerinden toplu alım yapıyoruz. Ürün listenizi ve adresinizi paylaştığınızda teslim alma şeklini (yerinde teslim, kargo veya nakliye) birlikte planlıyoruz.',
            ],
            [
                'question' => 'Fiyat teklifi için hangi bilgiler gerekiyor?',
                'answer' => 'Ürünlerin marka ve modeli, yaklaşık adedi, durumu (sıfır, teşhir, ikinci el, arızalı) ve varsa kutu-aksesuar bilgisi yeterli. Telefonla arayarak veya WhatsApp üzerinden listeyi göndererek teklif alabilirsiniz.',
            ],
        ];
    }

    private function districtDescription(string $district, string $province, array $neighborhoods, int $i): string
    {
        $near = implode(', ', array_slice($neighborhoods, 0, 3));

        $variants = [
            $district.' ('.$province.') bölgesinde toplu telefon, tablet, bilgisayar ve robot süpürge alımı yapıyoruz. Ürün listenize göre hızlı fiyat teklifi veriyoruz.',
            $district.' ilçesindeki mağaza, depo ve işletmelerden stok fazlası, teşhir, iade ve arızalı elektronik ürünleri toplu olarak satın alıyoruz.',
            $near !== ''
                ? $district.' ilçesinde '.$near.' ve çevresi dahil toplu elektronik alımı yapıyoruz. Telefon, laptop ve tablet stoklarınız için arayın.'
                : $district.' ilçesinde toplu elektronik alımı yapıyoruz. Telefon, laptop ve tablet stoklarınız için arayın.',
            $district.', '.$province.' içinde kurumsal demirbaş, depo boşaltma ve mağaza kapanış stokları için toplu elektronik alım teklifi alın.',
        ];

        return $variants[$i % count($variants)];
    }

    private function districtFaqs(string $district, string $province): array
    {
        return [
            [
                'question' => $district.' ilçesinden toplu elektronik alımı yapıyor musunuz?',
                'answer' => 'Evet. '.$district.' ('.$province.') bölgesindeki bireysel satıcı, mağaza, depo ve şirketlerden toplu telefon, tablet, bilgisayar ve diğer elektronik ürünleri alıyoruz. Ürün listenizi ilettiğinizde teslim alma şeklini birlikte planlıyoruz.',
            ],
            [
                'question' => $district.' için fiyat teklifi nasıl alırım?',
                'answer' => 'Bizi arayın veya WhatsApp üzerinden ürünlerin marka-model, adet ve durum bilgisini gönderin. Liste netleştiğinde toplu alım fiyatımızı bildiriyoruz.',
            ],
        ];
    }
}

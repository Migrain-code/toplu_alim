<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        if (Feature::query()->exists()) {
            return;
        }

        $trust = [
            ['fa-solid fa-boxes-stacked', 'Toplu Alım', 'Onlarca, yüzlerce ya da binlerce adetlik stoklara teklif.'],
            ['fa-solid fa-bolt', 'Hızlı Teklif', 'Ürün listesini iletin, kısa sürede fiyat dönüşü alın.'],
            ['fa-solid fa-truck-ramp-box', 'Yerinden Teslim Alım', 'Depo, mağaza veya ofisinizden teslim alma planlaması.'],
            ['fa-solid fa-map-location-dot', 'Türkiye Geneli', '81 ilden toplu elektronik alım talebi değerlendiriyoruz.'],
            ['fa-solid fa-layer-group', 'Her Durumda Ürün', 'Sıfır, iade, teşhir, ikinci el ve arızalı ürünler.'],
            ['fa-solid fa-handshake', 'Net Fiyat', 'Fiyat işlemden önce konuşulur, teslimde sürpriz çıkmaz.'],
        ];

        $whyUs = [
            ['fa-solid fa-clock', 'Zaman kazandırır', 'Ürünleri tek tek satmak yerine stoğu tek seferde değerlendirirsiniz.'],
            ['fa-solid fa-list-check', 'Karışık stok sorun değil', 'Farklı marka, model ve durumdaki ürünler aynı teklifte toplanır.'],
            ['fa-solid fa-phone-volume', 'Doğrudan iletişim', 'Telefonla ya da WhatsApp\'tan doğrudan alım yapan tarafla konuşursunuz.'],
            ['fa-solid fa-building', 'Kurumsal satışa uygun', 'Şirket demirbaşı ve faturalı satışlar için süreç birlikte planlanır.'],
            ['fa-solid fa-shield-halved', 'Gizlilik ve veri', 'Telefon ve bilgisayarlardaki veriler konusunda hassas davranırız.'],
            ['fa-solid fa-scale-balanced', 'Şeffaf değerlendirme', 'Fiyatı etkileyen durumları teklif öncesinde açıkça konuşuruz.'],
        ];

        $process = [
            ['fa-solid fa-phone', 'Arayın veya Yazın', 'Telefonla arayın ya da WhatsApp\'tan toplu satış talebinizi iletin.'],
            ['fa-solid fa-clipboard-list', 'Ürün Bilgilerini İletin', 'Ürün listesi, yaklaşık adet ve durum (sıfır, iade, ikinci el, arızalı) bilgisini paylaşın.'],
            ['fa-solid fa-file-invoice-dollar', 'Fiyat Teklifi Alın', 'Bilgilere göre hazırladığımız fiyat teklifini değerlendirin.'],
            ['fa-solid fa-truck-fast', 'Teslim Alım ve Ödeme', 'Anlaşma sonrası ürünleri yerinden teslim alalım; ödeme şekli teklifle netleşir.'],
        ];

        foreach ([Feature::TYPE_TRUST => $trust, Feature::TYPE_WHY_US => $whyUs, Feature::TYPE_PROCESS => $process] as $type => $items) {
            foreach ($items as $i => [$icon, $title, $description]) {
                Feature::query()->create([
                    'type' => $type,
                    'icon' => $icon,
                    'title' => $title,
                    'description' => $description,
                    'sort_order' => $i + 1,
                ]);
            }
        }
    }
}

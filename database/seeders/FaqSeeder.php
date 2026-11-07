<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        if (Faq::query()->exists()) {
            return;
        }

        $faqs = [
            [
                'question' => 'Toplu elektronik alımında fiyat nasıl belirleniyor?',
                'answer' => 'Fiyat; ürünün marka ve modeli, durumu (sıfır, kapalı kutu, iade, teşhir, ikinci el, arızalı), kutu ve aksesuar eksiksizliği, güncel piyasa değeri ve toplam adede göre belirlenir. Ürün listenizi ilettiğinizde fiyatı işlemden önce açıkça konuşuyoruz.',
            ],
            [
                'question' => 'En az kaç adet ürün olmalı?',
                'answer' => 'Kesin bir alt sınır koymuyoruz. Hizmetimiz toplu partiler için tasarlandı; elinizdeki adet az olsa da arayıp listenizi paylaşabilirsiniz, değerlendirip size dönüş yaparız.',
            ],
            [
                'question' => 'Hangi illerden alım yapıyorsunuz?',
                'answer' => 'Türkiye\'nin 81 ilinden toplu alım taleplerini değerlendiriyoruz. Teslim alma yöntemi ürünlerin bulunduğu il, adet ve hacme göre teklifle birlikte planlanır.',
            ],
            [
                'question' => 'Arızalı ve hurda ürünleri de alıyor musunuz?',
                'answer' => 'Evet. Ekranı kırık, açılmayan, çıkma ve hurda durumdaki telefon, tablet, bilgisayar, robot süpürge ve diğer elektronik cihazları ayrı gruplar hâlinde fiyatlandırıyoruz.',
            ],
            [
                'question' => 'Ödeme ve teslim alma nasıl oluyor?',
                'answer' => 'Teklif kabul edildikten sonra ürünleri bulunduğunuz yerden teslim alma ya da gönderim seçeneklerini birlikte planlıyoruz. Ödeme şekli ve zamanlaması teklifle birlikte netleşir.',
            ],
            [
                'question' => 'Faturalı ve kurumsal satış yapabilir miyiz?',
                'answer' => 'Şirket demirbaşı ve ticari stok satışlarında fatura, envanter listesi ve teslim tutanağı gibi belgelerin nasıl düzenleneceğini teklif aşamasında birlikte belirliyoruz.',
            ],
            [
                'question' => 'Telefon ve bilgisayarlardaki veriler ne olacak?',
                'answer' => 'Satıştan önce cihazları sıfırlamanızı, hesaplardan çıkış yapmanızı ve bilgisayar disklerini silmenizi ya da sökmenizi öneriyoruz. Disksiz bilgisayar satışı da mümkündür.',
            ],
            [
                'question' => 'iCloud veya Google (FRP) kilitli cihazları alıyor musunuz?',
                'answer' => 'Kilitli cihazlar ayrı bir grup olarak değerlendirilir ve fiyatları farklıdır. Kilit durumunu teklif öncesinde belirtmeniz gerekir. Satıcının cihazların yasal sahibi olması şarttır; çalıntı veya kayıp ihbarlı cihaz kabul edilmez.',
            ],
            [
                'question' => 'Ürünleri kargo ile gönderebilir miyim?',
                'answer' => 'Adet ve konuma göre kargo ile gönderim de değerlendirilebilir. Paketleme ve gönderim koşullarını teklifle birlikte netleştiriyoruz.',
            ],
            [
                'question' => 'Teklif almak için ne göndermem gerekiyor?',
                'answer' => 'Ürünlerin marka/model listesi, yaklaşık adet ve durum bilgisi yeterlidir. Birkaç örnek fotoğraf teklifi hızlandırır. En hızlı yol bizi aramak; WhatsApp\'tan da liste gönderebilirsiniz.',
            ],
        ];

        foreach ($faqs as $i => $faq) {
            Faq::query()->create($faq + ['sort_order' => $i + 1, 'show_on_home' => $i < 6]);
        }
    }
}

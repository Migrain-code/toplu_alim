<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name' => 'Toptan Elektronik Alımı',
            'site_tagline' => 'Toplu Telefon, Tablet, Bilgisayar ve Robot Süpürge Alımı',
            'meta_title' => 'Toptan Elektronik Alımı | Toplu Telefon, Tablet, Laptop Alanlar',
            'meta_description' => 'Toplu telefon, tablet, bilgisayar, robot süpürge ve elektronik ürün alımı. Stok fazlası, iade, teşhir ve arızalı ürünler için Türkiye geneli hızlı fiyat teklifi.',
            'whatsapp_message' => 'Merhaba, elimdeki ürünler için toplu alım teklifi almak istiyorum. Bölge: {bolge}',
            'working_hours' => 'Pazartesi - Cumartesi: 09:00 - 19:00',
            'address' => '',
            'service_area_text' => 'Türkiye\'nin 81 ili',

            'hero_badge' => 'Türkiye geneli · 81 il',
            'hero_title' => 'Toptan Elektronik Alımı',
            'hero_subtitle' => 'Elinizdeki telefon, tablet, bilgisayar, robot süpürge ve diğer elektronik ürünleri toplu olarak satın alıyoruz. Sıfır, ikinci el, iade, teşhir ya da arızalı fark etmez.',
            'hero_bullets' => "Ürün listesini iletin, kısa sürede fiyat teklifi alın\nStok fazlası, depo boşaltma ve tasfiye ürünleri\nYerinden teslim alım, ödeme şekli teklifle netleşir",
            'hero_image' => '',

            'about_subtitle' => 'Hakkımızda',
            'about_title' => 'Toplu Elektronik Satışında Muhatabınız',
            'about_text' => '<p>Bayilerin, ithalatçıların, e-ticaret satıcılarının, mağazaların ve şirketlerin elinde kalan elektronik ürünleri toplu olarak satın alıyoruz. Telefon, tablet, laptop, masaüstü bilgisayar, robot süpürge, televizyon, oyun konsolu ve daha birçok ürün grubunda teklif veriyoruz.</p><p>Ürünleri tek tek satmakla uğraşmak yerine elinizdeki stoğu tek seferde değerlendirebilirsiniz. Ürün türü, yaklaşık adet ve durum bilgisini iletmeniz yeterli; fiyatı işlemden önce açıkça konuşuyoruz.</p>',
            'about_image' => '',
            'about_image_2' => '',
            'about_page_content' => '<p>Toptan Elektronik Alımı, elinde çok sayıda elektronik ürün bulunan işletme ve kişilerden toplu alım yapar. Sıfır stok fazlası, kapalı kutu, teşhir, müşteri iadesi, ikinci el ve arızalı ürünler için Türkiye genelinde fiyat teklifi veriyoruz.</p><h2>Hangi ürünleri alıyoruz?</h2><p>Cep telefonu, tablet, laptop ve masaüstü bilgisayar, monitör, robot süpürge ve dikey süpürge, televizyon, oyun konsolu, akıllı saat, kulaklık, yazıcı, kamera, network ekipmanı ve POS cihazı gibi ürünleri toplu olarak değerlendiriyoruz. Listede olmayan bir ürün grubunuz varsa sormanız yeterli.</p><h2>Kimlerden alım yapıyoruz?</h2><ul><li>Stok fazlası olan ithalatçı, distribütör ve bayiler</li><li>İade ve teşhir ürünleri biriken e-ticaret satıcıları ve mağazalar</li><li>Demirbaşlarını yenileyen şirketler ve kurumlar</li><li>Depo boşaltan, kapanan ya da tasfiye sürecindeki işletmeler</li><li>Elinde çok sayıda cihaz bulunan bireysel satıcılar</li></ul><h2>Nasıl çalışıyoruz?</h2><p>Bizi arayın veya WhatsApp\'tan yazın. Ürünlerin marka/model listesini, yaklaşık adetini ve durumunu paylaşın. Bilgilere göre fiyat teklifi hazırlıyoruz; anlaşma sağlanırsa teslim alma ve ödeme ayrıntılarını birlikte planlıyoruz. Ödeme şekli ve teslim alma yöntemi teklifle birlikte netleşir.</p>',

            'stat_1_value' => '81',
            'stat_1_label' => 'İl',
            'stat_2_value' => '973',
            'stat_2_label' => 'İlçe',
            'stat_3_value' => '12',
            'stat_3_label' => 'Ürün ve Durum Grubu',
            'stat_4_value' => 'Ücretsiz',
            'stat_4_label' => 'Fiyat Teklifi',

            'process_subtitle' => 'Nasıl Çalışıyoruz?',
            'process_title' => 'Dört Adımda Toplu Satış',
            'process_image' => '',

            'cta_title' => 'Elinizde toplu elektronik ürün mü var?',
            'cta_text' => 'Hemen arayın, ürün listenizi paylaşın; kısa sürede fiyat teklifimizi iletelim.',
            'cta_image' => '',
            'banner_image' => '',

            'footer_text' => 'Toplu telefon, tablet, bilgisayar, robot süpürge ve elektronik ürün alımı. Türkiye\'nin 81 ilinden teklif veriyoruz.',

            'facebook_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',
            'map_embed' => '',
            'google_analytics_id' => '',
            'google_site_verification' => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::query()->firstOrCreate(['key' => $key], ['value' => $value]);
        }

        Setting::flush();
    }
}

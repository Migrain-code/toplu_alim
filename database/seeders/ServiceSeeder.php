<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Hizmet sayfaları: 6 ÜRÜN KATEGORİSİ + 6 ÜRÜN DURUMU.
 *
 * Kelime sahipliği (tek sahip kuralı): her baş kelime yalnız BİR sayfaya aittir.
 *  - Ana sayfa: "toptan elektronik alımı", "toplu elektronik alanlar"
 *  - Kategori sayfaları: "toplu {ürün} alımı / alanlar / alan firmalar"
 *  - Durum sayfaları: "stok fazlası…", "depo boşaltma", "tasfiye…", "arızalı/hurda…",
 *    "teşhir/iade/kapalı kutu…", "ikinci el … toplu alımı / lot / palet / parti mal"
 *
 * İÇERİK KURALI: uydurma bilgi yok (kuruluş yılı, müşteri sayısı, depo adresi,
 * kesin ödeme süresi, fiyat). Ödeme şekli "teklifle netleşir" diye anlatılır.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->services() as $i => $data) {
            $group = $data['group'];
            unset($data['group']);

            $service = Service::query()->firstOrCreate(
                ['slug' => $data['slug']],
                $data + [
                    'sort_order' => $i + 1,
                    'is_active' => true,
                    'is_featured' => $group === 'category',
                ]
            );

            // "group" modelin fillable listesinde olmayabilir; doğrudan yazılır.
            if ($service->getAttribute('group') !== $group) {
                $service->forceFill(['group' => $group])->save();
            }
        }
    }

    private function steps(string $what, string $info): array
    {
        return [
            ['title' => 'Arayın veya yazın', 'description' => 'Telefonla arayın ya da WhatsApp\'tan '.$what.' talebinizi iletin.'],
            ['title' => 'Ürün bilgilerini paylaşın', 'description' => $info],
            ['title' => 'Fiyat teklifini değerlendirin', 'description' => 'Paylaştığınız bilgilere göre hazırladığımız teklifi inceleyin; fiyatı etkileyen her durumu önceden konuşuruz.'],
            ['title' => 'Teslim alım ve ödeme', 'description' => 'Anlaşma sağlanırsa ürünleri yerinden teslim alırız. Ödeme şekli ve teslim alma yöntemi teklifle birlikte netleşir.'],
        ];
    }

    private function services(): array
    {
        return [
            // ------------------------------------------------------------------
            // KATEGORİLER
            // ------------------------------------------------------------------
            [
                'group' => 'category',
                'title' => 'Toplu Telefon Alımı',
                'slug' => 'toplu-telefon-alimi',
                'icon' => 'fa-solid fa-mobile-screen-button',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'Sıfır, ikinci el, iade ve arızalı cep telefonlarını toplu olarak satın alıyoruz.',
                'description' => '<p>Elinizde çok sayıda cep telefonu varsa onları tek tek ilana koymak, her alıcıyla ayrı ayrı pazarlık yapmak hem zaman alır hem de stoğun değer kaybetmesine yol açar. <strong>Toplu telefon alımı</strong> hizmetimizle bayilerin, mağazaların, ithalatçıların ve şirketlerin elindeki telefonları tek seferde değerlendiriyoruz. Toplu telefon alan firmalar arasında aradığınız muhatap, ürün listenizi görüp net fiyat konuşabilen biriyse doğru yerdesiniz.</p>
<h2>Hangi telefonları alıyoruz?</h2>
<p>iPhone, Samsung, Xiaomi, Oppo, Huawei, Realme ve diğer markaların akıllı telefonlarını toplu olarak alıyoruz. Kapalı kutu sıfır telefon stok fazlası, mağaza telefon stoğu, teşhir ve müşteri iadesi cihazlar, ikinci el telefonlar ve ekranı kırık ya da arızalı telefonlar aynı teklifte değerlendirilebilir. Farklı model ve durumdaki cihazlardan oluşan karışık bir telefon partisi de sorun değildir; listeyi gruplara ayırarak her grup için ayrı fiyat çıkarırız.</p>
<h2>Kimler telefonlarını toplu satıyor?</h2>
<p>Telefon deposu boşaltmak isteyen distribütörler, model değişikliği nedeniyle eski stoğunu eritmek isteyen bayiler, şirket telefonlarını toplu satmak isteyen kurumlar ve "elimde çok sayıda telefon var, nereye satabilirim" diye düşünen bireysel satıcılar en sık karşılaştığımız taleplerdir. Kurumsal telefon alımında cihazların fatura ve teslim süreçlerini birlikte planlıyoruz.</p>
<h2>Fiyat neye göre belirlenir?</h2>
<p>Toptan telefon alımında fiyatı; marka ve model, hafıza kapasitesi, kutulu olup olmadığı, ekran ve kasa durumu, pil sağlığı, garanti durumu ve toplam adet belirler. iCloud veya Google hesabı (FRP) kilidi açılmamış cihazlar ayrıca değerlendirilir. Bize marka/model, adet ve durum bilgisini içeren bir liste iletmeniz yeterli; mümkünse birkaç örnek fotoğraf da teklifi hızlandırır.</p>
<h2>Teklifi hızlandıran bilgiler</h2>
<p>Telefon listesini hazırlarken her satıra marka, model, hafıza kapasitesi, renk ve adet yazmanız; kutulu, açık kutu, ikinci el ve arızalı cihazları ayrı başlıklarda toplamanız teklifi belirgin biçimde hızlandırır. İkinci el cihazlarda ekran ve kasa durumunu "temiz, hafif çizik, belirgin hasar" gibi kısa ifadelerle belirtmeniz yeterlidir. Pil sağlığı yüzdesi biliniyorsa eklemeniz fiyatın daha isabetli çıkmasını sağlar. Garanti süresi devam eden ürünlerde garanti başlangıç tarihi de önemlidir. Liste hazırlamaya vaktiniz yoksa telefon kutularının ya da cihazların toplu fotoğrafını göndermeniz ilk değerlendirme için yeterlidir; ayrıntıları görüşme sırasında birlikte netleştiririz. Telefonları toplu nereye satabilirim diye düşünüyorsanız, tek bir görüşmeyle bütün partiye fiyat almanın ürünleri parça parça satmaktan çok daha az zaman aldığını göreceksiniz.</p>
<h2>Nasıl ilerliyoruz?</h2>
<p>Hemen arayın ya da WhatsApp\'tan yazın. Listenizi inceleyip kısa sürede fiyat teklifimizi iletiyoruz. Anlaşma sağlanırsa telefonları bulunduğunuz yerden teslim alıyoruz; ödeme şekli ve teslim yöntemi teklifle birlikte netleşir. Türkiye\'nin 81 ilinden toplu telefon satmak isteyenlerin taleplerini değerlendiriyoruz.</p>',
                'what_we_do' => [
                    'Kapalı kutu sıfır telefon stok fazlası',
                    'Mağaza ve bayi telefon stoğu',
                    'Teşhir, iade ve outlet telefonlar',
                    'İkinci el telefonların toplu alımı',
                    'Ekranı kırık ve arızalı telefonlar',
                    'iPhone, Samsung ve Xiaomi telefon partileri',
                ],
                'suitable_for' => [
                    'Telefon bayileri ve GSM mağazaları',
                    'İthalatçı ve distribütörler',
                    'Şirket telefonlarını yenileyen kurumlar',
                    'E-ticaret satıcıları',
                    'Elinde çok sayıda telefon bulunan bireysel satıcılar',
                ],
                'process_steps' => $this->steps('toplu telefon satış', 'Marka/model, hafıza, adet ve durum (kutulu, ikinci el, arızalı) bilgisini liste hâlinde iletin; birkaç örnek fotoğraf teklifi hızlandırır.'),
                'faqs' => [
                    ['question' => 'Toplu telefon alımında en az kaç adet olmalı?', 'answer' => 'Kesin bir alt sınır koymuyoruz. Toplu alım hizmetimiz onlarca ve yüzlerce adetlik partiler için tasarlandı; daha küçük adetler için de arayıp listenizi paylaşabilirsiniz.'],
                    ['question' => 'iCloud veya FRP kilitli telefonları alıyor musunuz?', 'answer' => 'Kilitli cihazlar ayrı bir grup olarak değerlendirilir ve fiyatları farklıdır. Teklif öncesinde kilit durumunu belirtmeniz gerekir; satıcının cihazın yasal sahibi olması şarttır.'],
                    ['question' => 'Farklı model ve durumdaki telefonları birlikte satabilir miyim?', 'answer' => 'Evet. Karışık telefon partilerini model ve durum gruplarına ayırarak her grup için fiyat çıkarıyor, tek bir teklifte topluyoruz.'],
                    ['question' => 'Telefonlardaki veriler ne olacak?', 'answer' => 'Satıştan önce cihazları sıfırlamanızı ve hesaplardan çıkış yapmanızı öneriyoruz. Sıfırlanamayan cihazlar için veri silme konusunu teklif aşamasında konuşuruz.'],
                    ['question' => 'Telefonları kargo ile gönderebilir miyim?', 'answer' => 'Teslim yöntemi teklifle birlikte belirlenir. Bulunduğunuz il ve adet durumuna göre yerinden teslim alma ya da gönderim seçeneklerini birlikte planlarız.'],
                ],
                'meta_title' => 'Toplu Telefon Alımı | Toplu Telefon Alan Firmalar',
                'meta_description' => 'Sıfır, ikinci el, iade ve arızalı telefonlarınızı toplu satın alıyoruz. iPhone, Samsung, Xiaomi telefon partileri için hemen arayın, hızlı teklif alın.',
            ],
            [
                'group' => 'category',
                'title' => 'Toplu Bilgisayar ve Laptop Alımı',
                'slug' => 'toplu-bilgisayar-alimi',
                'icon' => 'fa-solid fa-laptop',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'Laptop, masaüstü, monitör ve bilgisayar parçalarını toplu olarak satın alıyoruz.',
                'description' => '<p>Ofis yenilemesi, stok fazlası ya da kapanan bir iş yeri… Elinizde çok sayıda bilgisayar biriktiğinde onları tek seferde değerlendirmek en pratik yoldur. <strong>Toplu bilgisayar alımı</strong> hizmetimizle laptop, notebook, masaüstü bilgisayar, oyuncu bilgisayarı, monitör ve bilgisayar parçalarını toplu olarak satın alıyoruz. Bilgisayarları toplu alan yerler arasında listenizi inceleyip net teklif veren bir muhatap arıyorsanız bizi arayın.</p>
<h2>Hangi bilgisayarları alıyoruz?</h2>
<p>Lenovo, HP, Dell, Asus, Acer, Apple MacBook, Monster ve diğer markaların laptop ve masaüstü bilgisayarlarını alıyoruz. Sıfır bilgisayar stok fazlası, kutulu ürünler, ikinci el laptoplar, ofis bilgisayarları, oyuncu bilgisayarları, bilgisayar kasaları, monitörler ve ekran kartı, işlemci, RAM, SSD gibi bilgisayar parçaları toplu olarak değerlendirilir. Arızalı bilgisayarlar da ayrı bir grup olarak teklife eklenebilir.</p>
<h2>Kimler bilgisayarlarını toplu satıyor?</h2>
<p>Toplu laptop alanlar arayan bilgisayar mağazaları, bilgisayar depo boşaltma ihtiyacı olan distribütörler, bilgisayarlarımı toplu satmak istiyorum diyen bireysel satıcılar ve internet kafe, oyun salonu gibi çok sayıda cihaz kullanan işletmeler en sık çalıştığımız gruplardır. Şirket bilgisayarlarını toplu satmak isteyen kurumlar için ayrıca <a href="/kurumsal-elektronik-alimi">kurumsal elektronik ve IT ekipmanı alımı</a> sayfamıza göz atabilirsiniz.</p>
<h2>Fiyatı neler etkiler?</h2>
<p>Toptan bilgisayar alımında fiyatı; işlemci nesli, RAM ve depolama kapasitesi, ekran kartı, kasa ve ekran durumu, pil sağlığı (laptoplarda), şarj aleti ve kutu durumu ile toplam adet belirler. Aynı model cihazlardan oluşan partiler daha hızlı fiyatlanır. Model listesi ve kısa bir durum açıklaması teklif için çoğu zaman yeterlidir.</p>
<h2>Liste nasıl hazırlanır?</h2>
<p>Bilgisayar partilerinde teklifi en çok hızlandıran şey düzenli bir envanter listesidir. Her satıra marka ve model, işlemci (örneğin Intel Core i5 8. nesil ya da Ryzen 5), RAM, disk tipi ve kapasitesi (HDD/SSD), ekran boyutu ve adet yazmanız yeterlidir. Laptoplarda şarj aleti olup olmadığını, ekranda leke veya çizik bulunup bulunmadığını ve klavye durumunu kısa bir notla belirtin. Masaüstü bilgisayarlarda kasaların monitörle birlikte mi ayrı mı satılacağını yazmanız önemlidir. Oyuncu bilgisayarlarında ekran kartı modeli fiyatı en çok etkileyen unsurdur. Envanteriniz bir yazılımdan dışa aktarılmış bir tablo ise doğrudan o dosyayı gönderebilirsiniz. Bilgisayarları toplu nereye satabilirim sorusunun en kısa cevabı, listeyi tek seferde fiyatlayabilen bir alıcıyla görüşmektir.</p>
<h2>Veri güvenliği</h2>
<p>Bilgisayarlar genellikle hassas veriler taşır. Satış öncesinde diskleri temizlemenizi ya da sökmenizi öneriyoruz; disksiz satış da teklif aşamasında konuşulabilir. Hemen arayın, listenizi paylaşın; ödeme şekli ve teslim alma yöntemi teklifle birlikte netleşir.</p>',
                'what_we_do' => [
                    'Laptop ve notebook toplu alımı',
                    'Masaüstü bilgisayar ve kasa alımı',
                    'Oyuncu bilgisayarları',
                    'Monitör toplu alımı',
                    'Ekran kartı, işlemci, RAM, SSD gibi bilgisayar parçaları',
                    'Sıfır bilgisayar stok fazlası ve arızalı bilgisayarlar',
                ],
                'suitable_for' => [
                    'Bilgisayar mağazaları ve bayileri',
                    'Distribütör ve ithalatçılar',
                    'İnternet kafe ve oyun salonları',
                    'Ofis bilgisayarlarını yenileyen işletmeler',
                    'Elinde çok sayıda bilgisayar olan bireysel satıcılar',
                ],
                'process_steps' => $this->steps('toplu bilgisayar satış', 'Marka/model, işlemci, RAM, depolama, adet ve durum bilgisini liste hâlinde iletin; arızalı cihazları ayrıca belirtin.'),
                'faqs' => [
                    ['question' => 'Disksiz bilgisayar satabilir miyim?', 'answer' => 'Evet. Veri güvenliği nedeniyle diskleri sökerek satmak isteyebilirsiniz; disksiz cihazlar buna göre fiyatlandırılır.'],
                    ['question' => 'Eski nesil bilgisayarları da alıyor musunuz?', 'answer' => 'Eski nesil cihazlar da değerlendirilir. Fiyat işlemci nesli ve genel duruma göre belirlenir; listenizi görmeden kesin bir şey söylemek doğru olmaz.'],
                    ['question' => 'Sadece bilgisayar parçası satabilir miyim?', 'answer' => 'Evet. Ekran kartı, işlemci, RAM, SSD ve anakart gibi bilgisayar parçalarını da toplu olarak alıyoruz.'],
                    ['question' => 'Monitörleri bilgisayarlarla birlikte satabilir miyim?', 'answer' => 'Evet, monitör, klavye-mouse ve diğer çevre birimleri aynı teklifte değerlendirilebilir.'],
                ],
                'meta_title' => 'Toplu Bilgisayar ve Laptop Alımı | Toplu Laptop Alanlar',
                'meta_description' => 'Laptop, masaüstü bilgisayar, monitör ve bilgisayar parçalarını toplu satın alıyoruz. Stok fazlası, ikinci el ve arızalı cihazlar için hemen teklif alın.',
            ],
            [
                'group' => 'category',
                'title' => 'Toplu Tablet Alımı',
                'slug' => 'toplu-tablet-alimi',
                'icon' => 'fa-solid fa-tablet-screen-button',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'iPad, Samsung, Lenovo ve diğer marka tabletleri toplu olarak satın alıyoruz.',
                'description' => '<p>Tabletler eğitim projelerinde, satış ekiplerinde, restoranlarda ve depolarda toplu hâlde kullanılır; proje bittiğinde ya da cihazlar yenilendiğinde elde onlarca tablet kalır. <strong>Toplu tablet alımı</strong> hizmetimizle bu cihazları tek seferde değerlendiriyoruz. Toplu tablet alan firmalar arasında hızlı ve açık teklif veren bir muhatap arıyorsanız ürün listenizi bize iletin.</p>
<h2>Hangi tabletleri alıyoruz?</h2>
<p>Apple iPad (iPad, iPad Air, iPad Pro, iPad mini), Samsung Galaxy Tab, Lenovo Tab, Huawei MatePad, Xiaomi Pad ve diğer marka tabletleri alıyoruz. Kutulu sıfır tablet stok fazlası, teşhir ve iade tabletler, ikinci el tabletler, ekranı kırık ya da arızalı tabletler ayrı gruplar hâlinde fiyatlandırılır. Hücresel (SIM kartlı) ve yalnız Wi-Fi modeller farklı değerlendirilir.</p>
<h2>Kimler tabletlerini toplu satıyor?</h2>
<p>Tablet depo boşaltma ihtiyacı olan distribütörler, tablet stok fazlası olan bayiler, şirket tabletlerini toplu satmak isteyen kurumlar, eğitim ve saha projelerini tamamlayan firmalar ve "tabletlerimi toplu satmak istiyorum" diyen bireysel satıcılar en sık karşılaştığımız taleplerdir. Kurumsal tablet alımında cihaz yönetimi (MDM) kayıtlarının kaldırılması gerekir; bunu teklif aşamasında birlikte planlarız.</p>
<h2>Fiyatı neler etkiler?</h2>
<p>Toptan tablet alımında fiyatı; model ve nesil, depolama kapasitesi, hücresel özellik, ekran ve kasa durumu, pil sağlığı, kalem/klavye gibi aksesuarlar ve toplam adet belirler. Apple Kimliği ya da Google hesabı kilidi açık olmayan tabletler ayrıca değerlendirilir. Model listesi ve durum bilgisi teklif için yeterlidir; örnek fotoğraflar süreci hızlandırır.</p>
<h2>Satıştan önce yapmanız gerekenler</h2>
<p>Tabletleri teslim etmeden önce her cihazda Apple Kimliği veya Google hesabından çıkış yapılması, "iPad\'imi Bul" ya da cihaz koruma özelliğinin kapatılması ve cihazın fabrika ayarlarına döndürülmesi gerekir. Kurumsal tabletlerde bu işlemler genellikle cihaz yönetim panelinden toplu olarak yapılabilir. Aksesuarları (kalem, klavye kılıf, şarj aleti) cihazlarla eşleştirerek gruplamanız, sayım ve teslim aşamasını kolaylaştırır. Ekranında kırık, ölü piksel veya dokunmatik sorunu olan tabletleri ayrı bir grupta toplamanız da fiyatlandırmayı netleştirir. Tabletleri toplu nereye satabilirim diye araştırıyorsanız, bu hazırlıkları yaptıktan sonra tek bir görüşmeyle bütün partiye teklif alabilirsiniz. Hazırlığı tamamlayamadığınız durumlarda da önce listeyi paylaşın; eksik adımları birlikte planlarız.</p>
<h2>Satış süreci</h2>
<p>Hemen arayın ya da WhatsApp\'tan yazın, listenizi paylaşın. Kısa sürede fiyat teklifimizi iletiyoruz; anlaşma sağlanırsa tabletleri bulunduğunuz yerden teslim alıyoruz. Ödeme şekli ve teslim yöntemi teklifle birlikte netleşir. Türkiye\'nin 81 ilinden toplu tablet satışı taleplerini değerlendiriyoruz.</p>',
                'what_we_do' => [
                    'iPad toplu alımı',
                    'Samsung ve Lenovo tablet toplu alımı',
                    'Sıfır tablet stok fazlası',
                    'Teşhir ve iade tabletler',
                    'İkinci el tabletler',
                    'Arızalı ve ekranı kırık tabletler',
                ],
                'suitable_for' => [
                    'Tablet bayileri ve distribütörler',
                    'Eğitim ve saha projelerini tamamlayan firmalar',
                    'Restoran, otel ve perakende zincirleri',
                    'Şirket tabletlerini yenileyen kurumlar',
                    'E-ticaret satıcıları',
                ],
                'process_steps' => $this->steps('toplu tablet satış', 'Marka/model, depolama, hücresel/Wi-Fi bilgisi, adet ve durum bilgisini iletin; kilitli cihaz varsa belirtin.'),
                'faqs' => [
                    ['question' => 'MDM kayıtlı kurumsal tabletleri alıyor musunuz?', 'answer' => 'Evet, ancak cihazların kurumsal yönetim (MDM) kaydından çıkarılması gerekir. Bu adımı teklif sürecinde birlikte planlıyoruz.'],
                    ['question' => 'Kalem ve klavye kılıfları fiyatı etkiler mi?', 'answer' => 'Evet. Orijinal kalem, klavye ve şarj aleti gibi aksesuarlar teklifte ayrıca değerlendirilir.'],
                    ['question' => 'Ekranı kırık tabletleri alıyor musunuz?', 'answer' => 'Evet. Ekranı kırık ve arızalı tabletler ayrı bir grup olarak fiyatlandırılır.'],
                    ['question' => 'Eski model iPad\'leri alıyor musunuz?', 'answer' => 'Eski modeller de değerlendirilir; fiyat model, nesil ve genel duruma göre belirlenir.'],
                ],
                'meta_title' => 'Toplu Tablet Alımı | iPad, Samsung Tablet Alanlar',
                'meta_description' => 'iPad, Samsung, Lenovo tabletlerinizi toplu satın alıyoruz. Stok fazlası, iade, ikinci el ve arızalı tabletler için hemen arayın, hızlı fiyat teklifi alın.',
            ],
            [
                'group' => 'category',
                'title' => 'Toplu Robot Süpürge Alımı',
                'slug' => 'toplu-robot-supurge-alimi',
                'icon' => 'fa-solid fa-robot',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'Robot süpürge, dikey ve şarjlı süpürgeleri toplu olarak satın alıyoruz.',
                'description' => '<p>Robot süpürgeler son yıllarda en çok satılan küçük ev aletleri arasına girdi; bununla birlikte satıcıların elinde stok fazlası, iade ve teşhir ürünler de birikmeye başladı. <strong>Toplu robot süpürge alımı</strong> hizmetimizle robot süpürge, dikey süpürge, şarjlı süpürge ve elektrikli süpürgeleri toplu olarak satın alıyoruz. Toptan robot süpürge alımı yapan bir muhatap arıyorsanız ürün listenizi bize iletin.</p>
<h2>Hangi süpürgeleri alıyoruz?</h2>
<p>Xiaomi, Roborock, Dreame, Viomi, iRobot (Roomba), Ecovacs, Philips, Samsung ve diğer markaların robot süpürgelerini; Dyson, Xiaomi, Philips, Karcher ve diğer markaların dikey ve şarjlı süpürgelerini alıyoruz. Kutulu sıfır robot süpürge stok fazlası, teşhir ve iade ürünler, ikinci el cihazlar ve arızalı robot süpürgeler ayrı gruplar hâlinde değerlendirilir. İstasyonlu (otomatik boşaltmalı, paspas yıkamalı) modeller ayrıca fiyatlandırılır.</p>
<h2>Kimler robot süpürgelerini toplu satıyor?</h2>
<p>Robot süpürge depo boşaltma ihtiyacı olan ithalatçı ve distribütörler, robot süpürgelerimi toplu satmak istiyorum diyen e-ticaret satıcıları, iade ürünlerini eritmek isteyen pazaryeri mağazaları ve stok yenileyen beyaz eşya / elektronik mağazaları en sık çalıştığımız gruplardır. Robot süpürge lot alımı ve karışık marka partileri de değerlendirilir.</p>
<h2>Fiyatı neler etkiler?</h2>
<p>Toplu robot süpürge alımında fiyatı; marka ve model, istasyon tipi, kutu ve aksesuar durumu (yan fırça, filtre, şarj ünitesi), pil sağlığı, uygulama hesabından çıkarılıp çıkarılmadığı ve toplam adet belirler. Arızalı robot süpürgeler ve arızalı dikey süpürgeler için arızanın kısa açıklaması teklifi kolaylaştırır.</p>
<h2>Robot süpürge ve dikey süpürgede farklar</h2>
<p>Robot süpürgelerde fiyatı en çok etkileyen unsur istasyon tipidir: basit şarj ünitesiyle gelen modeller, otomatik toz boşaltma istasyonlu modeller ve paspas yıkayıp kurutan çok işlevli istasyonlu modeller birbirinden farklı değerlendirilir. Lidar (lazer) navigasyonlu cihazlar kameralı ya da jiroskoplu modellerden ayrı gruplanır. Dikey ve şarjlı süpürgelerde ise batarya sağlığı, başlık ve aparat eksiksizliği ile duvar askı aparatı fiyatı etkiler. Dyson gibi yüksek segment dikey süpürgelerde model ve nesil bilgisinin doğru verilmesi önemlidir. Elektrikli süpürge toplu alımında kablolu modeller ve torbalı / torbasız ayrımı da teklifte dikkate alınır. Robot süpürgeleri toplu nereye satabilirim diye düşünüyorsanız, farklı türleri tek listede toplayıp göndermeniz yeterlidir.</p>
<h2>Nasıl satarsınız?</h2>
<p>Hemen arayın ya da WhatsApp\'tan yazın; marka/model, adet ve durum listenizi paylaşın. Kısa sürede fiyat teklifimizi iletiyoruz. Anlaşma sağlanırsa ürünleri deponuzdan veya mağazanızdan teslim alıyoruz; ödeme şekli ve teslim yöntemi teklifle birlikte netleşir.</p>',
                'what_we_do' => [
                    'Robot süpürge toplu alımı',
                    'Xiaomi, Roborock, Dreame, Viomi ve iRobot partileri',
                    'Dyson ve diğer dikey süpürgeler',
                    'Şarjlı ve elektrikli süpürgeler',
                    'İade, teşhir ve kapalı kutu robot süpürgeler',
                    'Arızalı robot süpürge ve dikey süpürgeler',
                ],
                'suitable_for' => [
                    'Robot süpürge ithalatçı ve distribütörleri',
                    'E-ticaret ve pazaryeri satıcıları',
                    'Elektronik ve beyaz eşya mağazaları',
                    'Depo boşaltan işletmeler',
                    'Elinde çok sayıda cihaz bulunan satıcılar',
                ],
                'process_steps' => $this->steps('toplu robot süpürge satış', 'Marka/model, istasyon tipi, adet ve durum (kutulu, iade, ikinci el, arızalı) bilgisini iletin.'),
                'faqs' => [
                    ['question' => 'İstasyonu eksik robot süpürgeleri alıyor musunuz?', 'answer' => 'Evet. Eksik istasyon veya aksesuar fiyatı etkiler; teklif öncesinde eksikleri belirtmeniz yeterli.'],
                    ['question' => 'Dyson süpürgeleri toplu alıyor musunuz?', 'answer' => 'Evet. Dyson ve diğer markaların dikey, şarjlı ve kablolu süpürgelerini toplu olarak değerlendiriyoruz.'],
                    ['question' => 'Uygulama hesabına bağlı cihazlar ne olacak?', 'answer' => 'Satış öncesinde robot süpürgelerin uygulama hesaplarından çıkarılmasını ve sıfırlanmasını öneriyoruz.'],
                    ['question' => 'Arızalı robot süpürgeleri alıyor musunuz?', 'answer' => 'Evet. Arızalı cihazlar ayrı grup olarak fiyatlandırılır; arızanın kısa açıklaması teklifi hızlandırır.'],
                ],
                'meta_title' => 'Toplu Robot Süpürge Alımı | Robot Süpürge Alanlar',
                'meta_description' => 'Xiaomi, Roborock, Dreame, iRobot robot süpürge ve Dyson dikey süpürgeleri toplu satın alıyoruz. Stok fazlası, iade ve arızalı ürünler için teklif alın.',
            ],
            [
                'group' => 'category',
                'title' => 'Toplu Elektronik Cihaz Alımı',
                'slug' => 'toplu-elektronik-cihaz-alimi',
                'icon' => 'fa-solid fa-tv',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'TV, konsol, akıllı saat, kulaklık, kamera, yazıcı ve diğer cihazları toplu alıyoruz.',
                'description' => '<p>Telefon, tablet ve bilgisayarın dışında kalan elektronik ürünler de toplu olarak değerlendirilebilir. <strong>Toplu elektronik cihaz alımı</strong> hizmetimizle televizyon, monitör, oyun konsolu, akıllı saat, kulaklık, hoparlör, kamera, fotoğraf makinesi, yazıcı, fotokopi makinesi, projeksiyon cihazı, network ekipmanı, POS cihazı ve barkod okuyucu gibi ürünleri toplu olarak satın alıyoruz. Toplu elektronik eşya alımı yapan bir muhatap arıyorsanız listenizi bize gönderin.</p>
<h2>Hangi ürün gruplarını alıyoruz?</h2>
<ul><li><strong>Görüntü:</strong> televizyon toplu alımı, monitör, projeksiyon cihazı</li><li><strong>Oyun:</strong> PlayStation, Xbox, Nintendo Switch ve oyun konsolu toplu alımı, kol ve aksesuarlar</li><li><strong>Giyilebilir ve ses:</strong> akıllı saat, kulaklık, hoparlör</li><li><strong>Fotoğraf ve güvenlik:</strong> kamera, fotoğraf makinesi, güvenlik kamerası</li><li><strong>Ofis:</strong> yazıcı, fotokopi makinesi, barkod okuyucu, POS cihazı</li><li><strong>Ağ:</strong> modem, router, switch ve diğer network ekipmanları</li><li><strong>Parça:</strong> elektronik kart ve elektronik parça toplu alımı</li></ul>
<h2>Karışık stoklar</h2>
<p>Elinizdeki stok tek bir ürün grubundan oluşmak zorunda değil. Farklı kategorilerden ürünlerin bir arada bulunduğu stoklar için ürünleri gruplara ayırarak tek bir teklif hazırlıyoruz. Toplu elektronik cihaz alanlar arasında karışık stoğa tek seferde fiyat verebilen bir muhatap bulmak satış sürecini ciddi biçimde kısaltır.</p>
<h2>Fiyatı neler etkiler?</h2>
<p>Fiyatı ürünün marka ve modeli, kutu ve aksesuar durumu, sıfır / iade / ikinci el / arızalı olması, üretim yılı ve toplam adet belirler. Televizyon gibi hacimli ürünlerde teslim alma koşulları da teklife yansır. Marka/model listesi, adet ve kısa bir durum açıklaması teklif için yeterlidir.</p>
<h2>Ürün grubuna göre dikkat edilecekler</h2>
<p>Oyun konsollarında hesapların kapatılması ve konsolun sıfırlanması, akıllı saatlerde eşleştirilmiş telefondan ayrılması gerekir. Televizyonlarda kumanda, ayak ve duvar aparatının eksik olup olmadığı fiyatı etkiler; panelde çizik veya kırık bulunan ürünler ayrı grupta değerlendirilir. Yazıcı ve fotokopi makinelerinde sayaç bilgisi ve sarf malzemesi durumu, kameralarda deklanşör sayısı ve lens dahil olup olmadığı önemlidir. Network ekipmanlarında model ve port sayısı, POS cihazlarında ise kayıtlı oldukları kurumdan çıkışlarının yapılıp yapılmadığı belirleyicidir. Bu bilgiler elinizde değilse sorun değil; ürün listesi ve birkaç fotoğrafla ilk değerlendirmeyi yapar, eksik noktaları görüşme sırasında tamamlarız.</p>
<h2>Teklif alın</h2>
<p>Hemen arayın ya da WhatsApp\'tan yazın. Listenizi inceleyip kısa sürede fiyat teklifimizi iletiyoruz; anlaşma sağlanırsa ürünleri yerinden teslim alıyoruz. Ödeme şekli ve teslim yöntemi teklifle birlikte netleşir.</p>',
                'what_we_do' => [
                    'Televizyon ve monitör toplu alımı',
                    'PlayStation, Xbox ve Nintendo Switch konsollar',
                    'Akıllı saat, kulaklık ve hoparlör',
                    'Kamera, fotoğraf makinesi ve güvenlik kamerası',
                    'Yazıcı, fotokopi, POS cihazı ve barkod okuyucu',
                    'Modem, router ve network ekipmanı',
                ],
                'suitable_for' => [
                    'Elektronik mağazaları ve zincirler',
                    'İthalatçı ve distribütörler',
                    'E-ticaret satıcıları',
                    'Otel, ofis ve perakende işletmeleri',
                    'Karışık elektronik stoğu olan satıcılar',
                ],
                'process_steps' => $this->steps('toplu elektronik cihaz satış', 'Ürün grubu, marka/model, adet ve durum bilgisini liste hâlinde iletin; hacimli ürünlerde bulunduğu yeri belirtin.'),
                'faqs' => [
                    ['question' => 'Farklı kategorideki ürünleri tek seferde satabilir miyim?', 'answer' => 'Evet. Karışık stokları ürün gruplarına ayırarak tek bir teklifte topluyoruz.'],
                    ['question' => 'Televizyonları nasıl teslim alıyorsunuz?', 'answer' => 'Hacimli ürünlerde teslim alma koşulları adet ve konuma göre planlanır; ayrıntılar teklifle birlikte netleşir.'],
                    ['question' => 'Oyun konsollarını aksesuarsız satabilir miyim?', 'answer' => 'Evet. Kol, kablo ve kutu durumu fiyatı etkiler; eksikleri belirtmeniz yeterli.'],
                    ['question' => 'POS cihazlarını alıyor musunuz?', 'answer' => 'POS cihazları ve barkod okuyucular da toplu olarak değerlendirilir. Banka veya servis sağlayıcıya bağlı cihazlarda kayıt durumunun netleşmesi gerekir.'],
                ],
                'meta_title' => 'Toplu Elektronik Cihaz Alımı | TV, Konsol, Kamera',
                'meta_description' => 'TV, monitör, oyun konsolu, akıllı saat, kulaklık, kamera, yazıcı ve network ekipmanlarını toplu satın alıyoruz. Karışık stoklar için hemen teklif alın.',
            ],
            [
                'group' => 'category',
                'title' => 'Kurumsal Elektronik ve IT Ekipmanı Alımı',
                'slug' => 'kurumsal-elektronik-alimi',
                'icon' => 'fa-solid fa-building',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'Şirket demirbaşı bilgisayar, telefon, sunucu ve IT ekipmanlarını toplu alıyoruz.',
                'description' => '<p>Şirketler belirli aralıklarla bilgisayar, telefon ve IT altyapılarını yeniler; geride değerlendirilmeyi bekleyen çok sayıda demirbaş kalır. <strong>Kurumsal elektronik alımı</strong> hizmetimizle şirket bilgisayarları, şirket telefonları, sunucular, iş istasyonları, monitörler, yazıcılar ve network ekipmanları gibi kurumsal IT ekipmanlarını toplu olarak satın alıyoruz. Şirket demirbaşlarını satın alan firmalar arasında süreci düzenli yürütebilecek bir muhatap arıyorsanız bizimle iletişime geçin.</p>
<h2>Hangi ekipmanları alıyoruz?</h2>
<p>Ofis bilgisayarları (laptop ve masaüstü), iş istasyonları, sunucu ve server ekipmanları, depolama üniteleri, switch ve router gibi network cihazları, IP telefonlar, kurumsal cep telefonları ve tabletler, monitörler, yazıcı ve fotokopi makineleri, projeksiyon cihazları ve diğer ofis elektronik eşyaları kurumsal IT ekipmanı alımı kapsamında değerlendirilir.</p>
<h2>Kurumsal satışta süreç</h2>
<p>Kurumsal satışlarda envanter listesi, fatura, teslim tutanağı ve veri güvenliği gibi konular bireysel satıştan farklıdır. Envanterinizi (marka/model, adet, yaş ve durum) paylaşmanız teklif için yeterlidir. Faturalı satış ve teslim belgelerinin nasıl düzenleneceğini teklif aşamasında birlikte planlıyoruz. İş yeri elektronik eşya tasfiyesi gibi kapsamlı işlerde teslim alma günlerini iş akışınızı aksatmayacak şekilde ayarlıyoruz.</p>
<h2>Veri güvenliği</h2>
<p>Kurumsal cihazlarda veri güvenliği önceliklidir. Satış öncesinde disklerin silinmesini, sökülmesini ya da kurum içinde imha edilmesini öneriyoruz; disksiz cihaz satışı da mümkündür. Telefon ve tabletlerin kurumsal yönetim (MDM) kayıtlarından çıkarılması gerekir.</p>
<h2>Envanter hazırlığı</h2>
<p>Kurumsal satışlarda teklifin hızı büyük ölçüde envanterin düzenine bağlıdır. Varlık yönetimi ya da muhasebe sisteminizden alınan bir demirbaş listesi çoğu zaman yeterlidir. Listede marka ve model, seri numarası (varsa), alım yılı, adet ve kısa durum bilgisi bulunması işimizi kolaylaştırır. Cihazların birden fazla lokasyonda bulunduğu durumlarda her lokasyonu ayrı başlıkta toplamanız, teslim alma planını da netleştirir. Şirket demirbaşlarını nasıl satarım diye düşünen yöneticiler için en pratik yol, envanteri tek seferde paylaşıp bütün ekipman için tek teklif almaktır. Ofis bilgisayarları toplu satış sürecinde satın alma, bilgi işlem ve muhasebe birimlerinin aynı bilgiye sahip olması da süreci kısaltır.</p>
<h2>Kimler için uygun?</h2>
<p>Ofis taşıyan, bilgisayar parkını yenileyen, birleşme veya küçülme sürecinde olan, şube kapatan şirketler; okullar, çağrı merkezleri ve kamu dışı kurumlar. Hemen arayın ya da WhatsApp\'tan yazın; ödeme şekli ve teslim yöntemi teklifle birlikte netleşir.</p>',
                'what_we_do' => [
                    'Şirket bilgisayarları ve ofis bilgisayarları',
                    'Sunucu, iş istasyonu ve depolama üniteleri',
                    'Kurumsal telefon ve tabletler',
                    'Switch, router ve network ekipmanı',
                    'Monitör, yazıcı, fotokopi ve projeksiyon',
                    'İş yeri elektronik eşya tasfiyesi',
                ],
                'suitable_for' => [
                    'Bilgisayar parkını yenileyen şirketler',
                    'Ofis taşıyan veya şube kapatan işletmeler',
                    'Çağrı merkezleri ve eğitim kurumları',
                    'Veri merkezi ve sunucu altyapısını yenileyen firmalar',
                    'Birleşme, küçülme veya kapanış sürecindeki şirketler',
                ],
                'process_steps' => $this->steps('kurumsal satış', 'Envanter listenizi (marka/model, adet, yaş, durum) paylaşın; fatura ve teslim belgelerini birlikte planlayalım.'),
                'faqs' => [
                    ['question' => 'Faturalı satış yapabiliyor muyuz?', 'answer' => 'Kurumsal satışlarda fatura ve teslim belgelerinin nasıl düzenleneceğini teklif aşamasında birlikte planlıyoruz.'],
                    ['question' => 'Disklerdeki veriler ne olacak?', 'answer' => 'Satış öncesinde diskleri silmenizi, sökmenizi veya kurum içinde imha etmenizi öneriyoruz. Disksiz cihazlar da satın alınabilir.'],
                    ['question' => 'Sunucu ve network ekipmanı alıyor musunuz?', 'answer' => 'Evet. Sunucular, depolama üniteleri, switch ve router gibi network ekipmanları da kurumsal alım kapsamındadır.'],
                    ['question' => 'Teslim alma iş akışımızı aksatır mı?', 'answer' => 'Teslim alma gün ve saatlerini iş akışınızı aksatmayacak şekilde birlikte planlıyoruz.'],
                ],
                'meta_title' => 'Kurumsal Elektronik ve IT Ekipmanı Alımı',
                'meta_description' => 'Şirket bilgisayarları, telefonlar, sunucu ve IT ekipmanlarını toplu satın alıyoruz. Şirket demirbaşlarınız için faturalı süreç ve hızlı teklif.',
            ],

            // ------------------------------------------------------------------
            // DURUMLAR
            // ------------------------------------------------------------------
            [
                'group' => 'condition',
                'title' => 'Stok Fazlası Elektronik Alımı',
                'slug' => 'stok-fazlasi-elektronik-alimi',
                'icon' => 'fa-solid fa-boxes-stacked',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'İthalatçı, distribütör ve mağazaların elektronik stok fazlasını toplu alıyoruz.',
                'description' => '<p>Yanlış sipariş tahmini, model değişikliği, sezon sonu ya da beklenenden yavaş satış… Elektronik sektöründe stok fazlası kaçınılmazdır ve ürünler depoda bekledikçe değer kaybeder. <strong>Stok fazlası elektronik alımı</strong> hizmetimizle ithalatçıların, distribütörlerin, bayilerin ve mağazaların elindeki fazla stoğu toplu olarak satın alıyoruz. Elektronik stok fazlası alan firmalar arasında hızlı karar veren bir muhatap arıyorsanız listenizi bize iletin.</p>
<h2>Hangi stokları alıyoruz?</h2>
<p>Kapalı kutu sıfır telefon, tablet, laptop, robot süpürge, televizyon, oyun konsolu, akıllı saat, kulaklık ve diğer elektronik ürünlerin stok fazlasını değerlendiriyoruz. Tek model yüksek adetli stoklar da, çok sayıda farklı modelden oluşan karışık stoklar da teklife uygundur. Mağaza stok fazlası alımı, ithalatçı stok fazlası ve distribütör stok fazlası alımı en sık karşılaştığımız taleplerdir.</p>
<h2>Neden toplu satmak mantıklı?</h2>
<p>Stok fazlasını indirimli kampanyalarla eritmeye çalışmak hem fiyat algınızı zedeler hem de uzun sürer. Fazla stoğu tek seferde toplu satmak depo alanını boşaltır, nakit akışını rahatlatır ve yeni sezon ürünlerine yer açar. Firma elektronik stok alımı sürecinde ürünlerinizin hangi kanallarda satılacağı konusundaki hassasiyetlerinizi de teklif aşamasında konuşabiliriz.</p>
<h2>Teklif için ne gerekir?</h2>
<p>Ürün listesi (marka, model, varyant), adet, kutu durumu ve ürünlerin bulunduğu il bilgisi teklif için yeterlidir. Faturalı satış ve teslim belgeleri kurumsal satışlarda birlikte planlanır. Fiyatı; ürünün güncel piyasa değeri, model yaşı, adet ve garanti durumu belirler.</p>
<h2>Stok fazlası ne zaman satılmalı?</h2>
<p>Elektronik ürünlerde değer kaybı zamana bağlıdır: yeni model duyurusu, kampanya dönemleri ve sezon geçişleri fiyatları hızla aşağı çeker. Bu nedenle fazla stoğun, ürün hâlâ güncel modeller arasındayken değerlendirilmesi genellikle daha iyi sonuç verir. Stok fazlası ürünlerimi kime satabilirim sorusunu ürünler depoda aylarca bekledikten sonra sormak yerine, satış hızının düştüğünü fark ettiğiniz anda listeyi paylaşmanızı öneririz. Kısmi satış da mümkündür; stoğun tamamı yerine belirli modelleri ya da belirli adetleri değerlendirmek isteyebilirsiniz. Mağaza stoğunu toplu satmak istiyorum diyen perakendeciler için raf ve depo stoğunu birlikte fiyatlamak da süreci kısaltır.</p>
<h2>Hemen arayın</h2>
<p>Depo stok fazlası elektronik alımı için hemen arayın ya da WhatsApp\'tan yazın. Kısa sürede teklif iletiyoruz; anlaşma sağlanırsa ürünleri deponuzdan teslim alıyoruz. Ödeme şekli ve teslim yöntemi teklifle birlikte netleşir.</p>',
                'what_we_do' => [
                    'İthalatçı ve distribütör stok fazlası',
                    'Mağaza ve bayi stok fazlası',
                    'Kapalı kutu sıfır elektronik ürünler',
                    'Sezon sonu ve model değişikliği stokları',
                    'Tek model yüksek adetli partiler',
                    'Karışık marka ve model stokları',
                ],
                'suitable_for' => [
                    'İthalatçılar',
                    'Distribütörler',
                    'Bayiler ve mağaza zincirleri',
                    'E-ticaret satıcıları',
                    'Fazla sipariş vermiş kurumsal alıcılar',
                ],
                'process_steps' => $this->steps('stok fazlası satış', 'Ürün listesi (marka, model, varyant), adet, kutu durumu ve ürünlerin bulunduğu il bilgisini paylaşın.'),
                'faqs' => [
                    ['question' => 'Stok fazlası ürünlerim hangi kanallarda satılacak?', 'answer' => 'Satış kanalı konusunda hassasiyetiniz varsa teklif aşamasında konuşabiliriz; bu konuları anlaşmadan önce netleştiriyoruz.'],
                    ['question' => 'Garantili ürünlerin garantisi devam eder mi?', 'answer' => 'Garanti koşulları üretici ve ithalatçıya göre değişir. Ürünlerin garanti durumunu teklif öncesinde belirtmeniz fiyatlandırmayı kolaylaştırır.'],
                    ['question' => 'Tek model ve yüksek adetli stok alıyor musunuz?', 'answer' => 'Evet. Tek model yüksek adetli partiler genellikle daha hızlı fiyatlandırılır.'],
                    ['question' => 'Stok fazlası ürünlerimi kime satabilirim?', 'answer' => 'Toplu alım yapan bir muhatapla tek seferde satmak, ürünleri tek tek satmaktan çok daha hızlıdır. Listenizi paylaşın, teklifimizi iletelim.'],
                ],
                'meta_title' => 'Stok Fazlası Elektronik Alımı | Stok Fazlası Alanlar',
                'meta_description' => 'İthalatçı, distribütör ve mağazaların elektronik stok fazlasını toplu satın alıyoruz. Kapalı kutu telefon, tablet, laptop stokları için hemen teklif alın.',
            ],
            [
                'group' => 'condition',
                'title' => 'Elektronik Depo Boşaltma',
                'slug' => 'elektronik-depo-bosaltma',
                'icon' => 'fa-solid fa-warehouse',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'Depodaki elektronik ürünlerin tamamını tek seferde satın alıyor, depoyu boşaltıyoruz.',
                'description' => '<p>Depo kirası sözleşmesinin bitmesi, yeni sezon ürünlerine yer açma ihtiyacı ya da faaliyet değişikliği… Bir depodaki elektronik ürünlerin kısa sürede boşaltılması gerektiğinde ürünleri tek tek satmak çoğu zaman mümkün olmaz. <strong>Elektronik depo boşaltma</strong> hizmetimizle depodaki elektronik ürünleri toplu olarak satın alıyor, teslim alma sürecini sizinle birlikte planlıyoruz.</p>
<h2>Depoda neler olabilir?</h2>
<p>Telefon deposu boşaltma, bilgisayar depo boşaltma, tablet depo boşaltma ve robot süpürge depo boşaltma en sık karşılaştığımız taleplerdir. Bunların yanında televizyon, monitör, konsol, aksesuar, yedek parça ve karışık elektronik ürünlerin bulunduğu depolar da değerlendirilir. Depodaki ürünlerin bir kısmı sıfır, bir kısmı iade, teşhir ya da arızalı olabilir; hepsini gruplara ayırarak tek teklifte topluyoruz.</p>
<h2>Nasıl planlıyoruz?</h2>
<p>Depo boşaltmada en önemli konu süredir. Ürün listesi ya da depo envanteri, yaklaşık adet ve palet sayısı, deponun bulunduğu il ve boşaltılması gereken tarih bilgisini paylaşmanız yeterli. Envanter listesi yoksa depoyu gösteren fotoğraf ve videolar da ilk değerlendirme için işe yarar. Teklif kabul edildikten sonra teslim alma günlerini ve yükleme sürecini birlikte belirliyoruz.</p>
<h2>Kimler depo boşaltıyor?</h2>
<p>Depo ürünlerini toplu satmak istiyorum diyen ithalatçılar, depo değiştiren distribütörler, faaliyetini sonlandıran toptancılar, e-ticaret depolarını küçülten satıcılar ve lojistik firmalarında sahipsiz kalmış ürünlerin hak sahipleri en sık çalıştığımız gruplardır. Mağaza veya iş yeri kapanışlarında <a href="/tasfiye-elektronik-alimi">tasfiye ve mağaza kapanış ürünleri alımı</a> sayfamıza da göz atabilirsiniz.</p>
<h2>Depo envanteri nasıl çıkarılır?</h2>
<p>Depo boşaltmada ürünleri tek tek saymak çoğu zaman mümkün değildir. Bu durumda palet ya da koli bazında ilerlemek pratik bir çözümdür: her paletin içerdiği ürün grubunu, yaklaşık adedi ve genel durumunu not etmeniz ilk teklif için yeterlidir. Koli etiketleri, irsaliyeler ya da ithalat belgeleri de içerik hakkında fikir verir. Depoda sıfır ve iade ürünler karışık duruyorsa, mümkün olduğunca ayrı alanlarda toplamanız teslim alma gününde ciddi zaman kazandırır. Yükleme için forklift, rampa ve araç giriş koşulları gibi bilgileri de önceden paylaşmanız, teslim alma planını daha isabetli kılar. Depo ürünlerini toplu satmak istiyorum diyorsanız ilk adım, deponun genel fotoğraflarını ve palet sayısını iletmektir.</p>
<h2>Hemen arayın</h2>
<p>Depo boşaltma işlerinde zaman kısıtlı olduğu için telefonla görüşmek en hızlı yoldur. Hemen arayın; ürünleri ve süreyi konuşalım. Ödeme şekli ve teslim yöntemi teklifle birlikte netleşir.</p>',
                'what_we_do' => [
                    'Telefon deposu boşaltma',
                    'Bilgisayar ve laptop depo boşaltma',
                    'Tablet ve robot süpürge depo boşaltma',
                    'Karışık elektronik depo envanteri',
                    'Aksesuar ve yedek parça stokları',
                    'Palet hâlinde ürün teslim alma planlaması',
                ],
                'suitable_for' => [
                    'Depo değiştiren ithalatçı ve distribütörler',
                    'Faaliyetini sonlandıran toptancılar',
                    'E-ticaret deposunu küçülten satıcılar',
                    'Depo kirası sona eren işletmeler',
                    'Yeni sezona yer açmak isteyen firmalar',
                ],
                'process_steps' => $this->steps('depo boşaltma', 'Depo envanteri veya ürün listesi, palet sayısı, deponun bulunduğu il ve boşaltma tarihini paylaşın; liste yoksa fotoğraf ve video da olur.'),
                'faqs' => [
                    ['question' => 'Envanter listem yok, yine de teklif alabilir miyim?', 'answer' => 'Evet. Depoyu gösteren fotoğraf ve videolarla ilk değerlendirmeyi yapabiliriz; kesin teklif için ürünlerin detaylı incelenmesi gerekebilir.'],
                    ['question' => 'Depoyu belirli bir tarihe kadar boşaltmam gerekiyor.', 'answer' => 'Boşaltma tarihini en başta belirtin; teslim alma planını bu tarihe göre yapıyoruz.'],
                    ['question' => 'Depodaki ürünlerin bir kısmı arızalı, sorun olur mu?', 'answer' => 'Hayır. Sıfır, iade ve arızalı ürünleri gruplara ayırarak tek teklifte topluyoruz.'],
                    ['question' => 'Yükleme ve taşıma kime ait?', 'answer' => 'Teslim alma ve yükleme koşulları ürün hacmine ve konuma göre teklifle birlikte netleşir.'],
                ],
                'meta_title' => 'Elektronik Depo Boşaltma | Toplu Depo Ürünü Alımı',
                'meta_description' => 'Telefon, bilgisayar, tablet ve robot süpürge depolarını boşaltıyor, ürünleri toplu satın alıyoruz. Depo boşaltma için hemen arayın, hızlı teklif alın.',
            ],
            [
                'group' => 'condition',
                'title' => 'Tasfiye ve Mağaza Kapanış Ürünleri Alımı',
                'slug' => 'tasfiye-elektronik-alimi',
                'icon' => 'fa-solid fa-store-slash',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'Tasfiye, iflas ve mağaza kapanışı sürecindeki elektronik ürünleri toplu alıyoruz.',
                'description' => '<p>Bir mağazanın kapanması, şirketin tasfiyeye girmesi ya da iflas süreci… Bu dönemlerde elektronik ürünlerin kısa sürede ve tek seferde paraya çevrilmesi gerekir. <strong>Tasfiye elektronik ürün alımı</strong> hizmetimizle mağaza kapanış stoğu, tasfiye ürünleri ve iflas tasfiyesi kapsamındaki elektronik ürünleri toplu olarak satın alıyoruz.</p>
<h2>Hangi durumlarda çalışıyoruz?</h2>
<ul><li><strong>Mağaza kapanışı:</strong> raf ve depo stoğu, teşhir ürünleri, aksesuarlar</li><li><strong>Şirket tasfiyesi:</strong> ticari stoklar ve elektronik demirbaşlar</li><li><strong>İflas tasfiyesi:</strong> yetkili kişi ve kurumlarla yürütülen satışlar</li><li><strong>Şube kapatma:</strong> zincir mağazaların kapanan şubelerindeki ürünler</li><li><strong>Faaliyet değişikliği:</strong> elektronik satışını bırakan işletmelerin kalan stoğu</li></ul>
<h2>Toplu ürün tasfiye alımı nasıl ilerler?</h2>
<p>Tasfiye süreçlerinde genellikle hem ticari stok hem de iş yerinde kullanılan elektronik eşyalar bir arada bulunur. Telefon tasfiye alımı, bilgisayar tasfiye alımı, tablet tasfiye alımı ve robot süpürge tasfiye alımı gibi ürün gruplarını ayrı ayrı fiyatlayıp tek teklifte topluyoruz. Ürün listesi, adet, durum ve ürünlerin bulunduğu adres bilgisi ilk teklif için yeterlidir.</p>
<h2>Yetki ve belgeler</h2>
<p>Tasfiye ve iflas süreçlerinde satışın yetkili kişi veya kurum tarafından yapılması gerekir. Satış yetkisi, fatura ve teslim tutanağı gibi belgeleri teklif aşamasında birlikte netleştiriyoruz. Mağaza kapanış ürünleri alanlar arasında süreci düzenli ve belgeli yürütmek isteyen bir muhatap arıyorsanız bizimle iletişime geçin.</p>
<h2>Kapanış sürecini kolaylaştıran adımlar</h2>
<p>Mağaza kapanışlarında ürünleri raf stoğu, depo stoğu, teşhir ürünleri ve aksesuarlar olarak dört ana grupta toplamak sayımı ve fiyatlamayı hızlandırır. Teşhirde kullanılan cihazların demo modundan çıkarılması ve sıfırlanması, aksesuarların ise ürün gruplarına göre kolilenmesi teslim alma gününü kısaltır. Şirket tasfiyelerinde ticari stok ile şirketin kendi kullandığı elektronik demirbaşları ayrı listelemeniz, belgelendirmeyi de kolaylaştırır. İflas tasfiyesi gibi resmi süreçlerde satış takvimi çoğu zaman yetkili kişi veya kurum tarafından belirlenir; bu takvime uyumlu şekilde çalışıyoruz. Kapanış tarihi yaklaşan işletmelerin süreci son haftaya bırakmadan bizimle iletişime geçmesi, hem teklif hem de teslim alma planı için daha fazla esneklik sağlar.</p>
<h2>Zaman önemli</h2>
<p>Kapanış ve tasfiye işlerinde süre kısıtlıdır. Hemen arayın; ürünleri, adresi ve kapanış tarihini konuşalım. Ödeme şekli ve teslim yöntemi teklifle birlikte netleşir.</p>',
                'what_we_do' => [
                    'Mağaza kapanış stoğu alımı',
                    'Şirket tasfiyesi kapsamındaki elektronik ürünler',
                    'İflas tasfiye elektronik ürün alımı',
                    'Kapanan şube stokları',
                    'Telefon, bilgisayar ve tablet tasfiye alımı',
                    'Teşhir ürünleri ve aksesuar stokları',
                ],
                'suitable_for' => [
                    'Kapanan elektronik ve GSM mağazaları',
                    'Tasfiye sürecindeki şirketler',
                    'İflas süreçlerini yürüten yetkililer',
                    'Şube kapatan zincir mağazalar',
                    'Faaliyet değiştiren işletmeler',
                ],
                'process_steps' => $this->steps('tasfiye satış', 'Ürün listesi, adet, durum, ürünlerin bulunduğu adres ve kapanış tarihini paylaşın; satış yetkisini belirtin.'),
                'faqs' => [
                    ['question' => 'İflas sürecindeki ürünleri alıyor musunuz?', 'answer' => 'Evet, satışın yetkili kişi veya kurum tarafından yapılması şartıyla. Gerekli belgeleri teklif aşamasında netleştiriyoruz.'],
                    ['question' => 'Mağazadaki teşhir ürünlerini de alıyor musunuz?', 'answer' => 'Evet. Teşhir ürünleri, raf stoğu ve aksesuarlar aynı teklifte değerlendirilir.'],
                    ['question' => 'Kapanış tarihimize yetişir mi?', 'answer' => 'Kapanış tarihini en başta belirtin; teklif ve teslim alma planını bu tarihe göre yapıyoruz.'],
                    ['question' => 'Mağaza demirbaşı elektronikleri de alıyor musunuz?', 'answer' => 'Mağazada kullanılan bilgisayar, POS, yazıcı ve ekran gibi elektronik demirbaşlar da değerlendirilir.'],
                ],
                'meta_title' => 'Tasfiye Elektronik Alımı | Mağaza Kapanış Stoğu Alanlar',
                'meta_description' => 'Mağaza kapanışı, şirket tasfiyesi ve iflas sürecindeki elektronik ürünleri toplu satın alıyoruz. Kapanış stoğunuz için hemen arayın, teklif alın.',
            ],
            [
                'group' => 'condition',
                'title' => 'Arızalı ve Hurda Elektronik Alımı',
                'slug' => 'arizali-hurda-elektronik-alimi',
                'icon' => 'fa-solid fa-screwdriver-wrench',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'Arızalı, ekranı kırık, çıkma ve hurda telefon, tablet, bilgisayarları toplu alıyoruz.',
                'description' => '<p>Arızalı bir cihaz tek başına çoğu zaman değersiz görünür; ancak toplu hâlde, parçaları ve onarılabilir durumu dikkate alındığında hâlâ değerlidir. <strong>Arızalı ve hurda elektronik alımı</strong> hizmetimizle ekranı kırık, açılmayan, çıkma ya da hurda durumundaki elektronik cihazları toplu olarak satın alıyoruz. <strong>Toptan hurda telefon alımı</strong> başta olmak üzere tablet, bilgisayar, robot süpürge ve diğer cihazlarda teklif veriyoruz.</p>
<h2>Hangi arızalı ürünleri alıyoruz?</h2>
<ul><li>Arızalı telefonları toplu alanlar arasında: ekranı kırık telefon, açılmayan telefon, çıkma telefon ve hurda telefonlar</li><li>Arızalı ve hurda tabletler</li><li>Arızalı bilgisayar, hurda bilgisayar ve laptoplar, bilgisayar kasaları</li><li>Arızalı robot süpürge ve arızalı dikey süpürgeler</li><li>Arızalı elektronik cihazlar: TV, monitör, konsol, kamera ve benzerleri</li><li>Servis ve tamircilerde biriken parça cihazlar</li></ul>
<h2>Arızalı ürün nasıl fiyatlanır?</h2>
<p>Arızalı cihazlarda fiyatı; marka ve model, arızanın türü (ekran, anakart, batarya, kasa), cihazın açılıp açılmadığı, eksik parça durumu, hesap kilidi ve toplam adet belirler. Her cihazı tek tek test etmek mümkün olmadığında toplu partiler genel durum tarifine göre değerlendirilir. Arızaları kısaca listelemeniz ya da örnek cihazların fotoğraflarını göndermeniz teklifi hızlandırır.</p>
<h2>Toplu arızalı elektronik satışı kimler için uygun?</h2>
<p>Teknik servisler, tamirciler, garanti dışı iadeleri biriken satıcılar, e-ticaret firmalarının hasarlı iadeleri, kurumların kullanım dışı cihazları ve elinde çok sayıda bozuk cihaz bulunan bireysel satıcılar en sık çalıştığımız gruplardır. Hurda elektronik cihaz alanlar arasında cihazları gruplara ayırıp adil değerlendirme yapan bir muhatap arıyorsanız listenizi gönderin.</p>
<h2>Arızalı cihazları nasıl gruplamalı?</h2>
<p>Arızalı partilerde ürünleri durumlarına göre gruplamak teklifi hem hızlandırır hem de daha adil hâle getirir. Pratik bir yöntem şöyledir: açılan ve çalışan ama kozmetik hasarı olanlar, açılan ama bir işlevi (ekran, kamera, şarj, ses) arızalı olanlar, hiç açılmayanlar ve parçası eksik olanlar. Telefon ve tabletlerde hesap kilidi bulunan cihazları da ayrı bir grupta toplayın. Her grup için yaklaşık adet ve ağırlıklı modelleri yazmanız yeterlidir. Teknik servislerde biriken cihazlarda servis kayıtlarındaki arıza notları da işe yarar. Toptan hurda telefon alımında, örneğin aynı modelden çok sayıda ekranı kırık cihaz bulunması partiyi daha kolay fiyatlanır hâle getirir. Ne kadar düzenli bilgi gelirse teklif o kadar isabetli olur.</p>
<h2>Satıcı sorumluluğu</h2>
<p>Satışa sunulan cihazların satıcıya ait olması gerekir; çalıntı veya kayıp ihbarlı cihazları kabul etmiyoruz. Hemen arayın ya da WhatsApp\'tan yazın; ödeme şekli ve teslim yöntemi teklifle birlikte netleşir.</p>',
                'what_we_do' => [
                    'Toptan hurda telefon alımı',
                    'Ekranı kırık ve açılmayan telefonlar',
                    'Çıkma telefon ve tabletler',
                    'Arızalı ve hurda bilgisayar, laptop, kasa',
                    'Arızalı robot süpürge ve dikey süpürgeler',
                    'Teknik servislerde biriken parça cihazlar',
                ],
                'suitable_for' => [
                    'Teknik servis ve tamirciler',
                    'Hasarlı iadeleri biriken e-ticaret satıcıları',
                    'Kullanım dışı cihazları olan kurumlar',
                    'Garanti dışı ürünleri biriken bayiler',
                    'Elinde çok sayıda bozuk cihaz bulunan satıcılar',
                ],
                'process_steps' => $this->steps('arızalı ürün satış', 'Marka/model, arıza türü, adet ve cihazların açılıp açılmadığı bilgisini iletin; örnek fotoğraflar teklifi hızlandırır.'),
                'faqs' => [
                    ['question' => 'Hiç açılmayan cihazları da alıyor musunuz?', 'answer' => 'Evet. Açılmayan cihazlar ayrı bir grup olarak, marka/model ve genel duruma göre fiyatlandırılır.'],
                    ['question' => 'Her cihazı tek tek test ediyor musunuz?', 'answer' => 'Toplu partilerde her cihazı test etmek her zaman mümkün olmaz; genel durum tarifine ve örnek incelemeye göre değerlendirme yapılır. Bunu teklif öncesinde açıkça konuşuruz.'],
                    ['question' => 'Hesap kilitli arızalı telefonları alıyor musunuz?', 'answer' => 'Kilitli cihazlar ayrıca değerlendirilir ve farklı fiyatlanır. Cihazların satıcıya ait olması şarttır.'],
                    ['question' => 'Hurda telefonları kilo ile mi alıyorsunuz?', 'answer' => 'Fiyatlandırma ürünün türüne ve durumuna göre değişir; model bazlı ya da parti bazlı değerlendirme teklifle birlikte netleşir.'],
                ],
                'meta_title' => 'Arızalı ve Hurda Elektronik Alımı | Hurda Telefon Alanlar',
                'meta_description' => 'Ekranı kırık, arızalı, çıkma ve hurda telefon, tablet, bilgisayar ve robot süpürgeleri toplu satın alıyoruz. Toptan hurda telefon alımı için hemen arayın.',
            ],
            [
                'group' => 'condition',
                'title' => 'Teşhir, İade ve Kapalı Kutu Ürün Alımı',
                'slug' => 'teshir-iade-elektronik-alimi',
                'icon' => 'fa-solid fa-box-open',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'Teşhir, müşteri iadesi, e-ticaret iadesi ve kapalı kutu elektronik ürünleri toplu alıyoruz.',
                'description' => '<p>E-ticaret ve perakendede iade ve teşhir ürünleri sürekli birikir. Bu ürünler çoğu zaman sağlamdır, ancak "sıfır" olarak satılamadıkları için depoda bekler. <strong>Teşhir, iade ve kapalı kutu ürün alımı</strong> hizmetimizle teşhir elektronik ürünleri, müşteri iadesi elektronik ürünleri, e-ticaret iade ürünlerini ve kapalı kutu cihazları toplu olarak satın alıyoruz.</p>
<h2>Hangi ürünleri alıyoruz?</h2>
<p>Teşhir telefon, teşhir tablet ve teşhir robot süpürge başta olmak üzere mağazada sergilenmiş her türlü elektronik ürün; iade telefon, iade tablet, iade robot süpürge gibi müşteri tarafından açılıp geri gönderilmiş ürünler; ambalajı açılmamış kapalı kutu telefonlar ve diğer kapalı kutu elektronik ürünler bu kapsamda değerlendirilir. Outlet elektronik stok alımı ve pazaryeri iadeleri de en sık karşılaştığımız taleplerdendir.</p>
<h2>E-ticaret iade ürünleri</h2>
<p>E-ticaret iade ürünlerini alan firmalar arasında iadeleri düzenli olarak değerlendirebilen bir muhatap, satıcıya ciddi zaman kazandırır. İadeleri dönemsel olarak (haftalık, aylık) ya da birikmiş tek parti hâlinde değerlendirebiliriz. İadelerin bir kısmının kusurlu olması sorun değil; sağlam, kusurlu ve eksik parçalı ürünleri ayrı gruplarda fiyatlıyoruz.</p>
<h2>Fiyatı neler etkiler?</h2>
<p>Fiyatı ürünün marka ve modeli, kutu ve aksesuar eksiksizliği, kozmetik durumu, çalışıp çalışmadığı, garanti durumu ve toplam adet belirler. Kapalı kutu ürünler sıfıra yakın, teşhir ve iade ürünler durumlarına göre değerlendirilir. Ürün listesi ve durum özeti teklif için yeterlidir.</p>
<h2>İadeleri sınıflandırmanın kolay yolu</h2>
<p>İade ürünlerde sınıflandırma, teklifin en önemli girdisidir. Ürünleri dört gruba ayırmanızı öneririz: ambalajı hiç açılmamış (kapalı kutu), kutusu açılmış ama ürünü kullanılmamış, kullanılmış ama sağlam ve kusurlu ya da eksik parçalı. Pazaryeri iadelerinde iade nedenleri (vazgeçme, yanlış ürün, kusur) genellikle sistemde kayıtlıdır; bu kayıtlar ürünlerin durumunu tahmin etmede çok işe yarar. Teşhir ürünlerinde ise ne kadar süre sergilendikleri ve kozmetik durumları belirleyicidir. Düzenli iade akışı olan satıcılar için dönemsel teslim alma planı yapmak, depoda iade birikmesini de önler. Bu sınıflandırmayı yapamıyorsanız toplu fotoğraf ve iade listesiyle başlayabiliriz.</p>
<h2>Kapalı kutu elektronik ürünlerimi satmak istiyorum</h2>
<p>Elinizde açılmamış kutularda ürün varsa bu, en hızlı fiyatlanan gruptur. Hemen arayın ya da WhatsApp\'tan yazın; listenizi paylaşın. Ödeme şekli ve teslim yöntemi teklifle birlikte netleşir.</p>',
                'what_we_do' => [
                    'Teşhir telefon, tablet ve robot süpürge',
                    'Müşteri iadesi elektronik ürünler',
                    'E-ticaret ve pazaryeri iadeleri',
                    'Kapalı kutu telefon ve elektronik ürünler',
                    'Outlet elektronik stokları',
                    'Kutusu veya aksesuarı eksik ürünler',
                ],
                'suitable_for' => [
                    'E-ticaret ve pazaryeri satıcıları',
                    'Elektronik mağazaları ve zincirler',
                    'Outlet mağazaları',
                    'İade yönetimi yapan lojistik firmaları',
                    'Teşhir ürünlerini yenileyen bayiler',
                ],
                'process_steps' => $this->steps('iade ve teşhir ürün satış', 'Ürün listesi, adet, kutu/aksesuar durumu ve ürünlerin sağlam, kusurlu ya da eksik olup olmadığını belirtin.'),
                'faqs' => [
                    ['question' => 'İadeleri düzenli olarak satabilir miyim?', 'answer' => 'Evet. İade ürünlerini dönemsel olarak ya da birikmiş tek parti hâlinde değerlendirebiliriz.'],
                    ['question' => 'Kusurlu iadeler de alınır mı?', 'answer' => 'Evet. Sağlam, kusurlu ve eksik parçalı ürünleri ayrı gruplar hâlinde fiyatlıyoruz.'],
                    ['question' => 'Kapalı kutu ürünler sıfır fiyatına mı alınır?', 'answer' => 'Kapalı kutu ürünler sıfıra yakın değerlendirilir; kesin fiyat model, adet ve garanti durumuna göre teklifle belirlenir.'],
                    ['question' => 'Teşhir ürünlerinde çizik fiyatı etkiler mi?', 'answer' => 'Evet, kozmetik durum fiyatı etkiler. Örnek fotoğraflar teklifi daha isabetli hâle getirir.'],
                ],
                'meta_title' => 'Teşhir, İade ve Kapalı Kutu Elektronik Ürün Alımı',
                'meta_description' => 'Teşhir, müşteri iadesi, e-ticaret iadesi ve kapalı kutu elektronik ürünleri toplu satın alıyoruz. İade stoklarınız için hemen arayın, teklif alın.',
            ],
            [
                'group' => 'condition',
                'title' => 'İkinci El Elektronik Toplu Alımı',
                'slug' => 'ikinci-el-elektronik-toplu-alimi',
                'icon' => 'fa-solid fa-recycle',
                'image' => null,
                'image_alt' => null,
                'short_description' => 'İkinci el elektronik ürünleri lot, palet ve parti mal hâlinde toplu alıyoruz.',
                'description' => '<p>İkinci el elektronik ürünler doğru alıcıyla buluştuğunda hâlâ değerlidir; ancak çok sayıda cihazı tek tek satmak pratik değildir. <strong>İkinci el elektronik toplu alımı</strong> hizmetimizle kullanılmış telefon, tablet, bilgisayar, robot süpürge ve diğer elektronik ürünleri lot, palet ya da parti mal hâlinde toplu olarak satın alıyoruz. Toptan ikinci el elektronik alımı yapan bir muhatap arıyorsanız listenizi paylaşın.</p>
<h2>Lot, palet ve parti mal</h2>
<p>Lot hâlinde elektronik ürün alımı, palet elektronik ürün alımı ve karışık elektronik palet alımı en sık karşılaştığımız taleplerdir. Bu partilerde genellikle farklı marka, model ve durumda ürünler bir arada bulunur. Parti mal elektronik alımında ürünleri mümkün olduğunca gruplara ayırıyor, gruplama yapılamayan karışık partilerde genel durum tarifine göre teklif veriyoruz.</p>
<h2>Hangi ikinci el ürünleri alıyoruz?</h2>
<p>İkinci el telefonlar (çok sayıda ikinci el telefon satışı en sık talepler arasında), ikinci el tablet ve laptoplar, kullanılmış ofis bilgisayarları, ikinci el robot süpürgeler, oyun konsolları, monitörler ve kullanılmayan elektronik cihazlar bu kapsamda değerlendirilir. Kullanılmış ofis bilgisayarlarını satmak isteyen şirketler için <a href="/kurumsal-elektronik-alimi">kurumsal alım</a> sayfamızda ayrıntılı bilgi bulabilirsiniz.</p>
<h2>Fiyatı neler etkiler?</h2>
<p>İkinci el ürünlerde fiyatı; model ve yaş, kozmetik durum, çalışma durumu, pil sağlığı, aksesuar eksiksizliği, hesap kilidi ve toplam adet belirler. Ürünlerin genel durumunu A/B/C gibi sınıflarla ya da kısa açıklamalarla iletmeniz teklifi hızlandırır.</p>
<h2>İkinci el ürünlerde hazırlık</h2>
<p>İkinci el cihazları satışa hazırlarken en önemli adım hesaplardan çıkış ve sıfırlamadır. Telefon ve tabletlerde Apple Kimliği ya da Google hesabının kaldırılması, bilgisayarlarda disklerin silinmesi, konsollarda kullanıcı hesaplarının kapatılması gerekir. Ardından ürünleri türüne ve durumuna göre gruplamanız teklif sürecini kısaltır. Cihazların temizlenmesi zorunlu değildir, ancak kozmetik durumun doğru değerlendirilmesine yardımcı olur. Lot veya palet satışlarında içerik listesini birebir çıkarmak mümkün değilse, en çok bulunan model ve ürün gruplarını yaklaşık oranlarıyla belirtmeniz yeterlidir. Kullanılmayan elektronik cihazları alanlar arasında karışık partilere de teklif veren bir muhatapla çalışmak, ürünleri ayıklamakla geçecek zamanı ortadan kaldırır.</p>
<h2>Nasıl satarım?</h2>
<p>Hemen arayın ya da WhatsApp\'tan yazın. Lot veya palet içeriğini paylaşın, kısa sürede teklifimizi iletelim. Ödeme şekli ve teslim yöntemi teklifle birlikte netleşir.</p>',
                'what_we_do' => [
                    'İkinci el telefon, tablet ve laptop',
                    'Kullanılmış ofis bilgisayarları',
                    'Lot hâlinde elektronik ürünler',
                    'Palet ve karışık elektronik palet',
                    'Parti mal elektronik',
                    'Kullanılmayan elektronik cihazlar',
                ],
                'suitable_for' => [
                    'İkinci el elektronik satıcıları',
                    'Lot ve palet ürün ticareti yapanlar',
                    'Cihazlarını yenileyen işletmeler',
                    'Yenileme (refurbish) yapan firmalar',
                    'Elinde çok sayıda kullanılmış cihaz bulunan satıcılar',
                ],
                'process_steps' => $this->steps('ikinci el toplu satış', 'Lot veya palet içeriğini, adet ve genel durum sınıfını (A/B/C) paylaşın; karışık partilerde fotoğraf yeterli olabilir.'),
                'faqs' => [
                    ['question' => 'Karışık paletleri içeriğini bilmeden alıyor musunuz?', 'answer' => 'Karışık paletlerde içerik ve durum hakkında ne kadar bilgi olursa teklif o kadar isabetli olur. Bilgi sınırlıysa fotoğraf ve örnek incelemeyle değerlendirme yapılır.'],
                    ['question' => 'İkinci el ürünlerde A/B/C sınıfı ne demek?', 'answer' => 'Kozmetik ve çalışma durumuna göre yapılan genel bir sınıflandırmadır. Kendi sınıflandırmanızı ya da kısa açıklamaları iletmeniz yeterli.'],
                    ['question' => 'Kullanılmış ofis bilgisayarlarını alıyor musunuz?', 'answer' => 'Evet. Kullanılmış ofis bilgisayarları, monitörler ve çevre birimleri toplu olarak değerlendirilir.'],
                    ['question' => 'Çok sayıda ikinci el telefonu tek seferde satabilir miyim?', 'answer' => 'Evet. Model ve durum gruplarına ayırarak tek teklifte topluyoruz.'],
                ],
                'meta_title' => 'İkinci El Elektronik Toplu Alımı | Lot ve Palet Alımı',
                'meta_description' => 'İkinci el telefon, tablet, laptop ve elektronik ürünleri lot, palet ve parti mal hâlinde toplu satın alıyoruz. Hemen arayın, hızlı fiyat teklifi alın.',
            ],
        ];
    }
}

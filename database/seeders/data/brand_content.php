<?php

/*
 | Toplu alımını yaptığımız markalar.
 |
 | İÇERİK KURALI: metinler MARKA HAKKINDA iddia içermez (kalite, sıralama, ciro,
 | ödül). Yalnız o markanın HANGİ ÜRÜNLERİNİ, HANGİ DURUMDA toplu aldığımızı ve
 | teklif için hangi bilgilerin gerektiğini anlatır. Marka adlarının sahiplerine
 | ait olduğu ve bağımsız alıcı olduğumuz notunu BrandSeeder her metnin sonuna ekler.
 */

return [

    [
        'name' => 'Apple',
        'slug' => 'apple',
        'is_featured' => true,
        'description' => 'iPhone, iPad, MacBook ve Apple Watch ürünlerinin toplu alımı: sıfır, teşhir, iade, ikinci el ve arızalı.',
        'products' => ['iPhone', 'iPad', 'MacBook Air / Pro', 'iMac', 'Apple Watch', 'AirPods'],
        'meta_title' => 'Apple Toplu Alım | iPhone, iPad, MacBook Alımı',
        'meta_description' => 'Toplu iPhone, iPad ve MacBook alımı. Stok fazlası, teşhir, iade, ikinci el ve arızalı Apple ürünlerine hızlı fiyat teklifi. Hemen arayın.',
        'content' => '<p>Apple ürünlerini adetli olarak satın alıyoruz. En sık aldığımız grup iPhone; bunu iPad, MacBook ve Apple Watch takip ediyor. Bayi stok fazlası, e-ticaret iadeleri, kurumsal yenilemeden çıkan şirket telefonları ve karışık ikinci el partiler için teklif veriyoruz.</p>
<h2>Hangi durumdaki Apple ürünlerini alıyoruz?</h2>
<ul>
<li><strong>Sıfır ve kapalı kutu:</strong> faturalı stok fazlası iPhone ve iPad partileri.</li>
<li><strong>Teşhir ve iade:</strong> kutusu açılmış veya kısa süre kullanılmış ürünler.</li>
<li><strong>İkinci el:</strong> çalışır durumdaki cihazlar; batarya sağlığı ve kozmetik duruma göre gruplanır.</li>
<li><strong>Arızalı:</strong> ekranı kırık, Face ID arızalı veya açılmayan cihazlar.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Model (örneğin iPhone 13, iPad Air 5. nesil), hafıza kapasitesi, adet ve genel durum fiyatın temelidir. Apple cihazlarında en kritik konu <strong>iCloud / Etkinleştirme Kilidi</strong>dir: hesabı kaldırılmış cihazlar ile kilitli cihazlar çok farklı fiyatlanır. MacBook\'larda işlemci (Intel veya M serisi), RAM ve SSD bilgisi de gerekir. Kutu ve şarj aleti olup olmadığını belirtmeniz yeterli.</p>
<p>Listenizi telefonla iletin ya da WhatsApp\'tan gönderin. Aynı modelden çok sayıda cihaz olabileceği gibi farklı modellerin karıştığı bir parti de olabilir; ikisi için de partinin tamamına tek fiyat veriyoruz.</p>',
        'faqs' => [
            ['question' => 'iCloud kilitli iPhone alıyor musunuz?', 'answer' => 'Kilitli cihazlar yalnızca parça değeri üzerinden ve fatura veya sahiplik belgesiyle değerlendirilir. Hesabı kaldırılmış cihazlar çok daha yüksek fiyat alır; mümkünse satış öncesi hesapları kaldırmanızı öneriyoruz.'],
            ['question' => 'Farklı iPhone modellerinden oluşan karışık partiyi alır mısınız?', 'answer' => 'Evet. Model, kapasite ve durum listesine göre her grubu ayrı fiyatlayıp partinin tamamı için tek fiyat veriyoruz.'],
            ['question' => 'Şirket MacBook\'larını toplu satmak istiyoruz, ne yapmalıyız?', 'answer' => 'Model, işlemci, RAM/SSD ve adet listesini gönderin. Satış öncesi verilerin silinmesini ve cihazların kurumsal yönetimden (MDM) çıkarılmasını öneriyoruz.'],
        ],
    ],

    [
        'name' => 'Samsung',
        'slug' => 'samsung',
        'is_featured' => true,
        'description' => 'Samsung Galaxy telefon, tablet, akıllı saat ve monitörlerin toplu alımı.',
        'products' => ['Galaxy S serisi', 'Galaxy A serisi', 'Galaxy Z Fold / Flip', 'Galaxy Tab', 'Galaxy Watch', 'Monitör ve TV'],
        'meta_title' => 'Samsung Toplu Telefon ve Tablet Alımı',
        'meta_description' => 'Samsung Galaxy telefon, tablet ve akıllı saat toplu alımı. Stok fazlası, teşhir, iade ve arızalı ürünlere hızlı teklif. Hemen arayın.',
        'content' => '<p>Samsung ürünlerini parti hâlinde satın alıyoruz. Galaxy S ve A serisi telefonlar en sık aldığımız ürünler; Galaxy Tab tabletler, Galaxy Watch akıllı saatler, monitörler ve televizyonlar da alım kapsamımızda.</p>
<h2>Samsung toplu alımında hangi durumlar var?</h2>
<ul>
<li>Bayi ve distribütör elindeki sıfır stok fazlası telefon ve tabletler.</li>
<li>Mağazalardan çıkan teşhir ürünleri ve müşteri iadeleri.</li>
<li>Kurumsal hatlarla kullanılmış, aynı modelden çok sayıda şirket telefonu.</li>
<li>Ekranı kırık, açılmayan veya şarj almayan arızalı cihaz partileri.</li>
</ul>
<h2>Fiyatı etkileyen bilgiler</h2>
<p>Telefon ve tabletlerde model adı ile hafıza kapasitesi ilk sorumuzdur. Ardından Google hesabı (FRP) kilidinin kaldırılıp kaldırılmadığını, ekranda yanık veya çizik olup olmadığını ve kutu-şarj aleti durumunu soruyoruz. Katlanabilir modellerde menteşe ve iç ekran durumu ayrıca önemlidir. Monitör ve televizyonlarda ekran ölçüsü ve panelin sağlam olup olmadığı yeterlidir.</p>
<p>Elinizdeki listeyi kesin bilmiyorsanız yaklaşık adet de yeterli. Telefonla arayın veya WhatsApp\'tan liste gönderin, partiye göre toplu alım fiyatımızı iletelim.</p>',
        'faqs' => [
            ['question' => 'Google hesabı kilitli Samsung telefon alıyor musunuz?', 'answer' => 'Kilitli cihazlar sahiplik belgesiyle ve yalnız parça değeri üzerinden değerlendirilir. Satış öncesi hesabın kaldırılması fiyatı belirgin şekilde artırır.'],
            ['question' => 'Galaxy Tab tabletleri toplu alıyor musunuz?', 'answer' => 'Evet. Eğitim projeleri, saha ekipleri veya mağaza stoklarından çıkan Galaxy Tab tabletleri adetli olarak alıyoruz.'],
            ['question' => 'Ekranında yanık olan telefonlar alınır mı?', 'answer' => 'Evet, arızalı veya kusurlu ürün olarak ayrı fiyatlanır. Listede kaç adet cihazda yanık olduğunu belirtmeniz yeterli.'],
        ],
    ],

    [
        'name' => 'Xiaomi',
        'slug' => 'xiaomi',
        'is_featured' => true,
        'description' => 'Xiaomi, Redmi ve POCO telefonlar ile Xiaomi robot süpürge ve akıllı ürünlerin toplu alımı.',
        'products' => ['Xiaomi / Redmi / POCO telefon', 'Xiaomi Pad', 'Robot süpürge', 'Dikey süpürge', 'Akıllı saat ve bileklik', 'Scooter'],
        'meta_title' => 'Xiaomi Toplu Alım | Telefon ve Robot Süpürge',
        'meta_description' => 'Xiaomi, Redmi, POCO telefon ve Xiaomi robot süpürge toplu alımı. Stok fazlası, iade ve arızalı ürünlere hızlı teklif. Hemen arayın.',
        'content' => '<p>Xiaomi geniş bir ürün yelpazesine sahip olduğu için bize gelen Xiaomi partileri genellikle karışıktır: aynı listede Redmi telefonlar, robot süpürgeler, akıllı bileklikler ve tabletler bulunabilir. Bu tür karışık partileri olduğu gibi satın alıyoruz.</p>
<h2>Xiaomi ürünlerinde toplu aldığımız gruplar</h2>
<ul>
<li><strong>Telefon:</strong> Xiaomi, Redmi ve POCO serisi; sıfır, teşhir, ikinci el ve arızalı.</li>
<li><strong>Robot ve dikey süpürge:</strong> kapalı kutu stok fazlası, iade veya arızalı üniteler.</li>
<li><strong>Tablet:</strong> Xiaomi Pad ve Redmi Pad.</li>
<li><strong>Giyilebilir ve akıllı ev:</strong> akıllı saat, bileklik ve benzeri ürünler.</li>
</ul>
<h2>Teklif için hangi bilgiler gerekir?</h2>
<p>Telefonlarda model ve hafıza bilgisi, hesap (Mi hesabı ve Google) kilidinin kaldırılıp kaldırılmadığı ve kutu durumu fiyatı belirler. Robot süpürgelerde model adı, şarj istasyonunun ve aksesuarların tam olup olmadığı, ürünün çalışıp çalışmadığı önemlidir. Ürün resmi distribütör garantili mi yoksa ithalatçı garantili mi, bunu da belirtmeniz faydalı olur.</p>
<p>Ürün listenizi telefonla iletin veya WhatsApp\'tan fotoğraflarla gönderin; partinin tamamı için toplu fiyatımızı verelim.</p>',
        'faqs' => [
            ['question' => 'Xiaomi robot süpürgeleri iade veya arızalı durumda alıyor musunuz?', 'answer' => 'Evet. Çalışan, iade gelmiş veya arızalı robot süpürgeleri durumlarına göre gruplayıp ayrı fiyatlıyoruz.'],
            ['question' => 'Mi hesabı kilitli telefonlar alınır mı?', 'answer' => 'Kilitli cihazlar yalnız sahiplik belgesiyle ve parça değeri üzerinden değerlendirilir. Hesabı kaldırılmış cihazlar çok daha yüksek fiyat alır.'],
            ['question' => 'Telefon ve süpürgenin karıştığı bir partiyi tek seferde satabilir miyim?', 'answer' => 'Evet. Listenin tamamını alıp her ürün grubunu ayrı değerlendiriyor, size tek bir toplam fiyat sunuyoruz.'],
        ],
    ],

    [
        'name' => 'Huawei',
        'slug' => 'huawei',
        'is_featured' => true,
        'description' => 'Huawei telefon, tablet, akıllı saat ve laptopların toplu alımı.',
        'products' => ['Huawei telefon', 'MatePad tablet', 'Huawei Watch', 'MateBook laptop', 'FreeBuds kulaklık'],
        'meta_title' => 'Huawei Toplu Telefon, Tablet ve Saat Alımı',
        'meta_description' => 'Huawei telefon, MatePad tablet, akıllı saat ve MateBook toplu alımı. Stok fazlası, teşhir ve arızalı ürünlere hızlı teklif.',
        'content' => '<p>Huawei telefon, tablet, akıllı saat ve laptoplarını toplu olarak satın alıyoruz. Bayi ve mağaza stoklarından, kurumsal kullanımdan ve ikinci el partilerden gelen ürünler için teklif veriyoruz.</p>
<h2>Alım yaptığımız Huawei ürünleri</h2>
<ul>
<li>Telefon: farklı seri ve modellerden sıfır, teşhir veya ikinci el partiler.</li>
<li>MatePad tabletler: mağaza stoğu veya kurumsal kullanım.</li>
<li>Huawei Watch ve bileklikler: kapalı kutu veya iade.</li>
<li>MateBook laptoplar: ofis yenilemesinden çıkan cihazlar.</li>
<li>Arızalı ürünler: ekran, batarya veya anakart arızalı cihazlar.</li>
</ul>
<h2>Fiyatı belirleyen bilgiler</h2>
<p>Telefon ve tabletlerde model, hafıza kapasitesi ve Huawei ID kilidinin kaldırılmış olması önemlidir. Akıllı saatlerde kutunun ve şarj aparatının tam olması fiyatı yukarı taşır. Laptoplarda işlemci, RAM ve depolama bilgisini paylaşmanız yeterlidir.</p>
<p>Elinizde kaç ürün olduğunu yaklaşık olarak bilmeniz bile ilk fiyat için yeterli. Telefonla arayın ya da WhatsApp\'tan listeyi gönderin.</p>',
        'faqs' => [
            ['question' => 'Huawei ID kilitli cihazları alıyor musunuz?', 'answer' => 'Kilitli cihazlar yalnızca sahiplik belgesiyle ve parça değeri üzerinden alınır. Satış öncesi hesabın kaldırılmasını öneriyoruz.'],
            ['question' => 'Huawei akıllı saatleri toplu alıyor musunuz?', 'answer' => 'Evet. Kapalı kutu, teşhir ve iade akıllı saat ve bileklikleri adetli olarak alıyoruz.'],
            ['question' => 'Teklif ne kadar sürede gelir?', 'answer' => 'Model ve adet listesi net olduğunda fiyatı kısa sürede bildiriyoruz. Karışık partilerde fotoğraf istememiz gerekebilir.'],
        ],
    ],

    [
        'name' => 'Oppo',
        'slug' => 'oppo',
        'is_featured' => false,
        'description' => 'Oppo telefon ve kulaklıkların toplu alımı: stok fazlası, teşhir, iade ve arızalı.',
        'products' => ['Oppo Reno serisi', 'Oppo A serisi', 'Oppo Find serisi', 'Oppo Enco kulaklık'],
        'meta_title' => 'Oppo Toplu Telefon Alımı | Stok Fazlası ve İade',
        'meta_description' => 'Oppo telefon ve kulaklık toplu alımı. Bayi stok fazlası, teşhir, iade ve arızalı cihazlara hızlı fiyat teklifi. Hemen arayın.',
        'content' => '<p>Oppo telefon ve kulaklıklarını parti hâlinde satın alıyoruz. Bize gelen Oppo partileri çoğunlukla bayi stok fazlası, mağaza teşhir ürünleri ve operatör kanalından dönen iadelerden oluşuyor.</p>
<h2>Oppo ürünlerinde toplu alım kapsamı</h2>
<ul>
<li>Reno, A ve Find serisi telefonlar: sıfır, kapalı kutu, teşhir veya ikinci el.</li>
<li>Enco serisi kablosuz kulaklıklar.</li>
<li>Ekranı kırık, şarj almayan veya açılmayan arızalı telefonlar.</li>
</ul>
<h2>Teklif için gerekenler</h2>
<p>Model adı, hafıza kapasitesi ve adet bilgisi ilk fiyatın temelidir. Telefonlarda Google hesabı kilidinin kaldırılmış olması, kutu ve şarj aletinin bulunması fiyatı olumlu etkiler. Arızalı cihazlarda arızanın türünü (ekran, batarya, anakart) biliyorsanız belirtin; bilmiyorsanız "arızalı" demeniz yeterli.</p>
<p>Oppo ürünlerinizi tek tek satmakla uğraşmayın. Listeyi telefonla iletin ya da WhatsApp\'tan gönderin, partinin tamamına tek fiyat verelim.</p>',
        'faqs' => [
            ['question' => 'Az sayıda Oppo telefon için de teklif veriyor musunuz?', 'answer' => 'Önceliğimiz toplu alım olsa da birkaç cihazlık partiler için de teklif verebiliyoruz. Listeyi göndermeniz yeterli.'],
            ['question' => 'Teşhir telefonlarını kutusuz alır mısınız?', 'answer' => 'Evet. Kutusuz teşhir ürünleri de alınır; kutu olmaması fiyatı bir miktar etkiler.'],
            ['question' => 'Oppo kulaklıkları da alıyor musunuz?', 'answer' => 'Evet. Enco serisi kulaklıkları kapalı kutu, teşhir veya iade durumunda toplu olarak alıyoruz.'],
        ],
    ],

    [
        'name' => 'Lenovo',
        'slug' => 'lenovo',
        'is_featured' => true,
        'description' => 'Lenovo ThinkPad, IdeaPad laptop, masaüstü bilgisayar ve Lenovo tabletlerin toplu alımı.',
        'products' => ['ThinkPad', 'IdeaPad', 'Legion', 'ThinkCentre masaüstü', 'Lenovo Tab', 'Monitör'],
        'meta_title' => 'Lenovo Toplu Laptop ve Tablet Alımı | ThinkPad',
        'meta_description' => 'Lenovo ThinkPad, IdeaPad laptop, ThinkCentre ve Lenovo Tab toplu alımı. Kurumsal demirbaş ve stok fazlasına hızlı teklif.',
        'content' => '<p>Lenovo ürünleri, kurumsal yenilemelerde en sık karşılaştığımız markalar arasında. Şirketlerden çıkan ThinkPad laptoplar, ThinkCentre masaüstü bilgisayarlar ve monitörler ile mağaza ve eğitim projelerinden gelen Lenovo Tab tabletleri toplu olarak satın alıyoruz.</p>
<h2>Lenovo ürünlerinde alım kapsamı</h2>
<ul>
<li><strong>Kurumsal laptop:</strong> ThinkPad ve ThinkBook serisi, aynı modelden çok sayıda.</li>
<li><strong>Bireysel ve oyun laptopu:</strong> IdeaPad, Yoga ve Legion serisi.</li>
<li><strong>Masaüstü:</strong> ThinkCentre kasa ve mini PC\'ler, monitörlerle birlikte.</li>
<li><strong>Tablet:</strong> Lenovo Tab serisi; okul ve saha projelerinden çıkan adetli tabletler.</li>
<li><strong>Arızalı:</strong> ekranı kırık, açılmayan veya klavyesi arızalı cihazlar.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Laptop ve masaüstlerinde model, işlemci nesli, RAM ve depolama bilgisi fiyatın temelidir. Şarj adaptörlerinin olup olmadığını ve BIOS şifresi bulunup bulunmadığını da belirtmeniz gerekir. Tabletlerde model, hafıza ve hesap kilidi durumu yeterlidir.</p>
<p>Demirbaş listenizi Excel ya da fotoğraf olarak gönderebilirsiniz. Satış öncesi disklerdeki verilerin silinmesini öneriyoruz. Telefonla arayın veya WhatsApp\'tan listeyi iletin.</p>',
        'faqs' => [
            ['question' => 'BIOS şifreli ThinkPad\'leri alıyor musunuz?', 'answer' => 'Şifreli cihazlar ayrı ve daha düşük fiyatlanır. Mümkünse satış öncesi BIOS şifrelerinin kaldırılmasını öneriyoruz.'],
            ['question' => 'Disksiz laptopları alır mısınız?', 'answer' => 'Evet. Veri güvenliği için diskleri sökülmüş cihazları da alıyoruz; disk olmaması fiyata yansıtılır.'],
            ['question' => 'Lenovo tabletleri toplu alıyor musunuz?', 'answer' => 'Evet. Eğitim projeleri ve saha ekiplerinden çıkan Lenovo Tab tabletleri adetli olarak alıyoruz.'],
        ],
    ],

    [
        'name' => 'HP',
        'slug' => 'hp',
        'is_featured' => true,
        'description' => 'HP laptop, masaüstü bilgisayar, monitör ve yazıcıların toplu alımı.',
        'products' => ['EliteBook', 'ProBook', 'HP Pavilion / Victus', 'EliteDesk / ProDesk', 'Monitör', 'Yazıcı'],
        'meta_title' => 'HP Toplu Laptop, Bilgisayar ve Yazıcı Alımı',
        'meta_description' => 'HP EliteBook, ProBook laptop, masaüstü bilgisayar, monitör ve yazıcı toplu alımı. Şirket demirbaşları için hızlı teklif.',
        'content' => '<p>HP ürünlerini özellikle şirket ve kurum yenilemelerinde toplu olarak satın alıyoruz. Ofislerden çıkan EliteBook ve ProBook laptoplar, EliteDesk ve ProDesk masaüstü bilgisayarlar, monitörler ve yazıcılar en sık aldığımız HP ürünleri.</p>
<h2>HP ürünlerinde toplu alım kapsamı</h2>
<ul>
<li>Kurumsal laptoplar: EliteBook, ProBook ve ZBook serisi.</li>
<li>Bireysel laptoplar: Pavilion, Victus ve Omen serisi; mağaza stoğu veya iade.</li>
<li>Masaüstü kasa, mini PC ve all-in-one bilgisayarlar.</li>
<li>Monitörler ve ofis yazıcıları.</li>
<li>Arızalı veya eksik parçalı cihazlar.</li>
</ul>
<h2>Teklif sürecinde istediğimiz bilgiler</h2>
<p>Bilgisayarlarda model, işlemci, RAM, disk ve adet bilgisi yeterlidir. Yazıcılarda model ile çalışır durumda olup olmadığını, monitörlerde ekran ölçüsünü ve panelin sağlam olup olmadığını belirtmeniz gerekir. Adaptör, kablo ve stand gibi aksesuarların durumu da fiyatı etkiler.</p>
<p>Kurumsal satışlarda cihazlardaki verilerin silinmesi sizin sorumluluğunuzdadır; bu konuyu teklif aşamasında birlikte konuşabiliriz. Demirbaş listenizi telefonla iletin veya WhatsApp\'tan gönderin.</p>',
        'faqs' => [
            ['question' => 'HP yazıcıları toplu alıyor musunuz?', 'answer' => 'Evet. Çalışır durumdaki ofis yazıcılarını ve çok fonksiyonlu cihazları adetli olarak alıyoruz. Arızalı yazıcılar ayrıca değerlendirilir.'],
            ['question' => 'Farklı nesillerden oluşan laptop partisini alır mısınız?', 'answer' => 'Evet. Listeyi model ve nesle göre gruplayıp her grubu ayrı fiyatlıyor, toplamda tek fiyat veriyoruz.'],
            ['question' => 'Monitörleri bilgisayarlarla birlikte satabilir miyim?', 'answer' => 'Evet. Bilgisayar, monitör ve çevre birimlerini aynı partide alıyoruz.'],
        ],
    ],

    [
        'name' => 'Dell',
        'slug' => 'dell',
        'is_featured' => true,
        'description' => 'Dell Latitude, Vostro, XPS laptop, OptiPlex masaüstü, monitör ve sunucuların toplu alımı.',
        'products' => ['Latitude', 'Vostro', 'XPS', 'Precision iş istasyonu', 'OptiPlex', 'PowerEdge sunucu', 'Monitör'],
        'meta_title' => 'Dell Toplu Laptop, Masaüstü ve Sunucu Alımı',
        'meta_description' => 'Dell Latitude, OptiPlex, Precision ve PowerEdge sunucu toplu alımı. Kurumsal IT ekipmanı ve demirbaşlara hızlı teklif.',
        'content' => '<p>Dell ürünlerini kurumsal IT yenilemelerinde toplu olarak satın alıyoruz. Latitude ve Vostro laptoplar, OptiPlex masaüstü bilgisayarlar, Precision iş istasyonları, monitörler ve PowerEdge sunucular alım kapsamımızda.</p>
<h2>Dell ürünlerinde alım kapsamı</h2>
<ul>
<li><strong>Laptop:</strong> Latitude, Vostro, Inspiron ve XPS serisi.</li>
<li><strong>Masaüstü:</strong> OptiPlex kasa, mini ve mikro form faktörlü bilgisayarlar.</li>
<li><strong>İş istasyonu:</strong> Precision serisi masaüstü ve mobil iş istasyonları.</li>
<li><strong>Sunucu ve depolama:</strong> PowerEdge sunucular ve sunucu parçaları.</li>
<li><strong>Monitör:</strong> ofis ve profesyonel monitörler.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Bilgisayarlarda model, işlemci, RAM, disk ve adet; sunucularda model, işlemci, bellek ve disk yapılandırması fiyatın temelini oluşturur. Servis etiketi (Service Tag) listesi paylaşabilirseniz cihazların yapılandırmasını daha hızlı netleştiririz. BIOS şifresi ve adaptör durumu da fiyatı etkiler.</p>
<p>Veri merkezi veya sunucu odası boşaltma gibi büyük işlerde ekipman listesini önceden alıp teslim almayı sizin prosedürlerinize uygun planlıyoruz. Listenizi telefonla iletin veya WhatsApp\'tan gönderin.</p>',
        'faqs' => [
            ['question' => 'Dell sunucu ve sunucu parçaları alıyor musunuz?', 'answer' => 'Evet. PowerEdge sunucuları ve işlemci, bellek, disk gibi sunucu parçalarını toplu olarak alıyoruz.'],
            ['question' => 'Servis etiketi listesi göndermem gerekiyor mu?', 'answer' => 'Zorunlu değil, ama yapılandırmayı netleştirmeyi hızlandırır. Model ve adet bilgisi ilk fiyat için yeterlidir.'],
            ['question' => 'Disklerin sökülmesi gerekiyor mu?', 'answer' => 'Veri güvenliği için disklerin silinmesini ya da sökülmesini öneriyoruz. Disksiz cihazlar da alınır.'],
        ],
    ],

    [
        'name' => 'Asus',
        'slug' => 'asus',
        'is_featured' => false,
        'description' => 'Asus laptop, oyun bilgisayarı, monitör ve bilgisayar parçalarının toplu alımı.',
        'products' => ['Vivobook / Zenbook', 'ROG / TUF oyun laptopu', 'ExpertBook', 'Monitör', 'Ekran kartı ve anakart', 'Modem-router'],
        'meta_title' => 'Asus Toplu Laptop ve Oyun Bilgisayarı Alımı',
        'meta_description' => 'Asus laptop, ROG ve TUF oyun bilgisayarı, monitör ve bilgisayar parçası toplu alımı. Stok fazlası ve iadeye hızlı teklif.',
        'content' => '<p>Asus laptoplarını, oyun bilgisayarlarını, monitörlerini ve bilgisayar parçalarını toplu olarak satın alıyoruz. Mağaza stokları, e-ticaret iadeleri, internet kafe ve oyun salonu kapanışları ile kurumsal yenilemeler en sık karşılaştığımız durumlar.</p>
<h2>Asus ürünlerinde alım kapsamı</h2>
<ul>
<li>Vivobook, Zenbook ve ExpertBook laptoplar.</li>
<li>ROG ve TUF serisi oyun laptopları ve masaüstü sistemler.</li>
<li>Oyun ve ofis monitörleri.</li>
<li>Anakart, ekran kartı gibi bilgisayar parçaları.</li>
<li>Modem, router ve network ürünleri.</li>
</ul>
<h2>Teklif için gerekenler</h2>
<p>Laptoplarda model, işlemci, ekran kartı, RAM ve depolama bilgisi gerekir. Oyun bilgisayarlarında ekran kartı modeli fiyatı en çok etkileyen kalemdir. Parçalarda kutulu olup olmadığını ve çalışır durumunu belirtmeniz yeterli. Monitörlerde ekran ölçüsü, yenileme hızı ve panel durumu önemlidir.</p>
<p>Ürün listenizi telefonla iletin ya da WhatsApp\'tan gönderin. Karışık partilerde her ürün grubunu ayrı değerlendirip tek bir toplam fiyat veriyoruz.</p>',
        'faqs' => [
            ['question' => 'İnternet kafe kapanışından çıkan sistemleri alıyor musunuz?', 'answer' => 'Evet. Kasa, monitör, klavye-mouse ve kulaklıklardan oluşan setleri toplu olarak alıyoruz.'],
            ['question' => 'Tek başına ekran kartı veya anakart alıyor musunuz?', 'answer' => 'Evet. Bilgisayar parçalarını adetli olarak, çalışır veya arızalı durumlarına göre alıyoruz.'],
            ['question' => 'İade gelen Asus laptopları alır mısınız?', 'answer' => 'Evet. Kutusu açılmış, kozmetik kusurlu veya kısa süre kullanılmış iade ürünleri alıyoruz.'],
        ],
    ],

    [
        'name' => 'Acer',
        'slug' => 'acer',
        'is_featured' => false,
        'description' => 'Acer laptop, masaüstü bilgisayar ve monitörlerin toplu alımı.',
        'products' => ['Aspire', 'TravelMate', 'Nitro / Predator', 'Veriton masaüstü', 'Monitör', 'Chromebook'],
        'meta_title' => 'Acer Toplu Laptop ve Monitör Alımı',
        'meta_description' => 'Acer Aspire, TravelMate, Nitro laptop, masaüstü ve monitör toplu alımı. Stok fazlası, iade ve demirbaşa hızlı teklif.',
        'content' => '<p>Acer laptop, masaüstü bilgisayar ve monitörlerini adetli olarak satın alıyoruz. Eğitim kurumlarından çıkan bilgisayarlar, mağaza stokları, iade ürünler ve şirket demirbaşları için teklif veriyoruz.</p>
<h2>Acer ürünlerinde alım kapsamı</h2>
<ul>
<li>Aspire ve TravelMate serisi laptoplar.</li>
<li>Nitro ve Predator oyun laptopları.</li>
<li>Veriton masaüstü bilgisayarlar.</li>
<li>Chromebook\'lar ve eğitim projelerinden çıkan cihazlar.</li>
<li>Ofis ve oyun monitörleri.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Model, işlemci, RAM, depolama ve adet bilgisi ilk fiyat için yeterli. Adaptörlerin olup olmadığını, ekranda kırık veya ölü piksel bulunup bulunmadığını ve cihazların açılıp açılmadığını belirtmeniz gerekir. Chromebook\'larda cihazın kurumsal yönetimden çıkarılmış olması fiyatı doğrudan etkiler.</p>
<p>Listenizi telefonla iletin veya WhatsApp\'tan gönderin. Aynı modelden onlarca cihaz da olabilir, karışık bir parti de; ikisi için de toplam fiyat veriyoruz.</p>',
        'faqs' => [
            ['question' => 'Okuldan çıkan Chromebook\'ları alıyor musunuz?', 'answer' => 'Evet. Kurumsal yönetimden çıkarılmış Chromebook\'lar daha yüksek fiyat alır; kayıtlı cihazlar ayrıca değerlendirilir.'],
            ['question' => 'Açılmayan laptoplar alınır mı?', 'answer' => 'Evet, arızalı ürün olarak ayrı fiyatlanır.'],
            ['question' => 'Monitörleri ayrı satabilir miyim?', 'answer' => 'Evet. Monitörleri tek başına da toplu olarak alıyoruz.'],
        ],
    ],

    [
        'name' => 'Roborock',
        'slug' => 'roborock',
        'is_featured' => true,
        'description' => 'Roborock robot süpürge ve dikey süpürgelerin toplu alımı: kapalı kutu, iade, teşhir ve arızalı.',
        'products' => ['Roborock S serisi', 'Roborock Q serisi', 'Roborock Qrevo', 'Dikey süpürge', 'Yedek parça ve aksesuar'],
        'meta_title' => 'Roborock Toplu Robot Süpürge Alımı',
        'meta_description' => 'Roborock robot süpürge toplu alımı. Kapalı kutu stok fazlası, iade, teşhir ve arızalı ürünlere hızlı fiyat teklifi. Hemen arayın.',
        'content' => '<p>Roborock robot süpürgelerini parti hâlinde satın alıyoruz. Distribütör ve bayi stok fazlası, e-ticaret iadeleri, teşhirden inen ürünler ve arızalı üniteler için teklif veriyoruz.</p>
<h2>Roborock ürünlerinde alım kapsamı</h2>
<ul>
<li><strong>Kapalı kutu:</strong> faturalı stok fazlası robot süpürgeler.</li>
<li><strong>İade:</strong> kutusu açılmış, kısa süre kullanılmış veya ambalajı hasarlı ürünler.</li>
<li><strong>Teşhir:</strong> mağaza ve fuar teşhirinden inen üniteler.</li>
<li><strong>Arızalı:</strong> şarj olmayan, lazer sensörü veya fırça motoru arızalı cihazlar.</li>
<li><strong>Aksesuar:</strong> şarj istasyonu, fırça, filtre ve mop gibi yedek parça stokları.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Model adı, adet ve ürünün durumu fiyatın temelidir. Otomatik boşaltma veya mop yıkama istasyonlu modellerde istasyonun ürünle birlikte olup olmadığı fiyatı önemli ölçüde etkiler. Ürünlerin uygulamaya bağlı bir hesaptan çıkarılmış olmasını da öneriyoruz. Arızalı ürünlerde arızayı biliyorsanız belirtin; bilmiyorsanız "çalışmıyor" demeniz yeterli.</p>
<p>Elinizdeki robot süpürge stoğunu tek seferde nakde çevirmek için bizi arayın ya da WhatsApp\'tan model ve adet listesini gönderin.</p>',
        'faqs' => [
            ['question' => 'İstasyonu eksik robot süpürgeleri alıyor musunuz?', 'answer' => 'Evet. İstasyonu veya aksesuarı eksik üniteler de alınır; eksik parça fiyata yansıtılır.'],
            ['question' => 'E-ticaret iadesi Roborock\'ları alır mısınız?', 'answer' => 'Evet. İade ürünleri kozmetik ve çalışma durumuna göre gruplayıp toplu fiyat veriyoruz.'],
            ['question' => 'Yedek parça stoklarını da alıyor musunuz?', 'answer' => 'Evet. Fırça, filtre, mop ve şarj istasyonu gibi aksesuar stoklarını da toplu alıyoruz.'],
        ],
    ],

    [
        'name' => 'Dreame',
        'slug' => 'dreame',
        'is_featured' => true,
        'description' => 'Dreame robot süpürge, dikey süpürge ve ıslak-kuru süpürgelerin toplu alımı.',
        'products' => ['Dreame robot süpürge', 'Dreame dikey süpürge', 'Islak-kuru süpürge', 'Saç kurutma makinesi'],
        'meta_title' => 'Dreame Toplu Robot ve Dikey Süpürge Alımı',
        'meta_description' => 'Dreame robot süpürge, dikey ve ıslak-kuru süpürge toplu alımı. Stok fazlası, iade, teşhir ve arızalı ürünlere hızlı teklif.',
        'content' => '<p>Dreame robot süpürgelerini, dikey süpürgelerini ve ıslak-kuru süpürgelerini toplu olarak satın alıyoruz. Bize gelen Dreame partileri genellikle bayi stok fazlası, iade ve teşhir ürünlerinden oluşuyor.</p>
<h2>Dreame ürünlerinde alım kapsamı</h2>
<ul>
<li>Robot süpürgeler: istasyonlu ve istasyonsuz modeller.</li>
<li>Kablosuz dikey süpürgeler.</li>
<li>Islak-kuru zemin temizleyiciler.</li>
<li>Kapalı kutu, iade, teşhir veya arızalı tüm durumlar.</li>
</ul>
<h2>Fiyatı belirleyen bilgiler</h2>
<p>Model adı ve adet ilk sorumuzdur. Ardından ürünün kapalı kutu mu, iade mi yoksa kullanılmış mı olduğunu, aksesuarların ve şarj ünitesinin tam olup olmadığını soruyoruz. Dikey süpürgelerde batarya sağlığı, robot süpürgelerde istasyonun varlığı fiyatı en çok etkileyen konulardır.</p>
<p>Robot süpürge ve dikey süpürgelerin karıştığı bir stok da olabilir. Listenizi telefonla iletin ya da WhatsApp\'tan gönderin; her grubu ayrı değerlendirip toplam fiyat verelim.</p>',
        'faqs' => [
            ['question' => 'Bataryası zayıf dikey süpürgeleri alıyor musunuz?', 'answer' => 'Evet. Batarya durumu fiyatı etkiler ama zayıf bataryalı ürünler de alınır.'],
            ['question' => 'Dreame ıslak-kuru süpürgeleri de alır mısınız?', 'answer' => 'Evet. Islak-kuru zemin temizleyicileri kapalı kutu, iade veya arızalı durumda alıyoruz.'],
            ['question' => 'Teşhir ürünlerinin kutusu yoksa sorun olur mu?', 'answer' => 'Hayır. Kutusuz teşhir ürünleri de alınır; kutu olmaması fiyatı bir miktar etkiler.'],
        ],
    ],

    [
        'name' => 'Viomi',
        'slug' => 'viomi',
        'is_featured' => false,
        'description' => 'Viomi robot süpürge ve küçük ev aletlerinin toplu alımı.',
        'products' => ['Viomi robot süpürge', 'Viomi dikey süpürge', 'Küçük ev aletleri'],
        'meta_title' => 'Viomi Toplu Robot Süpürge Alımı',
        'meta_description' => 'Viomi robot süpürge toplu alımı. Stok fazlası, iade, teşhir ve arızalı ürünlere hızlı fiyat teklifi. Hemen arayın.',
        'content' => '<p>Viomi robot süpürgelerini ve küçük ev aletlerini adetli olarak satın alıyoruz. Bayi ve e-ticaret kanallarından gelen stok fazlası, iade ve teşhir ürünleri en sık karşılaştığımız durumlar.</p>
<h2>Viomi ürünlerinde alım kapsamı</h2>
<ul>
<li>Robot süpürgeler: kapalı kutu, iade, teşhir veya arızalı.</li>
<li>Dikey süpürgeler.</li>
<li>Viomi markalı diğer küçük ev aletleri.</li>
</ul>
<h2>Teklif için gerekenler</h2>
<p>Model adı, adet ve ürün durumu yeterli. Şarj istasyonu, adaptör ve aksesuarların tam olup olmadığını, ürünün uygulama hesabından çıkarılıp çıkarılmadığını belirtmeniz fiyatı netleştirir. Arızalı ürünlerde arızayı tarif edebilirseniz daha doğru fiyat veririz.</p>
<p>Viomi stoklarınızı tek seferde satmak için bizi arayın ya da WhatsApp\'tan model ve adet listesini gönderin.</p>',
        'faqs' => [
            ['question' => 'Az sayıda Viomi robot süpürge için teklif alabilir miyim?', 'answer' => 'Evet. Toplu alım önceliğimiz olsa da küçük partiler için de teklif veriyoruz.'],
            ['question' => 'Uygulamaya bağlı cihazları alıyor musunuz?', 'answer' => 'Evet, ancak satış öncesi cihazların uygulama hesabından çıkarılmasını öneriyoruz.'],
            ['question' => 'Arızalı Viomi ürünleri alınır mı?', 'answer' => 'Evet. Arızalı üniteler durumlarına göre ayrı fiyatlanır.'],
        ],
    ],

    [
        'name' => 'iRobot',
        'slug' => 'irobot',
        'is_featured' => false,
        'description' => 'iRobot Roomba robot süpürge ve Braava zemin silme robotlarının toplu alımı.',
        'products' => ['Roomba robot süpürge', 'Roomba Combo', 'Braava zemin silme robotu', 'Clean Base istasyon'],
        'meta_title' => 'iRobot Roomba Toplu Alım | Robot Süpürge',
        'meta_description' => 'iRobot Roomba ve Braava toplu alımı. Kapalı kutu, iade, teşhir ve arızalı robot süpürgelere hızlı fiyat teklifi. Hemen arayın.',
        'content' => '<p>iRobot Roomba robot süpürgelerini ve Braava zemin silme robotlarını toplu olarak satın alıyoruz. Stok fazlası, iade, teşhir ve arızalı üniteler için teklif veriyoruz.</p>
<h2>iRobot ürünlerinde alım kapsamı</h2>
<ul>
<li>Roomba serisi robot süpürgeler ve Combo modeller.</li>
<li>Braava zemin silme robotları.</li>
<li>Clean Base otomatik boşaltma istasyonları.</li>
<li>Arızalı veya parçası eksik üniteler.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Roomba\'larda model numarası (örneğin j7, i3, s9 gibi seri adı), istasyonun olup olmadığı ve cihazın çalışır durumu fiyatın temelidir. Batarya durumu ve fırça-filtre gibi sarf parçaların varlığı da fiyatı etkiler. Cihazları satış öncesi uygulama hesabından çıkarmanızı öneriyoruz.</p>
<p>Model ve adet listesini telefonla iletin ya da WhatsApp\'tan gönderin; partinin tamamına toplu alım fiyatımızı verelim.</p>',
        'faqs' => [
            ['question' => 'Eski seri Roomba\'ları alıyor musunuz?', 'answer' => 'Evet. Eski seri cihazlar da alınır; fiyat model ve çalışma durumuna göre belirlenir.'],
            ['question' => 'Clean Base istasyonlarını ayrı alır mısınız?', 'answer' => 'Evet. İstasyonları tek başına da toplu olarak alıyoruz.'],
            ['question' => 'Bataryası bitmiş cihazlar alınır mı?', 'answer' => 'Evet. Batarya durumu fiyata yansıtılır.'],
        ],
    ],

    [
        'name' => 'Dyson',
        'slug' => 'dyson',
        'is_featured' => true,
        'description' => 'Dyson dikey süpürge, robot süpürge, saç bakım ve hava temizleyici ürünlerinin toplu alımı.',
        'products' => ['Dyson V serisi dikey süpürge', 'Dyson Gen5', 'Airwrap / Supersonic', 'Hava temizleyici', 'Batarya ve aksesuar'],
        'meta_title' => 'Dyson Toplu Süpürge Alımı | Dikey ve Şarjlı',
        'meta_description' => 'Dyson dikey süpürge, Airwrap, Supersonic ve hava temizleyici toplu alımı. Stok fazlası, iade ve arızalı ürünlere hızlı teklif.',
        'content' => '<p>Dyson ürünlerini adetli olarak satın alıyoruz. En sık aldığımız grup dikey şarjlı süpürgeler; saç bakım ürünleri (Airwrap, Supersonic) ve hava temizleyiciler de alım kapsamımızda.</p>
<h2>Dyson ürünlerinde toplu alım kapsamı</h2>
<ul>
<li><strong>Dikey süpürge:</strong> V serisi ve sonraki modeller; kapalı kutu, iade veya ikinci el.</li>
<li><strong>Saç bakım:</strong> Airwrap ve Supersonic; teşhir, iade veya kapalı kutu.</li>
<li><strong>Hava temizleyici ve fan:</strong> stok fazlası veya teşhir.</li>
<li><strong>Arızalı:</strong> bataryası bitmiş, motoru arızalı veya parçası eksik ürünler.</li>
<li><strong>Aksesuar:</strong> batarya, başlık ve şarj ünitesi stokları.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Model adı, adet ve durum bilgisi temeldir. Dikey süpürgelerde batarya sağlığı ve başlıkların tam olup olmadığı, saç bakım ürünlerinde ise başlık setinin eksiksiz olup olmadığı fiyatı belirler. Ürünlerin seri numarası ve fatura durumu, menşei netleştirmek için istenebilir.</p>
<p>Dyson stoklarınızı tek seferde satmak için bizi arayın veya WhatsApp\'tan model ve adet listesini gönderin.</p>',
        'faqs' => [
            ['question' => 'Fatura olmadan Dyson ürünü alıyor musunuz?', 'answer' => 'Toplu alımlarda ürünlerin menşeini netleştirmek için fatura veya sahiplik belgesi isteyebiliriz. Belgesiz ürünler ayrıca değerlendirilir.'],
            ['question' => 'Başlıkları eksik Airwrap\'leri alır mısınız?', 'answer' => 'Evet. Eksik başlıklar fiyata yansıtılır; ana ünite çalışıyorsa alınır.'],
            ['question' => 'Arızalı Dyson süpürgeler alınır mı?', 'answer' => 'Evet. Arızalı üniteler parça değeri ve onarım maliyetine göre fiyatlanır.'],
        ],
    ],

    [
        'name' => 'PlayStation',
        'slug' => 'playstation',
        'is_featured' => false,
        'description' => 'PlayStation oyun konsolu, kol ve aksesuarların toplu alımı.',
        'products' => ['PlayStation 5', 'PlayStation 4', 'DualSense / DualShock kol', 'PlayStation VR', 'Oyun disk stokları'],
        'meta_title' => 'Toplu PlayStation Alımı | PS5, PS4 ve Kol',
        'meta_description' => 'PlayStation 5, PlayStation 4, kol ve aksesuar toplu alımı. Oyun salonu kapanışı, stok fazlası ve arızalı konsollara hızlı teklif.',
        'content' => '<p>PlayStation konsollarını, kollarını ve aksesuarlarını toplu olarak satın alıyoruz. Oyun salonu ve PlayStation kafe kapanışları, mağaza stok fazlaları ve iade ürünler en sık karşılaştığımız durumlar.</p>
<h2>Alım kapsamımızdaki PlayStation ürünleri</h2>
<ul>
<li>PlayStation 5 ve PlayStation 4 konsollar (standart, slim, pro, dijital).</li>
<li>DualSense ve DualShock kollar.</li>
<li>VR setleri ve diğer aksesuarlar.</li>
<li>Oyun disk stokları.</li>
<li>Arızalı konsollar: görüntü vermeyen, disk okumayan veya aşırı ısınan cihazlar.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Konsol modeli (standart, slim, pro, dijital sürüm), depolama kapasitesi, adet ve çalışma durumu fiyatın temelidir. Kol sayısı, kablolar ve kutu durumu da önemlidir. Oyun salonu kapanışlarında konsol, kol, televizyon ve oyunlardan oluşan setlerin tamamını tek partide değerlendiriyoruz.</p>
<p>Ürün listenizi telefonla iletin ya da WhatsApp\'tan gönderin; partinin tamamına tek fiyat verelim.</p>',
        'faqs' => [
            ['question' => 'PlayStation kafe kapanışında televizyonları da alır mısınız?', 'answer' => 'Evet. Konsol, kol, televizyon ve oyunların tamamını tek partide alıyoruz.'],
            ['question' => 'Arızalı konsolları alıyor musunuz?', 'answer' => 'Evet. Görüntü vermeyen veya disk okumayan konsolları arızalı ürün olarak alıyoruz.'],
            ['question' => 'Hesaplardan çıkış yapmam gerekiyor mu?', 'answer' => 'Evet, satış öncesi kullanıcı hesaplarından çıkış yapılmasını ve konsolun sıfırlanmasını öneriyoruz.'],
        ],
    ],

    [
        'name' => 'Xbox',
        'slug' => 'xbox',
        'is_featured' => false,
        'description' => 'Xbox Series X, Series S, Xbox One konsol ve kolların toplu alımı.',
        'products' => ['Xbox Series X', 'Xbox Series S', 'Xbox One', 'Xbox kablosuz kol', 'Aksesuar'],
        'meta_title' => 'Toplu Xbox Alımı | Series X, Series S ve Kol',
        'meta_description' => 'Xbox Series X, Series S, Xbox One ve kol toplu alımı. Stok fazlası, oyun salonu kapanışı ve arızalı konsollara hızlı teklif.',
        'content' => '<p>Xbox konsollarını ve kollarını parti hâlinde satın alıyoruz. Mağaza stok fazlası, iade ürünler, oyun salonu kapanışları ve ikinci el konsol partileri için teklif veriyoruz.</p>
<h2>Xbox ürünlerinde alım kapsamı</h2>
<ul>
<li>Xbox Series X ve Series S konsollar.</li>
<li>Xbox One ailesi konsollar.</li>
<li>Kablosuz kollar ve aksesuarlar.</li>
<li>Arızalı veya parçası eksik konsollar.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Konsol modeli, depolama kapasitesi, adet ve çalışma durumu fiyatın temelidir. Kol ve kabloların konsolla birlikte olup olmadığını ve kutu durumunu belirtmeniz yeterli. Satış öncesi konsolların sıfırlanması ve kullanıcı hesaplarının kaldırılması gerekir.</p>
<p>Listenizi telefonla iletin ya da WhatsApp\'tan gönderin; partinin tamamına toplu fiyat verelim.</p>',
        'faqs' => [
            ['question' => 'Xbox kollarını ayrı alıyor musunuz?', 'answer' => 'Evet. Kolları ve aksesuarları tek başına da toplu olarak alıyoruz.'],
            ['question' => 'Kutusuz konsollar alınır mı?', 'answer' => 'Evet. Kutu olmaması fiyatı bir miktar etkiler ama kutusuz konsollar da alınır.'],
            ['question' => 'Arızalı Xbox alıyor musunuz?', 'answer' => 'Evet. Açılmayan veya görüntü vermeyen konsolları arızalı ürün olarak alıyoruz.'],
        ],
    ],

    [
        'name' => 'Nintendo',
        'slug' => 'nintendo',
        'is_featured' => false,
        'description' => 'Nintendo Switch konsol, Joy-Con ve oyunların toplu alımı.',
        'products' => ['Nintendo Switch', 'Switch OLED', 'Switch Lite', 'Joy-Con ve Pro Controller', 'Oyun kartuşları'],
        'meta_title' => 'Toplu Nintendo Switch Alımı | Konsol ve Joy-Con',
        'meta_description' => 'Nintendo Switch, Switch OLED, Switch Lite, Joy-Con ve oyun toplu alımı. Stok fazlası, iade ve arızalı ürünlere hızlı teklif.',
        'content' => '<p>Nintendo Switch konsollarını, Joy-Con kollarını ve oyunlarını toplu olarak satın alıyoruz. Mağaza stokları, iade ürünler ve ikinci el konsol partileri için teklif veriyoruz.</p>
<h2>Nintendo ürünlerinde alım kapsamı</h2>
<ul>
<li>Nintendo Switch, Switch OLED ve Switch Lite konsollar.</li>
<li>Joy-Con ve Pro Controller kollar.</li>
<li>Dock, şarj aleti ve diğer aksesuarlar.</li>
<li>Oyun kartuşu stokları.</li>
<li>Arızalı konsollar ve Joy-Con\'lar.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Konsol modeli, adet, çalışma durumu ve aksesuarların tam olup olmadığı fiyatı belirler. Joy-Con\'larda sapma (drift) sorunu olup olmadığını biliyorsanız belirtin. Konsolların satış öncesi sıfırlanması ve hesaplardan çıkarılması gerekir.</p>
<p>Listenizi telefonla iletin ya da WhatsApp\'tan gönderin; partinin tamamına toplu alım fiyatı verelim.</p>',
        'faqs' => [
            ['question' => 'Drift sorunu olan Joy-Con\'ları alıyor musunuz?', 'answer' => 'Evet. Arızalı kol olarak ayrı fiyatlanır.'],
            ['question' => 'Oyun kartuşlarını toplu alır mısınız?', 'answer' => 'Evet. Oyun kartuşu stoklarını konsollarla birlikte veya ayrı olarak alıyoruz.'],
            ['question' => 'Dock\'u eksik konsollar alınır mı?', 'answer' => 'Evet. Eksik aksesuar fiyata yansıtılır.'],
        ],
    ],

    [
        'name' => 'LG',
        'slug' => 'lg',
        'is_featured' => false,
        'description' => 'LG televizyon, monitör ve laptopların toplu alımı.',
        'products' => ['LG televizyon', 'LG monitör', 'LG gram laptop', 'Ses sistemi ve soundbar'],
        'meta_title' => 'LG Toplu Televizyon ve Monitör Alımı',
        'meta_description' => 'LG televizyon, monitör, gram laptop ve soundbar toplu alımı. Otel, mağaza ve ofis stoklarına hızlı fiyat teklifi. Hemen arayın.',
        'content' => '<p>LG televizyon, monitör, laptop ve ses ürünlerini toplu olarak satın alıyoruz. Otel ve konaklama yenilemeleri, mağaza teşhir ürünleri, iade stokları ve ofis monitörleri en sık karşılaştığımız durumlar.</p>
<h2>LG ürünlerinde alım kapsamı</h2>
<ul>
<li>Televizyonlar: farklı ekran ölçülerinde, otel ve mağaza stokları dahil.</li>
<li>Monitörler: ofis, oyun ve profesyonel kullanım.</li>
<li>gram serisi laptoplar.</li>
<li>Soundbar ve ses sistemleri.</li>
<li>Paneli kırık veya görüntü vermeyen arızalı ürünler.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Televizyon ve monitörlerde model, ekran ölçüsü, adet ve panel durumu fiyatın temelidir. Kumanda, ayak ve kablo gibi aksesuarların olup olmadığını belirtmeniz gerekir. Televizyonlar hacimli olduğu için teslim alma şeklini parti büyüklüğüne göre birlikte planlıyoruz.</p>
<p>Listenizi telefonla iletin ya da WhatsApp\'tan gönderin; partinin tamamına toplu fiyat verelim.</p>',
        'faqs' => [
            ['question' => 'Otelden çıkan televizyonları alıyor musunuz?', 'answer' => 'Evet. Otel ve konaklama tesislerinden çıkan televizyonları adetli olarak alıyoruz.'],
            ['question' => 'Paneli kırık televizyon alınır mı?', 'answer' => 'Evet, arızalı ürün olarak parça değeri üzerinden fiyatlanır.'],
            ['question' => 'Monitörleri ayaksız alır mısınız?', 'answer' => 'Evet. Ayağı olmayan monitörler de alınır; eksik parça fiyata yansıtılır.'],
        ],
    ],

    [
        'name' => 'Philips',
        'slug' => 'philips',
        'is_featured' => false,
        'description' => 'Philips monitör, televizyon, süpürge ve küçük ev aletlerinin toplu alımı.',
        'products' => ['Philips monitör', 'Philips televizyon', 'Dikey ve robot süpürge', 'Kişisel bakım ürünleri', 'Küçük ev aletleri'],
        'meta_title' => 'Philips Toplu Alım | Monitör, Süpürge, Ev Aleti',
        'meta_description' => 'Philips monitör, televizyon, süpürge ve küçük ev aleti toplu alımı. Stok fazlası, iade ve teşhir ürünlere hızlı fiyat teklifi.',
        'content' => '<p>Philips markalı monitör, televizyon, süpürge, kişisel bakım ve küçük ev aletlerini toplu olarak satın alıyoruz. Mağaza ve e-ticaret stok fazlaları, iade ürünler ve teşhir stokları en sık karşılaştığımız durumlar.</p>
<h2>Philips ürünlerinde alım kapsamı</h2>
<ul>
<li>Ofis ve oyun monitörleri.</li>
<li>Televizyonlar.</li>
<li>Dikey, robot ve elektrikli süpürgeler.</li>
<li>Tıraş makinesi, epilatör ve saç bakım ürünleri.</li>
<li>Kahve makinesi, fritöz ve benzeri küçük ev aletleri.</li>
</ul>
<h2>Teklif için gereken bilgiler</h2>
<p>Ürün grubu, model, adet ve durum bilgisi yeterlidir. Kapalı kutu ürünlerde kutunun hasarlı olup olmadığını, iade ürünlerde kullanılmışlık durumunu ve aksesuarların tam olup olmadığını belirtmeniz gerekir. Farklı ürün gruplarının karıştığı partiler için de teklif veriyoruz.</p>
<p>Philips stoklarınızı tek seferde nakde çevirmek için bizi arayın ya da WhatsApp\'tan listeyi gönderin.</p>',
        'faqs' => [
            ['question' => 'Küçük ev aletlerini de toplu alıyor musunuz?', 'answer' => 'Evet. Kapalı kutu, iade veya teşhir küçük ev aletlerini adetli olarak alıyoruz.'],
            ['question' => 'Kutusu hasarlı ürünler alınır mı?', 'answer' => 'Evet. Ürün sağlamsa kutu hasarı fiyatı yalnız bir miktar etkiler.'],
            ['question' => 'Farklı ürün gruplarını aynı partide satabilir miyim?', 'answer' => 'Evet. Her grubu ayrı değerlendirip partinin tamamına tek fiyat veriyoruz.'],
        ],
    ],

];

<?php

/*
 * Blog yazıları — 2. grup (kurumsal / IT / depo).
 * Bilgilendirici niyet: ticari terimler hizmet sayfalarına aittir; yazılar oraya link verir.
 * Uydurma bilgi yok: istatistik, fiyat, yüzde, kanun maddesi numarası veya şirket iddiası içermez.
 */

return [
    [
        'slug' => 'bilgisayarlari-satmadan-once-veri-silme-rehberi',
        'title' => 'Ofis Bilgisayarlarını Satmadan Önce Veri Silme Rehberi',
        'category' => 'Bilgisayar ve IT',
        'primary_keyword' => 'kullanılmış ofis bilgisayarlarını satmak',
        'meta_title' => 'Ofis Bilgisayarlarını Satmadan Önce Veri Silme',
        'meta_description' => 'Kullanılmış ofis bilgisayarlarını satmadan önce verileri güvenle silme, BIOS şifresi, MDM kaydı ve envanter hazırlığı için adım adım rehber.',
        'excerpt' => 'Kullanılmış ofis bilgisayarlarını satmak istiyorsanız önce verileri güvenle silmeniz, kilitleri kaldırmanız ve bir envanter listesi hazırlamanız gerekir. Bu rehber süreci adım adım anlatıyor.',
        'body_html' => <<<'HTML'
<p><strong>Kısaca:</strong> Kullanılmış ofis bilgisayarlarını satmadan önce her cihazdaki verileri geri getirilemeyecek şekilde silmeli, BIOS ve hesap kilitlerini kaldırmalı ve cihazları kurumsal yönetim (MDM) kayıtlarından çıkarmalısınız. Ardından marka, model, seri numarası ve durum bilgisini içeren bir envanter listesiyle toplu teklif almak süreci hem güvenli hem hızlı hale getirir.</p>

<h2>Neden veri silme satıştan önce gelir?</h2>
<p>Bir şirketin bilgisayarlarında müşteri kayıtları, e-posta arşivleri, muhasebe dosyaları, kayıtlı şifreler ve çalışanlara ait kişisel bilgiler bulunur. Bu cihazlar el değiştirdiğinde sorumluluk büyük ölçüde hâlâ verinin sahibi olan şirkettedir. Kişisel veri içeren cihazların elden çıkarılması, KVKK kapsamında da dikkat edilmesi gereken bir konudur. Bu nedenle silme işlemi, alıcıya güvenmekten bağımsız olarak şirketin kendi kontrolünde ve belgelenerek yapılmalıdır.</p>
<p>Veri silmeyi satışın son adımına bırakmak yaygın bir hatadır. Cihazlar toplandıktan, paketlendikten sonra tek tek açıp silmek hem zaman kaybettirir hem de bazı cihazların gözden kaçmasına yol açar. En sağlıklı yöntem, satış kararı verildiği anda veri silme planını da başlatmaktır.</p>

<h2>Satış öncesi hazırlık kontrol listesi</h2>
<p>Aşağıdaki liste, toplu satıştan önce her cihaz için tamamlanması gereken işleri özetler:</p>
<ul>
<li>Saklanması gereken dosyaların merkezi bir sunucuya veya buluta yedeklenmesi</li>
<li>Kullanıcı hesaplarının, e-posta istemcilerinin ve tarayıcı oturumlarının kapatılması</li>
<li>Lisanslı yazılımların cihazdan devre dışı bırakılması (lisansın başka cihaza taşınabilmesi için)</li>
<li>Diskin güvenli yöntemle sıfırlanması</li>
<li>BIOS / UEFI yönetici şifresinin kaldırılması</li>
<li>Cihazın MDM, Apple Business veya benzeri kurumsal yönetim sisteminden çıkarılması</li>
<li>Seri numarası ve durum bilgisinin envanter listesine işlenmesi</li>
</ul>

<h2>Adım adım güvenli veri silme</h2>
<h3>1. Yedekleme ve hesap kapatma</h3>
<p>Silme işleminden önce, işletmenin ihtiyaç duyacağı belgelerin yedeklendiğinden emin olun. Çalışanlardan cihazlarındaki kişisel dosyaları ayırmalarını isteyin. Ardından işletim sistemindeki kullanıcı hesaplarını, kurumsal e-posta bağlantılarını ve bulut senkronizasyon uygulamalarını kapatın. Bu adım, cihaz sıfırlandığında bile bir hesabın uzaktan yeniden bağlanmasını engeller.</p>

<h3>2. Diskin güvenli sıfırlanması</h3>
<p>Dosyaları çöp kutusuna atıp boşaltmak veya hızlı biçimlendirme yapmak veriyi gerçekten silmez; uygun araçlarla geri getirilebilir. Güvenli silme için şu yaklaşımlardan biri tercih edilir:</p>
<ul>
<li><strong>Disk şifrelemesi açık cihazlarda:</strong> Şifreleme anahtarının silinip ardından cihazın fabrika ayarlarına döndürülmesi, verileri okunamaz hale getirir.</li>
<li><strong>SSD diskler:</strong> Üreticinin sunduğu güvenli silme (secure erase) özelliği kullanılır. SSD'lerde klasik "üzerine yazma" yöntemleri her hücreye ulaşamayabilir.</li>
<li><strong>Mekanik diskler (HDD):</strong> Diskin tamamının üzerine yazan silme araçları kullanılır.</li>
<li><strong>Çalışmayan diskler:</strong> Disk açılamıyorsa veri silinemez; bu durumda diskin cihazdan sökülüp ayrıca imha edilmesi değerlendirilir.</li>
</ul>
<p>Hangi yöntemi seçerseniz seçin, her cihaz için silme işleminin yapıldığını envanter listesinde işaretleyin. Toplu satışlarda bu kayıt, hem iç denetim hem de alıcıyla yapılan görüşmeler için güven verir.</p>

<h3>3. BIOS / UEFI şifresinin kaldırılması</h3>
<p>Kurumsal bilgisayarlarda BIOS yönetici şifresi sık kullanılır. Bu şifre kaldırılmazsa alıcı cihazı yeniden kuramaz, bu da cihazın değerini ciddi şekilde düşürür. Şifreler genellikle BT ekibinin elindeki bir listede ya da yönetim yazılımında tutulur. Satış öncesinde şifrelerin kaldırıldığını bir örnek cihazda test etmek iyi bir alışkanlıktır.</p>

<h3>4. MDM ve Apple Business kayıtlarından çıkarma</h3>
<p>Mobil cihaz yönetimi (MDM) sistemine kayıtlı bilgisayarlar, sıfırlansa bile ilk açılışta şirketin yönetim sistemine bağlanmaya çalışır. MacBook'larda Apple Business Manager kaydı, Windows cihazlarda ise Autopilot veya benzeri kayıtlar bu duruma yol açar. Cihazların bu sistemlerden kaldırılması, BT yöneticisinin panelden yapması gereken bir işlemdir. Kayıttan çıkarılmayan cihazlar alıcı için kullanılamaz durumdadır ve teklif buna göre şekillenir.</p>

<h2>Envanter listesi nasıl hazırlanır?</h2>
<p>Toplu teklif almanın en hızlı yolu düzenli bir envanter listesidir. Basit bir tablo yeterlidir; önemli olan her satırın tek bir cihazı ya da aynı özellikteki bir grubu net tanımlamasıdır.</p>
<ol>
<li><strong>Marka ve model:</strong> Örneğin "Dell Latitude", "Lenovo ThinkPad" gibi, mümkünse tam model koduyla.</li>
<li><strong>Donanım:</strong> İşlemci, RAM ve disk kapasitesi. Aynı model farklı donanımlarla gelebilir.</li>
<li><strong>Seri numarası:</strong> Hem takip hem de veri silme kaydı için.</li>
<li><strong>Adet:</strong> Aynı özellikteki cihazlar tek satırda toplanabilir.</li>
<li><strong>Durum:</strong> Çalışır, kozmetik kusurlu, ekranı arızalı, açılmıyor gibi kısa notlar.</li>
<li><strong>Aksesuar:</strong> Şarj adaptörü, dock istasyonu, çanta olup olmadığı.</li>
</ol>
<p>Listeyi hazırlarken cihazları durumlarına göre gruplara ayırmak (lotlama) teklif sürecini hızlandırır. Çalışan ve temiz cihazlar bir grupta, arızalı veya parça olarak değerlendirilecek cihazlar başka bir grupta toplanabilir.</p>

<h2>Silme işlemi nasıl belgelenir?</h2>
<p>İç denetim veya ileride doğabilecek sorular için silme işlemini belgelemek iyi bir uygulamadır. Envanter tablosuna "silme yöntemi", "silme tarihi" ve "işlemi yapan kişi" sütunları eklemek basit ama etkili bir yoldur. Bazı silme araçları işlem sonunda rapor üretir; bu raporları cihaz seri numarasıyla eşleştirerek saklayabilirsiniz.</p>

<h2>Monitör, dock ve çevre birimleri</h2>
<p>Ofis yenilemesinde yalnızca bilgisayarlar değil; monitörler, klavye-fare setleri, yazıcılar, dock istasyonları ve ağ cihazları da elden çıkarılır. Bunları ayrı bir liste halinde eklemek, tüm ekipmanın tek seferde değerlendirilmesini sağlar. Yazıcı ve çok fonksiyonlu cihazlarda da dahili hafıza veya disk bulunabileceğini unutmayın; bunların da sıfırlanması gerekir.</p>

<h2>Satış ve teslim süreci</h2>
<p>Hazırlık tamamlandığında süreç genellikle şu adımlarla ilerler:</p>
<ol>
<li>Envanter listesi ve gerekiyorsa birkaç örnek fotoğraf alıcıya iletilir.</li>
<li>Alıcı, liste üzerinden toplu fiyat teklifi verir; gerekiyorsa yerinde kontrol planlanır.</li>
<li>Anlaşma sağlandığında teslim günü ve yeri belirlenir.</li>
<li>Teslimde cihaz sayısı ve durumu listeyle karşılaştırılır.</li>
<li>Satış belgelenir; şirket satışlarında fatura düzenlenir.</li>
</ol>
<p>Demirbaş olarak kayıtlı cihazların kayıttan düşülmesi ve satışın muhasebeleştirilmesi için mali müşavirinizle görüşmeniz önerilir. Bu adım, satıştan sonra kayıtlarda tutarsızlık oluşmasını önler.</p>

<h2>Sık yapılan hatalar</h2>
<ul>
<li>Yalnızca masaüstündeki dosyaları silip diski sıfırlamamak</li>
<li>BIOS şifrelerini kaldırmayı unutmak</li>
<li>Cihazları MDM'den çıkarmadan teslim etmek</li>
<li>Envanter listesi olmadan, "toplam şu kadar bilgisayar var" diye teklif istemek</li>
<li>Arızalı cihazları ayrı not etmemek; bu da teslimde liste ile gerçek durum arasında fark yaratır</li>
</ul>

<h2>BT ekibi için iş bölümü ve zaman planı</h2>
<p>Onlarca ya da yüzlerce cihazın hazırlanması, tek bir kişinin birkaç saatte bitirebileceği bir iş değildir. Süreci aşamalara bölmek ve sorumlulukları netleştirmek hem hataları azaltır hem de satış tarihini öngörülebilir kılar. Pratik bir iş bölümü şöyle olabilir:</p>
<ul>
<li><strong>Toplama:</strong> Cihazların çalışanlardan teslim alınması ve tek bir odada toplanması. Bu aşamada şarj adaptörleri ve çantalar da birlikte istenir.</li>
<li><strong>Yedekleme kontrolü:</strong> Her kullanıcının dosyalarının merkezi sisteme aktarıldığının teyidi.</li>
<li><strong>Silme ve kilit kaldırma:</strong> Disk sıfırlama, BIOS şifresi ve MDM kaydı işlemlerinin cihaz cihaz yapılması.</li>
<li><strong>Kayıt:</strong> Her cihazın seri numarası, donanımı, durumu ve silme bilgisinin envantere işlenmesi.</li>
<li><strong>Paketleme:</strong> Cihazların gruplara göre kolilenmesi ve kolilerin üzerine içerik etiketi yapıştırılması.</li>
</ul>
<p>Cihazlar yenileme döneminde kademeli olarak boşa çıkıyorsa, her hafta biriken cihazları hazırlayıp tek seferde satmak yerine bekleyen bir "hazır" grubu oluşturmak işe yarar. Böylece satış günü geldiğinde yalnızca son birkaç cihaz hazırlanır. Uzaktan çalışan personelin evinde kalan cihazlar için de toplama tarihini önceden duyurmak, envanterin eksiksiz olmasını sağlar.</p>
<p>Satış tarihini belirlerken yeni cihazların kurulumunun tamamlanmasını beklemek de önemlidir. Eski cihazlar, yeni sistemlerin sorunsuz çalıştığı doğrulanana kadar yedek olarak tutulabilir; ardından toplu olarak satışa hazırlanır.</p>

<h2>Kimlerle çalışmalı?</h2>
<p>Kurumsal cihazlarda alıcının toplu alım deneyimi önemlidir. Çok sayıda farklı modeli tek seferde değerlendirebilen, teslim planını şirketin çalışma düzenine göre ayarlayabilen ve fatura sürecini düzgün yürüten bir alıcı tercih edilmelidir. Bu konuda <a href="/kurumsal-elektronik-alimi">kurumsal elektronik ve IT ekipmanı alımı</a> sayfamızda çalışma şeklimizi bulabilirsiniz. Yalnızca dizüstü ve masaüstü bilgisayarlarınız varsa <a href="/toplu-bilgisayar-alimi">toplu bilgisayar alımı</a> sayfası daha uygun olacaktır. Ankara veya Kocaeli'deyseniz <a href="/ankara/toplu-bilgisayar-alimi">Ankara toplu bilgisayar alımı</a> ve <a href="/kocaeli/toplu-bilgisayar-alimi">Kocaeli toplu bilgisayar alımı</a> sayfalarına göz atabilirsiniz.</p>

<h2>Sonuç</h2>
<p>Kullanılmış ofis bilgisayarlarını satmak, doğru hazırlıkla hem güvenli hem de pratik bir süreçtir. Verileri güvenle silmek, kilitleri kaldırmak ve düzenli bir envanter hazırlamak; cihazlarınızın değerini korur ve toptan satışı hızlandırır. Listeniz hazırsa bizi arayın ya da <a href="/teklif-al">teklif formunu</a> doldurun; cihazlarınız için toplu fiyat teklifimizi iletelim.</p>
HTML,
        'faqs' => [
            ['question' => 'Bilgisayarı fabrika ayarlarına döndürmek veri silmek için yeterli mi?', 'answer' => 'Disk şifrelemesi açık bir cihazda fabrika ayarlarına dönmek genellikle verileri okunamaz hale getirir. Şifreleme kapalıysa ya da emin değilseniz, disk türüne uygun güvenli silme aracı kullanmanız daha doğrudur. Her durumda silme işlemini envanter listesinde kayıt altına alın.'],
            ['question' => 'BIOS şifresi kaldırılmamış bilgisayarlar satılabilir mi?', 'answer' => 'Satılabilir, ancak alıcı cihazı yeniden kuramayacağı için değeri düşer. Şifreleri BT ekibiniz kaldırabilir; kaldırılamayan cihazları envanter listesinde ayrıca belirtmeniz teklifin doğru verilmesini sağlar.'],
            ['question' => 'Arızalı ofis bilgisayarları da toplu satışa dahil edilebilir mi?', 'answer' => 'Evet, arızalı cihazlar da değerlendirilir. Bunları çalışan cihazlardan ayrı bir grupta listelemek ve arızayı kısaca not etmek, teklif sürecini hızlandırır. Disk açılamıyorsa veri güvenliği için diskin ayrıca imha edilmesini değerlendirin.'],
            ['question' => 'Satış için fatura gerekli mi?', 'answer' => 'Şirket adına kayıtlı cihazların satışında fatura düzenlenmesi beklenir. Demirbaş kaydından düşme ve muhasebe işlemleri için mali müşavirinizle görüşmeniz önerilir.'],
        ],
    ],

    [
        'slug' => 'sirket-demirbaslari-nasil-satilir',
        'title' => 'Şirket Demirbaşları Nasıl Satılır? Elektronik Ekipman Rehberi',
        'category' => 'Kurumsal ve Tasfiye',
        'primary_keyword' => 'şirket demirbaşlarını nasıl satarım',
        'meta_title' => 'Şirket Demirbaşları Nasıl Satılır? Adım Adım Rehber',
        'meta_description' => 'Şirket demirbaşlarını nasıl satarım diye düşünüyorsanız: envanter, veri güvenliği, lotlama, teklif, teslim ve fatura adımlarını anlatan pratik rehber.',
        'excerpt' => 'Ofis taşıma, yenileme veya küçülme sonrasında elinizde kalan bilgisayar, telefon, monitör ve diğer elektronik demirbaşları toplu satarken izlemeniz gereken adımlar.',
        'body_html' => <<<'HTML'
<p><strong>Kısaca:</strong> Şirket demirbaşlarını satmak için önce bir envanter listesi çıkarın, kişisel veri içeren cihazları güvenle sıfırlayın ve ekipmanı durumuna göre gruplara ayırın. Ardından listeyle toplu teklif alıp teslim planını yapın; satışı fatura ile belgeleyin ve demirbaş kayıtları için mali müşavirinizle görüşün.</p>

<h2>Demirbaş satışı ne zaman gündeme gelir?</h2>
<p>Şirketler elektronik demirbaşlarını genellikle belirli dönemlerde toplu olarak elden çıkarır. Ofis taşıma, donanım yenileme, uzaktan çalışmaya geçiş, şube kapatma, birleşme veya şirket tasfiyesi bu dönemlerin başında gelir. Bu durumlarda tek tek cihaz satmak hem çok zaman alır hem de iş yükünü artırır. Tüm ekipmanın tek seferde, tek alıcıya toptan satılması çoğu şirket için en pratik yoldur.</p>

<h2>Hangi demirbaşlar satılabilir?</h2>
<p>Elektronik demirbaşların büyük çoğunluğu, durumu ne olursa olsun değerlendirilebilir:</p>
<ul>
<li>Dizüstü ve masaüstü bilgisayarlar, iş istasyonları</li>
<li>Monitörler, dock istasyonları, klavye ve fare setleri</li>
<li>Şirket telefonları ve tabletler</li>
<li>Sunucular, ağ anahtarları (switch), modem ve router'lar</li>
<li>Yazıcılar, tarayıcılar, projeksiyon cihazları</li>
<li>Toplantı odası ekranları, kameralar ve ses sistemleri</li>
<li>POS cihazları, barkod okuyucular gibi mağaza ekipmanları</li>
</ul>
<p>Çalışmayan veya eksik parçalı cihazlar da listeye eklenebilir. Arızalı ekipman genellikle parça değeri üzerinden değerlendirilir, bu yüzden "nasılsa bozuk" diye ayırmak yerine durumu not ederek dahil etmek daha doğrudur.</p>

<h2>Adım adım demirbaş satışı</h2>
<h3>1. Envanter listesini çıkarın</h3>
<p>Satışın temeli doğru bir listedir. Muhasebedeki demirbaş kayıtları iyi bir başlangıç noktasıdır ama gerçekte nelerin elde olduğunu fiziksel sayımla doğrulamak gerekir. Listede her satır için şu bilgiler bulunmalıdır:</p>
<ol>
<li>Ürün türü (laptop, telefon, monitör…)</li>
<li>Marka ve model</li>
<li>Seri numarası veya demirbaş numarası</li>
<li>Adet</li>
<li>Durum: çalışır, kozmetik kusurlu, arızalı</li>
<li>Kutu, şarj aleti, aksesuar bilgisi</li>
</ol>

<h3>2. Veri güvenliğini sağlayın</h3>
<p>Bilgisayar, telefon, tablet, sunucu ve hatta yazıcılar şirket ve çalışan verisi taşıyabilir. Kişisel veri içeren cihazların elden çıkarılması KVKK açısından da dikkat gerektirir. Satış öncesinde:</p>
<ul>
<li>Diskleri güvenli yöntemle sıfırlayın, telefon ve tabletleri fabrika ayarlarına döndürün.</li>
<li>Hesap kilitlerini (iCloud, Google hesabı) ve BIOS şifrelerini kaldırın.</li>
<li>Cihazları MDM veya Apple Business gibi kurumsal yönetim sistemlerinden çıkarın.</li>
<li>Silme işlemini her cihaz için envanter listesinde işaretleyin.</li>
</ul>

<h3>3. Ekipmanı gruplara ayırın (lotlama)</h3>
<p>Aynı model ve durumdaki cihazları bir araya getirmek teklif sürecini kolaylaştırır. Örneğin "çalışır durumda laptoplar", "ekranı arızalı laptoplar", "monitörler", "ağ ekipmanları" gibi gruplar oluşturabilirsiniz. Lotlama, alıcının listeyi hızlı değerlendirmesini ve teslimde sayım yapılmasını kolaylaştırır.</p>

<h3>4. Toplu teklif alın</h3>
<p>Envanter listesini ve gerekiyorsa her gruptan birkaç fotoğrafı alıcıya iletin. Liste ne kadar net olursa teklif o kadar hızlı ve isabetli çıkar. Büyük envanterlerde alıcı yerinde kontrol isteyebilir; bu durumda uygun bir gün planlayın.</p>

<h3>5. Teslim planını yapın</h3>
<p>Teslim günü, ekipmanın bulunduğu katlar, asansör durumu, yükleme alanı ve şirketin çalışma saatleri birlikte değerlendirilmelidir. Ofis taşıma sürecinde demirbaş satışı yapılıyorsa teslimi taşınma takvimiyle uyumlu planlamak, eski ofisin zamanında boşaltılmasını sağlar.</p>

<h3>6. Satışı belgeleyin</h3>
<p>Teslimde cihaz adetleri ve durumları listeyle karşılaştırılır. Şirket satışlarında fatura düzenlenir. Demirbaşların kayıttan düşülmesi ve satışın muhasebeleştirilmesi için mali müşavirinizle görüşmeniz önerilir.</p>

<h2>Ofis taşıma takvimiyle uyum</h2>
<p>Demirbaş satışı çoğu zaman bir ofis taşınmasıyla aynı döneme denk gelir. Bu durumda en sık yaşanan sorun, yeni ofise götürülmeyecek ekipmanın taşınma gününe kadar eski ofiste unutulmasıdır. Taşınma planının en başında "yeni ofise gidecekler" ve "satılacaklar" listelerini ayırmak, nakliye maliyetini de azaltır; satılacak ekipmanı yeni ofise taşıyıp oradan satmak gereksiz bir iş yüküdür.</p>
<p>Pratik bir sıralama şöyle olabilir:</p>
<ol>
<li>Taşınmadan birkaç hafta önce satılacak ekipmanın listesini çıkarın.</li>
<li>Yeni cihazlar kurulurken eskileri toplayıp veri silme işlemine başlayın.</li>
<li>Teklifi taşınma tarihinden önce alın ve teslim gününü belirleyin.</li>
<li>Teslimi, eski ofisin boşaltılmasından önceki bir güne planlayın.</li>
</ol>
<p>Böylece taşınma günü yalnızca yeni ofise gidecek ekipmanla ilgilenirsiniz ve eski ofis zamanında, eksiksiz boşaltılır.</p>
<h3>Uzaktan çalışan ekiplerdeki cihazlar</h3>
<p>Hibrit veya uzaktan çalışma düzeninde şirket cihazlarının bir kısmı çalışanların evinde bulunur. Bu cihazların toplanması ofisteki ekipmana göre daha uzun sürer. Toplama için net bir son tarih belirlemek, cihazı kargo ile göndermesi gereken çalışanlara paketleme talimatı vermek ve gelen cihazları listede işaretlemek, envanterin eksiksiz tamamlanmasını sağlar. Gelmeyen cihazlar için ayrı bir takip listesi tutmak, satış gününde eksik kalemlerle karşılaşmayı önler. Kargo ile gelen cihazların teslim alınırken hasar kontrolünden geçirilmesi ve durumunun listeye hemen işlenmesi de teklifin doğru kalması için önemlidir.</p>

<h2>Demirbaş satışında sık yapılan hatalar</h2>
<ul>
<li><strong>Listesiz teklif istemek:</strong> "Yaklaşık 50 bilgisayar var" gibi bir bilgiyle verilen teklif, teslimde değişmek zorunda kalabilir.</li>
<li><strong>Veri silmeyi alıcıya bırakmak:</strong> Verinin sorumluluğu şirkettedir; silme işlemini kendi kontrolünüzde yapın.</li>
<li><strong>Kilitli cihazları belirtmemek:</strong> Hesap veya BIOS kilidi olan cihazlar ayrı değerlendirilir; listede belirtmek sürprizleri önler.</li>
<li><strong>Parça parça satmaya çalışmak:</strong> Envanteri birden fazla alıcıya bölmek iş yükünü artırır ve en zor satılan kalemler elde kalır.</li>
<li><strong>Teslim gününü son ana bırakmak:</strong> Ofis boşaltma tarihi yaklaşırken acele edilen satışlar pazarlık gücünü azaltır.</li>
</ul>

<h2>Satış kararı ve iç onay süreci</h2>
<p>Demirbaş satışı çoğu şirkette birden fazla birimi ilgilendirir. BT ekibi cihazların teknik hazırlığından, idari işler ekibi toplama ve teslimden, finans birimi ise kayıtlardan sorumludur. Satışa başlamadan önce kimin onay vereceğini ve kimin alıcıyla iletişim kuracağını belirlemek, sürecin yarıda kalmasını önler.</p>
<p>Bazı şirketlerde cihazların bir kısmının çalışanlara devredilmesi, bir kısmının bağışlanması ya da başka bir şubeye aktarılması da gündeme gelir. Bu kararlar satıştan önce verilmeli ve envanter listesi buna göre güncellenmelidir. Aksi halde satış listesinde yer alan bir cihazın teslim günü başka bir yere gittiği ortaya çıkabilir.</p>

<h2>Telefon ve tabletlerde dikkat edilecekler</h2>
<p>Şirket telefonları ve tabletleri bilgisayarlara göre farklı hazırlık gerektirir. Bu cihazlarda en sık karşılaşılan sorun hesap kilitleridir. iPhone ve iPad'lerde "Bul" özelliği açık bırakılırsa cihaz sıfırlansa bile önceki hesaba bağlı kalır. Android cihazlarda ise Google hesabı kaldırılmadan yapılan sıfırlama, cihazın ilk açılışta eski hesabı istemesine neden olur. Bu yüzden:</p>
<ul>
<li>Cihazdaki hesaplar sıfırlamadan önce kaldırılmalıdır.</li>
<li>Kurumsal yönetim sistemine kayıtlı cihazlar panelden serbest bırakılmalıdır.</li>
<li>SIM kartlar ve hafıza kartları cihazlardan çıkarılmalıdır.</li>
<li>Kilit kaldırılamayan cihazlar listede ayrıca belirtilmelidir.</li>
</ul>

<h2>Teklifi değerlendirirken nelere bakmalı?</h2>
<p>Toplu tekliflerde yalnızca toplam rakama değil, teklifin kapsamına da bakmak gerekir. Teklifin tüm envanteri mi yoksa yalnızca belirli kalemleri mi kapsadığı, teslim alma şekli, ödeme şekli ve fatura süreci netleştirilmelidir. Arızalı ve kilitli cihazların nasıl değerlendirildiğini sormak da teslimde farklılık yaşanmasını önler. Net bir liste üzerinden verilen teklifte bu konular baştan konuşulduğu için süreç sürprizsiz ilerler.</p>

<h2>Şirket tasfiyesi ve şube kapanışlarında</h2>
<p>Tasfiye sürecindeki şirketlerde demirbaş satışı genellikle daha kapsamlı ve zamana bağlıdır. Bu durumda tüm elektronik ekipmanı tek seferde değerlendirebilen bir alıcıyla çalışmak süreci kısaltır. Tasfiye ve mağaza kapanışı süreçlerinde nasıl çalıştığımızı <a href="/tasfiye-elektronik-alimi">tasfiye ve mağaza kapanış ürünleri alımı</a> sayfamızda bulabilirsiniz.</p>

<h2>Kime satmalı?</h2>
<p>Kurumsal demirbaşlarda doğru alıcı; farklı türdeki ekipmanı tek seferde değerlendirebilen, teslimi şirketin düzenine göre planlayabilen ve faturalı çalışan bir toplu alıcıdır. Bilgisayar, telefon, monitör ve ağ ekipmanı gibi karışık envanterler için <a href="/kurumsal-elektronik-alimi">kurumsal elektronik ve IT ekipmanı alımı</a> sayfamıza göz atabilirsiniz. Envanteriniz ağırlıklı olarak bilgisayardan oluşuyorsa <a href="/toplu-bilgisayar-alimi">toplu bilgisayar alımı</a> sayfası da yol gösterici olacaktır. Hangi illerde çalıştığımızı görmek için <a href="/bolgeler">bölgeler</a> sayfasına bakabilirsiniz.</p>

<h2>Sonuç</h2>
<p>Şirket demirbaşlarını satmak; envanter, veri güvenliği, lotlama, teklif, teslim ve belgeleme adımlarıyla düzenli bir sürece dönüştüğünde hem hızlı hem güvenli olur. Toptan satış, tek tek alıcı aramanın getirdiği iş yükünü ortadan kaldırır. Envanter listeniz hazırsa bizi arayın ya da <a href="/teklif-al">teklif formunu</a> doldurun; demirbaşlarınız için toplu fiyat teklifimizi iletelim.</p>
HTML,
        'faqs' => [
            ['question' => 'Şirket demirbaşlarını satmak için hangi bilgiler gerekir?', 'answer' => 'Ürün türü, marka-model, seri veya demirbaş numarası, adet ve durum bilgisini içeren bir envanter listesi yeterlidir. Kutu ve aksesuar durumunu da eklemek teklifin daha isabetli olmasını sağlar.'],
            ['question' => 'Farklı türdeki ekipmanlar tek seferde satılabilir mi?', 'answer' => 'Evet. Bilgisayar, telefon, monitör, yazıcı ve ağ ekipmanı gibi karışık envanterler tek liste üzerinden toplu olarak değerlendirilebilir. Aynı türdeki cihazları gruplamak süreci hızlandırır.'],
            ['question' => 'Demirbaş kayıtlarından düşme işlemi nasıl yapılır?', 'answer' => 'Bu işlem şirketin muhasebe kayıtlarıyla ilgilidir. Satışın belgelenmesi ve demirbaşların kayıttan düşülmesi için mali müşavirinizle görüşmeniz önerilir.'],
            ['question' => 'Çalışmayan demirbaşlar da alınır mı?', 'answer' => 'Arızalı veya eksik parçalı cihazlar da değerlendirilir. Bunları envanter listesinde ayrı bir grupta ve kısa bir arıza notuyla belirtmeniz yeterlidir.'],
        ],
    ],

    [
        'slug' => 'magaza-kapanisinda-elektronik-stok-satisi',
        'title' => 'Mağaza Kapanışında Elektronik Stok Nasıl Satılır?',
        'category' => 'Kurumsal ve Tasfiye',
        'primary_keyword' => 'mağaza stoğunu toplu satmak istiyorum',
        'meta_title' => 'Mağaza Kapanışında Elektronik Stok Nasıl Satılır?',
        'meta_description' => 'Mağaza stoğunu toplu satmak istiyorum diyorsanız: stok sayımı, teşhir ve iade ürünler, lotlama, teklif ve teslim planı için adım adım rehber.',
        'excerpt' => 'Mağazanızı kapatıyor veya devrediyorsanız elinizdeki elektronik stoğu, teşhir ürünlerini ve mağaza ekipmanlarını tek seferde satmak için izlenecek yol.',
        'body_html' => <<<'HTML'
<p><strong>Kısaca:</strong> Mağaza stoğunu toplu satmak için önce sayım yapıp ürünleri sıfır, teşhir, iade ve arızalı olarak ayırın; ardından model ve adet bilgisini içeren bir listeyle toplu teklif alın. Mağaza ekipmanlarını da aynı listeye eklemek ve teslimi kapanış tarihine göre planlamak, dükkânı zamanında ve tek seferde boşaltmanızı sağlar.</p>

<h2>Kapanış sürecinde stok neden sorun olur?</h2>
<p>Bir mağazanın kapanışı ya da devri genellikle sınırlı bir sürede gerçekleşir. Kira sözleşmesinin bitişi, devir tarihi veya yeni işletmenin teslim alma günü belli bir takvim oluşturur. Bu takvim içinde rafta kalan ürünleri tek tek satmak çoğu zaman mümkün değildir. İndirim kampanyaları stoğun bir kısmını eritse de en çok satan ürünler hızla biter, yavaş dönen ürünler ve teşhirler elde kalır. Bu noktada stoğun tamamını tek seferde toptan satmak, hem zamanı hem de iş yükünü yönetilebilir hale getirir.</p>

<h2>Satılabilecek ürün ve ekipmanlar</h2>
<p>Elektronik mağazası kapanışlarında değerlendirilebilecek kalemler genellikle şunlardır:</p>
<ul>
<li>Kapalı kutu sıfır ürünler: telefon, tablet, laptop, robot süpürge, kulaklık, akıllı saat</li>
<li>Teşhir ürünleri ve açık kutu ürünler</li>
<li>Müşteri iadeleri ve servisten dönen ürünler</li>
<li>Aksesuar stokları: kılıf, şarj aleti, kablo, ekran koruyucu</li>
<li>Arızalı veya parça eksik ürünler</li>
<li>Mağaza ekipmanları: POS cihazları, barkod okuyucular, bilgisayarlar, ekranlar, kamera sistemleri</li>
</ul>

<h2>Adım adım mağaza stoğu satışı</h2>
<h3>1. Sayım yapın ve stok listesini güncelleyin</h3>
<p>Mağaza yazılımındaki stok kayıtları ile raftaki gerçek durum zamanla farklılaşabilir. Kapanış öncesinde fiziksel sayım yapmak, alıcıya verilen listenin doğru olmasını sağlar. Sayım sırasında her ürünün modeli, varyantı (renk, kapasite) ve adedi not edilmelidir.</p>

<h3>2. Ürünleri durumuna göre ayırın</h3>
<p>Aynı model bile farklı durumlarda farklı değerlendirilir. Bu nedenle stoğu şu gruplara ayırmak teklif sürecini hızlandırır:</p>
<ol>
<li><strong>Sıfır / kapalı kutu:</strong> Ambalajı açılmamış ürünler</li>
<li><strong>Teşhir / açık kutu:</strong> Mağazada sergilenmiş veya kutusu açılmış ürünler</li>
<li><strong>İade:</strong> Müşteriden geri dönmüş ürünler</li>
<li><strong>Arızalı:</strong> Çalışmayan veya parça eksik ürünler</li>
<li><strong>Aksesuar:</strong> Kılıf, kablo, şarj aleti gibi küçük ürünler</li>
<li><strong>Ekipman:</strong> Mağazanın kendi kullandığı cihazlar</li>
</ol>

<h3>3. Teşhir ve demo cihazlarını hazırlayın</h3>
<p>Teşhirdeki telefon, tablet ve bilgisayarlarda genellikle demo modları, mağaza hesapları veya güvenlik kilitleri bulunur. Satış öncesinde bu cihazları fabrika ayarlarına döndürmek ve varsa hesap kilitlerini kaldırmak, değerlerini korur. Kaldırılamayan kilitleri listede belirtmek teslimde sürpriz yaşanmasını önler. Mağaza bilgisayarları ve POS cihazlarında ise müşteri ve satış verileri bulunabileceğinden bu cihazların güvenli şekilde sıfırlanması gerekir.</p>

<h3>4. Toplu teklif alın</h3>
<p>Hazırladığınız listeyi, her gruptan birkaç fotoğrafla birlikte alıcıya iletin. Listenin net olması teklifin hızını belirler. Büyük stoklarda alıcı mağazada yerinde kontrol yapmak isteyebilir; kapanış takviminiz sıkışıksa bunu en baştan belirtin.</p>

<h3>5. Teslimi kapanış tarihine göre planlayın</h3>
<p>Teslim günü, mağazanın boşaltılması gereken tarihten önce olmalıdır. Ürünlerin kolilenmesi, yükleme alanı ve alışveriş merkezi içindeki mağazalarda yük kabul saatleri gibi detaylar önceden konuşulmalıdır.</p>

<h3>6. Satışı belgeleyin</h3>
<p>Teslimde ürünler listeyle karşılaştırılır ve satış fatura ile belgelenir. Kapanışa bağlı muhasebe işlemleri için mali müşavirinizle görüşmeniz önerilir.</p>

<h2>Mağaza ekipmanlarını unutmayın</h2>
<p>Kapanış sırasında dikkatler raftaki ürünlere yoğunlaşır, oysa mağazanın kendi kullandığı elektronik ekipman da değerlendirilebilir. Kasa bilgisayarları, POS cihazları, barkod okuyucular, fiş yazıcıları, vitrin ekranları, güvenlik kameraları ve ağ cihazları bu gruba girer. Bu ekipmanları ayrı bir bölüm olarak listeye eklemek, mağazanın tek seferde boşaltılmasına katkı sağlar.</p>
<p>Ekipmanlarda iki konuya dikkat edin: Birincisi, kasa ve POS sistemlerinde satış ve müşteri verisi bulunabilir; bu cihazlar sıfırlanmadan teslim edilmemelidir. İkincisi, bazı POS cihazları ve kamera sistemleri kiralık ya da bankaya veya hizmet sağlayıcıya ait olabilir. Mülkiyeti işletmeye ait olmayan ekipmanı listeden çıkarıp ilgili firmaya iade sürecini ayrıca başlatmak gerekir.</p>
<h3>Konsinye ve tedarikçiye iade edilebilecek ürünler</h3>
<p>Bazı ürünler mağazaya konsinye olarak bırakılmış ya da tedarikçiyle iade anlaşması kapsamında olabilir. Kapanış öncesinde tedarikçi sözleşmelerini gözden geçirip iade edilebilecek ürünleri belirlemek, satılacak stoğun doğru tanımlanmasını sağlar. Konsinye ürünlerin toplu satış listesinde yer alması, sonradan tedarikçiyle anlaşmazlık yaşanmasına yol açabilir. Bu ayrımı yaptıktan sonra elinizde kalan ve mülkiyeti size ait olan stok, toplu satış listesinin temelini oluşturur. Tedarikçiye iade süreci uzun sürecekse bu ürünleri ayrı kolilerde ve açıkça etiketlenmiş şekilde bekletmek, teslim günü karışıklığı önler.</p>

<h2>Kontrol listesi: kapanıştan önce</h2>
<ul>
<li>Fiziksel stok sayımı yapıldı mı?</li>
<li>Ürünler sıfır, teşhir, iade ve arızalı olarak ayrıldı mı?</li>
<li>Teşhir cihazları sıfırlandı, kilitler kaldırıldı mı?</li>
<li>Mağaza bilgisayarları ve POS cihazlarındaki veriler silindi mi?</li>
<li>Aksesuar stoğu adet bazında listelendi mi?</li>
<li>Teslim günü ve yükleme koşulları netleşti mi?</li>
</ul>

<h2>Devir mi, kapanış mı?</h2>
<p>Mağazanın tamamen kapanması ile başka bir işletmeye devredilmesi, stok satışını farklı şekillerde etkiler. Devir durumunda yeni işletme raftaki ürünlerin bir kısmını devralmak isteyebilir; bu durumda devredilecek ve satılacak ürünlerin ayrımını erkenden yapmak gerekir. Tamamen kapanışta ise ürünler, ekipmanlar ve hatta mağaza mobilyaları dahil her şeyin boşaltılması gündeme gelir. Elektronik ürünler ve elektronik ekipmanlar toplu alıcıya, mobilya ve dekorasyon ise farklı alıcılara gidebilir. Bu ayrımı listede net göstermek, teslim gününde karışıklığı önler.</p>

<h2>Aksesuar ve küçük ürünleri küçümsemeyin</h2>
<p>Elektronik mağazalarında aksesuar stoğu, adet olarak çoğu zaman ana ürünlerden fazladır. Kılıflar, ekran koruyucular, kablolar, şarj adaptörleri, kulaklıklar ve hafıza kartları kapanış sırasında genellikle kolilere gelişigüzel doldurulur. Bunları ürün türüne göre ayırmak ve yaklaşık adet vermek, aksesuar stoğunun da doğru değerlendirilmesini sağlar. Uyumlu olduğu telefon modeline göre ayrılmış kılıflar, karışık bir koliye göre çok daha kolay değerlendirilir.</p>

<h2>Personel ve iş bölümü</h2>
<p>Kapanış haftası mağaza personeli için yoğun geçer: müşteri iadeleri, son satışlar, kasa işlemleri ve sayım aynı anda yürür. Sayımı ve ürün ayrımını bir ya da iki kişiye özel görev olarak vermek, diğer işlerin aksamasını önler. Teslim günü için de ürünleri kolileyecek, sayımda alıcıyla birlikte duracak ve teslim belgesini imzalayacak kişiyi önceden belirlemek süreci hızlandırır.</p>
<p>Zincir mağazalarda veya birden fazla şubesi olan işletmelerde, kapanan şubenin stoğunu önce diğer şubelere aktarmak bir seçenek olabilir. Ancak aktarım maliyeti ve diğer şubelerdeki talep düşünüldüğünde, özellikle teşhir, iade ve yavaş dönen ürünlerde doğrudan toplu satış daha pratik olabilir.</p>

<h2>Teklifi karşılaştırırken</h2>
<p>Birden fazla teklif alıyorsanız, tekliflerin aynı listeyi kapsadığından emin olun. Bir teklif yalnızca sıfır ürünleri, diğeri tüm stoğu kapsıyorsa rakamlar karşılaştırılabilir değildir. Teslim alma şekli, ödeme zamanı ve teşhir ile arızalı ürünlerin nasıl değerlendirildiği de karşılaştırmanın parçası olmalıdır. En çok satılan ürünlere iyi teklif verip geri kalanını almayan bir alıcı, kapanış takviminde elinizde değerlendirilemeyen ürün bırakabilir.</p>

<h2>Neden tek alıcıyla toptan satış?</h2>
<p>Stoğu parça parça satmak, en çok talep gören ürünlerin hızla gitmesine ve geri kalanının elde kalmasına yol açar. Kapanış takviminde bu durum, son günlerde değerlendirilemeyen ürünler anlamına gelir. Sıfırdan arızalıya kadar tüm stoğu tek seferde değerlendiren bir toplu alıcıyla çalışmak; tek görüşme, tek teslim ve tek fatura ile süreci tamamlamanızı sağlar.</p>
<p>Kapanış ve devir süreçlerinde nasıl çalıştığımızı <a href="/tasfiye-elektronik-alimi">tasfiye ve mağaza kapanış ürünleri alımı</a> sayfamızda bulabilirsiniz. Teşhir ve iade ürünleriniz ağırlıktaysa <a href="/teshir-iade-elektronik-alimi">teşhir, iade ve kapalı kutu ürün alımı</a>, depoda bekleyen sıfır ürünleriniz varsa <a href="/stok-fazlasi-elektronik-alimi">stok fazlası elektronik alımı</a> sayfaları da yol gösterici olacaktır.</p>

<h2>Sonuç</h2>
<p>Mağaza stoğunu toplu satmak, düzenli bir sayım ve doğru gruplama ile kapanış takvimine sığan, öngörülebilir bir sürece dönüşür. Ürünlerinizin durumu ne olursa olsun, listeyle verilen bir toptan teklif dükkânı zamanında boşaltmanıza yardımcı olur. Stok listeniz hazırsa bizi arayın ya da <a href="/teklif-al">teklif formunu</a> doldurun; mağaza stoğunuz için toplu fiyat teklifimizi iletelim.</p>
HTML,
        'faqs' => [
            ['question' => 'Mağaza stoğunun tamamı tek seferde satılabilir mi?', 'answer' => 'Evet. Sıfır, teşhir, iade, arızalı ürünler ve mağaza ekipmanları tek liste üzerinden toplu olarak değerlendirilebilir. Ürünleri durumuna göre gruplamak teklif sürecini hızlandırır.'],
            ['question' => 'Teşhir ürünleri sıfır ürünlerle aynı değerlendirilir mi?', 'answer' => 'Hayır, teşhir ve açık kutu ürünler ayrı değerlendirilir. Bu yüzden listede sıfır ve teşhir ürünleri ayrı satırlarda belirtmeniz isabetli teklif için önemlidir.'],
            ['question' => 'Aksesuar stokları da alınır mı?', 'answer' => 'Kılıf, kablo, şarj aleti gibi aksesuarlar da toplu olarak değerlendirilebilir. Bunları ürün türü ve yaklaşık adetle listelemeniz yeterlidir.'],
            ['question' => 'Kapanış tarihim yakınsa süreç nasıl ilerler?', 'answer' => 'Takviminizi en baştan belirtmeniz önemlidir. Liste net olduğunda teklif ve teslim planı kapanış tarihinize göre ayarlanabilir.'],
        ],
    ],

    [
        'slug' => 'elektronik-depo-bosaltma-rehberi',
        'title' => 'Elektronik Depo Boşaltma Rehberi: Stoğu Toplu Satmanın Yolu',
        'category' => 'Stok ve Depo Yönetimi',
        'primary_keyword' => 'depo ürünlerini toplu satmak istiyorum',
        'meta_title' => 'Elektronik Depo Boşaltma Rehberi | Toplu Satış',
        'meta_description' => 'Depo ürünlerini toplu satmak istiyorum diyorsanız: stok sayımı, palet ve lot hazırlığı, teklif, yükleme ve teslim planı için pratik depo boşaltma rehberi.',
        'excerpt' => 'Depoda bekleyen elektronik stoğu, iadeleri ve karışık paletleri tek seferde satarak alan açmak isteyenler için sayımdan yüklemeye kadar adım adım rehber.',
        'body_html' => <<<'HTML'
<p><strong>Kısaca:</strong> Depo ürünlerini toplu satmak için önce sayım yapıp stoğu model, adet ve durum bilgisiyle listeleyin; ardından ürünleri palet veya lot halinde gruplayın. Bu listeyle alınan toplu teklif sonrasında yükleme günü planlanır ve depo tek seferde boşaltılır.</p>

<h2>Depo neden boşaltılır?</h2>
<p>Elektronik ürünler depoda beklerken değer kaybeder; yeni modeller çıktıkça eski stoğun talebi azalır. Bunun yanında depo kirası, sigorta ve personel giderleri devam eder. İthalatçılar, distribütörler, e-ticaret satıcıları ve bayiler bu nedenle belirli aralıklarla depo boşaltmaya karar verir. Depo taşıma, sözleşme bitişi, ürün gamı değişikliği veya iş modelinin değişmesi de depo boşaltmanın sık görülen sebepleridir.</p>
<p>Depodaki stok genellikle karışıktır: sıfır ürünler, müşteri iadeleri, hasarlı ambalajlı ürünler, arızalılar ve aksesuarlar bir arada bulunur. Bu karışık yapıyı tek tek satmaya çalışmak aylar sürebilir. Tüm stoğu tek seferde toptan satmak ise alanı hızla açar ve stoğu nakde çevirir.</p>

<h2>Depo boşaltmaya hazırlık kontrol listesi</h2>
<ul>
<li>Depo yazılımındaki kayıtlar ile fiziksel stok karşılaştırıldı mı?</li>
<li>Ürünler model, varyant ve adet bazında listelendi mi?</li>
<li>Sıfır, iade, hasarlı ambalaj ve arızalı ürünler ayrıldı mı?</li>
<li>Paletlerin içeriği etiketlendi mi?</li>
<li>Yükleme alanı, forklift ve çalışma saatleri netleşti mi?</li>
<li>Fatura ve sevk belgeleri için gerekli bilgiler hazır mı?</li>
</ul>

<h2>Adım adım elektronik depo boşaltma</h2>
<h3>1. Sayım ve stok listesi</h3>
<p>Depo boşaltmanın ilk adımı doğru bir stok listesidir. Kayıtlardaki rakamlar ile raflardaki gerçek durum zamanla ayrışabileceği için fiziksel sayım önemlidir. Listede her satır için şu bilgiler bulunmalıdır:</p>
<ol>
<li>Ürün türü ve marka-model</li>
<li>Varyant: renk, kapasite, bölge versiyonu</li>
<li>Adet</li>
<li>Durum: sıfır, iade, hasarlı ambalaj, arızalı</li>
<li>Paket bilgisi: koli içi adet, palet numarası</li>
</ol>

<h3>2. Lot ve palet hazırlığı</h3>
<p>Benzer ürünleri bir araya getirerek lotlar oluşturmak, teklif ve teslim sürecini hızlandırır. Örneğin aynı model telefonlar bir lotta, karışık aksesuarlar başka bir lotta, arızalı ürünler ayrı bir lotta toplanabilir. Her paletin üzerine içeriğini gösteren bir etiket yapıştırmak, teslimde sayımı kolaylaştırır. Karışık paletlerde içeriğin yaklaşık dökümünü vermek bile değerlendirmeyi hızlandırır.</p>

<h3>3. Fotoğraf ve örnek kontrol</h3>
<p>Listeye ek olarak her lottan birkaç fotoğraf göndermek alıcının durumu anlamasını kolaylaştırır. İade ve hasarlı ambalajlı ürünlerde ambalaj ve ürünün kendisinin fotoğrafı ayrı ayrı faydalıdır. Büyük stoklarda alıcı depoda yerinde kontrol yapmak isteyebilir.</p>

<h3>4. Toplu teklif</h3>
<p>Liste ve fotoğraflar üzerinden toplu fiyat teklifi alınır. Teklif tüm stok için tek bir rakam olabileceği gibi lot bazında da verilebilir. Lot bazlı teklif, hangi ürün grubunun nasıl değerlendirildiğini görmenizi sağlar.</p>

<h3>5. Yükleme ve teslim planı</h3>
<p>Anlaşma sonrasında yükleme günü planlanır. Bu aşamada şunlar netleştirilmelidir:</p>
<ul>
<li>Deponun adresi, yükleme rampası ve araç giriş koşulları</li>
<li>Forklift ve transpalet ihtiyacı</li>
<li>Depo çalışma saatleri ve yükleme için ayrılacak personel</li>
<li>Stok büyükse yüklemenin birkaç güne bölünüp bölünmeyeceği</li>
</ul>

<h3>6. Belgeleme</h3>
<p>Teslimde paletler ve lotlar listeyle karşılaştırılır, satış fatura ile belgelenir. Stoğun kayıtlardan düşülmesi ve muhasebe işlemleri için mali müşavirinizle görüşmeniz önerilir.</p>

<h2>İade ve hasarlı ürünler ne olacak?</h2>
<p>Depo boşaltmada en çok soru işareti yaratan kalemler iade ve hasarlı ürünlerdir. Bu ürünlerin bir kısmı tamamen sağlam, yalnızca ambalajı açılmış veya zarar görmüştür; bir kısmı ise gerçekten arızalıdır. Bunları ayrı gruplarda listelemek ve durumlarını kısaca not etmek, hepsinin doğru değerlendirilmesini sağlar. Arızalı ürünler de genellikle parça değeri üzerinden alınır; ayırıp atmak yerine listeye eklemek daha doğrudur.</p>

<h2>Satıcı türüne göre depo boşaltma</h2>
<h3>İthalatçılar ve distribütörler</h3>
<p>İthalatçı ve distribütör depolarında stok genellikle aynı modelden yüksek adetlerde ve orijinal kolilerinde bulunur. Bu durum sayımı kolaylaştırır; koli içi adet ve koli sayısı vermek çoğu zaman yeterlidir. Ürünlerin bölge versiyonu, garanti durumu ve ithalat belgeleri gibi bilgiler teklif açısından önemli olabileceğinden listeye not düşmek faydalıdır. Model değişikliği nedeniyle eski seriyi eritmek isteyen distribütörler için toplu satış, yeni seriye depo alanı açmanın da yoludur.</p>
<h3>E-ticaret satıcıları</h3>
<p>E-ticaret depolarında iade oranı yüksek olduğundan stok daha karışıktır. Aynı modelden sıfır, açık kutu, kozmetik kusurlu ve arızalı ürünler bir arada bulunabilir. İade ürünleri müşteri dönüş sebebine göre (kutusu açılmış, kullanılmış, arızalı) ayırmak, değerlendirmeyi doğrudan etkiler. Pazaryeri kampanyalarından arta kalan ürünler de bu gruba girer.</p>
<h3>Bayiler ve perakendeciler</h3>
<p>Bayi depolarında genellikle farklı markalardan küçük adetli ürünler ve teşhirden dönen cihazlar bulunur. Bu tür stoklarda ürün çeşitliliği fazla olduğu için listeyi ürün türüne göre gruplamak, alıcının değerlendirmesini hızlandırır.</p>

<h2>Örnek bir stok listesi nasıl görünür?</h2>
<p>Listeyi hazırlarken karmaşık bir yazılıma gerek yoktur; basit bir tablo yeterlidir. Önemli olan her satırın tek bir ürün grubunu net tanımlamasıdır. Örneğin bir satır "akıllı telefon – marka/model – kapasite – renk – adet – sıfır, orijinal kolide" şeklinde olabilir. Başka bir satır "karışık şarj kablosu ve adaptör – yaklaşık adet – kolide, ambalajsız" olarak yazılabilir. Arızalı ürünler için kısa bir arıza notu ("ekran kırık", "açılmıyor") eklemek yeterlidir.</p>
<p>Palet bazında çalışıyorsanız tabloya bir "palet no" sütunu ekleyin ve aynı numarayı paletin üzerindeki etikete yazın. Teslim günü sayım, tablo ile etiketlerin karşılaştırılmasıyla dakikalar içinde tamamlanır. Liste hazır olmadan teklif istemek yerine birkaç saat ayırıp bu tabloyu çıkarmak, sürecin geri kalanını belirgin şekilde hızlandırır.</p>

<h2>Zamanlama neden önemli?</h2>
<p>Elektronik üründe zaman, değer üzerinde doğrudan etkilidir. Üreticilerin yeni model duyuruları, bir önceki modelin talebini kısa sürede değiştirebilir. Bu nedenle depo boşaltma kararını ertelemek, bekleyen stoğun değerini azaltabilir. Satışa karar verildiğinde sayımı ve listeyi hızla hazırlamak, teklifin güncel piyasa koşullarına göre verilmesini sağlar.</p>
<p>Bunun yanında depo sözleşmesinin bitiş tarihi, yeni sezon ürünlerinin geliş zamanı ve personel planlaması da zamanlamayı belirler. Yükleme gününü bu tarihlere göre geriye doğru planlamak, son günlerde acele edilmesini önler.</p>
<h3>Kısmi boşaltma mı, tamamı mı?</h3>
<p>Her depo boşaltma stoğun tamamını kapsamak zorunda değildir. Bazı işletmeler yalnızca yavaş dönen ürünleri, iadeleri veya belirli bir ürün grubunu satarak alan açmayı tercih eder. Kısmi boşaltmada satılacak ürünleri depoda fiziksel olarak ayırmak ve ayrı bir alana toplamak, yükleme günü yanlış ürünün araca konmasını önler. Tamamen boşaltmada ise raf sistemleri, depo ekipmanları ve ofis elektroniği gibi kalemlerin de değerlendirilip değerlendirilmeyeceğine karar vermek gerekir. Depo ofisindeki bilgisayarlar, el terminalleri ve etiket yazıcıları da elektronik ekipman olarak toplu satış listesine eklenebilir; bu cihazlarda kayıtlı verilerin satıştan önce silinmesini unutmayın. Hangi kalemlerin satışa dahil olduğunu teklif aşamasında netleştirmek, yükleme günü yanlış anlaşılmaları önler.</p>

<h2>Sık yapılan hatalar</h2>
<ul>
<li><strong>Kayıtlara güvenip sayım yapmamak:</strong> Teslimde liste ile gerçek stok arasında fark çıkar.</li>
<li><strong>Karışık paletleri tanımsız bırakmak:</strong> İçeriği belli olmayan palet temkinli değerlendirilir.</li>
<li><strong>En iyi ürünleri ayırıp kalanını satmaya çalışmak:</strong> Geriye kalan stok için alıcı bulmak zorlaşır.</li>
<li><strong>Yükleme koşullarını konuşmamak:</strong> Teslim günü araç, forklift veya personel eksikliği süreci uzatır.</li>
</ul>

<h2>Kime satmalı?</h2>
<p>Depo boşaltmada doğru alıcı; karışık stoğu tek seferde değerlendirebilen, büyük hacimli yüklemeyi organize edebilen ve faturalı çalışan bir toplu alıcıdır. Depo boşaltma sürecinde nasıl çalıştığımızı <a href="/elektronik-depo-bosaltma">elektronik depo boşaltma</a> sayfamızda bulabilirsiniz. Depodaki ürünleriniz ağırlıklı olarak sıfır stok fazlasıysa <a href="/stok-fazlasi-elektronik-alimi">stok fazlası elektronik alımı</a>, iade ve teşhir ürünleri ağırlıktaysa <a href="/teshir-iade-elektronik-alimi">teşhir, iade ve kapalı kutu ürün alımı</a> sayfaları da yol gösterici olacaktır.</p>

<h2>Sonuç</h2>
<p>Depo ürünlerini toplu satmak, sayım, lotlama ve yükleme planı ile düzenli bir sürece dönüştüğünde depoyu hızla boşaltmanın en pratik yoludur. Toptan satış, tek tek alıcı aramanın getirdiği zaman kaybını ortadan kaldırır. Stok listeniz hazırsa bizi arayın ya da <a href="/teklif-al">teklif formunu</a> doldurun; deponuzdaki ürünler için toplu fiyat teklifimizi iletelim.</p>
HTML,
        'faqs' => [
            ['question' => 'Depodaki karışık stok tek seferde satılabilir mi?', 'answer' => 'Evet. Sıfır ürünler, iadeler, hasarlı ambalajlı ve arızalı ürünler tek liste üzerinden toplu olarak değerlendirilebilir. Ürünleri lotlara ayırıp paletleri etiketlemek süreci hızlandırır.'],
            ['question' => 'Teklif için stok listesi şart mı?', 'answer' => 'İsabetli ve hızlı teklif için liste çok önemlidir. Liste yoksa birkaç fotoğraf ve yaklaşık döküm ile başlanabilir, ancak kesin teklif genellikle sayım sonrasında verilir.'],
            ['question' => 'Büyük stoklar için yerinde kontrol yapılır mı?', 'answer' => 'Büyük ve karışık stoklarda depoda yerinde kontrol planlanabilir. Bu, teklifin daha doğru verilmesini ve teslimde fark çıkmamasını sağlar.'],
            ['question' => 'Yükleme nasıl organize edilir?', 'answer' => 'Anlaşma sonrasında deponun adresi, yükleme koşulları ve çalışma saatlerine göre yükleme günü planlanır. Stok büyükse yükleme birkaç güne bölünebilir.'],
        ],
    ],
];

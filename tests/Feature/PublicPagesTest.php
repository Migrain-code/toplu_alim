<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Province;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public static function publicUrls(): array
    {
        return collect([
            '/', '/hizmetler',
            // ürün kategorileri
            '/toplu-telefon-alimi', '/toplu-bilgisayar-alimi', '/toplu-tablet-alimi', '/toplu-robot-supurge-alimi',
            '/toplu-elektronik-cihaz-alimi', '/kurumsal-elektronik-alimi',
            // ürün durumları
            '/stok-fazlasi-elektronik-alimi', '/elektronik-depo-bosaltma', '/tasfiye-elektronik-alimi',
            '/arizali-hurda-elektronik-alimi', '/teshir-iade-elektronik-alimi', '/ikinci-el-elektronik-toplu-alimi',
            // bölgeler: il, ilçe, il × kategori (metinli ve metinsiz il)
            '/bolgeler', '/istanbul', '/istanbul/kadikoy', '/istanbul/toplu-telefon-alimi',
            '/mardin', '/mardin/midyat', '/mardin/toplu-tablet-alimi', '/sanliurfa', '/igdir',
            // il × durum hizmeti (müşteri listesi: "{il} stok fazlası elektronik alanlar" …)
            '/istanbul/stok-fazlasi-elektronik-alimi', '/adana/arizali-hurda-elektronik-alimi', '/ankara/elektronik-depo-bosaltma',
            '/markalar', '/markalar/apple', '/blog', '/hakkimizda', '/sss', '/iletisim', '/teklif-al',
            '/teklif-al/tesekkurler', '/kvkk-aydinlatma-metni', '/sitemap.xml',
        ])->mapWithKeys(fn ($url) => [$url => [$url]])->all();
    }

    #[DataProvider('publicUrls')]
    public function test_public_page_loads(string $url): void
    {
        $this->get($url)->assertOk();
    }

    public function test_all_81_provinces_and_973_districts_are_seeded(): void
    {
        $this->assertSame(81, Province::count());
        $this->assertSame(973, District::count());
    }

    /**
     * 973 ilçenin hepsini render etmek paketi dakikalarca uzatıyor ve belleği
     * dolduruyordu; her ilin ilk ilçesi (81 sayfa) şablonun her ilde çalıştığını kanıtlar.
     */
    public function test_a_district_page_loads_in_every_province(): void
    {
        foreach (Province::with('districts')->get() as $province) {
            $district = $province->districts->first();

            $this->get('/'.$province->slug.'/'.$district->slug)
                ->assertOk()
                ->assertSee($district->name.' Toplu Elektronik Alımı');
        }
    }

    public function test_unknown_urls_return_404(): void
    {
        $this->get('/olmayan-sayfa')->assertNotFound();
        $this->get('/istanbul/olmayan-ilce')->assertNotFound();
        $this->get('/olmayan-il/kadikoy')->assertNotFound();
        // Kapalı hizmetin il sayfası da kapanır.
        Service::where('slug', 'elektronik-depo-bosaltma')->update(['is_active' => false]);
        $this->get('/istanbul/elektronik-depo-bosaltma')->assertNotFound();
    }

    public function test_home_has_primary_conversion_points_and_seo_tags(): void
    {
        $response = $this->get('/')->assertOk();

        $response->assertSee('Hemen Ara');
        // Hero: numara butonun içinde görünür + hızlı ürün seçimi
        $response->assertSee('class="hero-call"', false);
        $response->assertSee('Ne satmak istiyorsunuz?');
        $response->assertSee('https://wa.me/', false);
        $response->assertSee('href="tel:+', false);
        $response->assertSee('<link rel="canonical" href="'.url('/').'">', false);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('"@type":"LocalBusiness"', false);
        // Müşterinin istediği dört ana banner
        $response->assertSee('Toplu Telefon Alımı');
        $response->assertSee('Toplu Robot Süpürge Alımı');
        $response->assertSee('Toplu Bilgisayar ve Laptop Alımı');
        $response->assertSee('Toplu Elektronik Cihaz Alımı');
    }

    public function test_phone_and_whatsapp_come_from_settings_not_code(): void
    {
        Setting::set('phone', '+90 532 111 22 33');
        Setting::set('whatsapp', '905321112233');
        Setting::flush();

        $this->get('/')
            ->assertSee('tel:+905321112233', false)
            ->assertSee('https://wa.me/905321112233', false)
            ->assertSee('+90 532 111 22 33');
    }

    public function test_district_page_has_local_content_and_prefilled_whatsapp_message(): void
    {
        $district = District::whereHas('province', fn ($q) => $q->where('slug', 'istanbul'))->where('slug', 'kadikoy')->firstOrFail();

        $response = $this->get('/istanbul/kadikoy')->assertOk();

        $response->assertSee($district->neighborhoods[0]);
        $response->assertSee('Kadıköy için sık sorulan sorular');
        $response->assertSee(rawurlencode('Kadıköy, İstanbul'), false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('FAQPage', false);
    }

    public function test_service_page_has_required_sections(): void
    {
        $this->get('/toplu-telefon-alimi')
            ->assertOk()
            ->assertSee('Neleri alıyoruz?')
            ->assertSee('Kimlerden alıyoruz?')
            ->assertSee('Nasıl çalışıyoruz?')
            ->assertSee('Sık sorulan sorular')
            ->assertSee('Sık aldığımız markalar')
            ->assertSee(url('/istanbul/toplu-telefon-alimi'), false)
            ->assertSee('"@type":"Service"', false);
    }

    public function test_province_service_page_combines_city_and_category(): void
    {
        $this->get('/istanbul/toplu-telefon-alimi')
            ->assertOk()
            ->assertSee('<h1>İstanbul Toplu Telefon Alımı</h1>', false)
            ->assertSee('<link rel="canonical" href="'.url('/istanbul/toplu-telefon-alimi').'">', false)
            ->assertSee(url('/istanbul/kadikoy'), false)
            ->assertSee('"areaServed":"İstanbul"', false);

        $this->get('/istanbul/stok-fazlasi-elektronik-alimi')
            ->assertOk()
            ->assertSee('<h1>İstanbul Stok Fazlası Elektronik Alımı</h1>', false)
            ->assertSee('İstanbul için sık sorulan sorular');
    }

    /**
     * Kopya içerik önlemi: il × hizmet sayfası hizmetin uzun metnini tekrarlamaz, iki ilin
     * aynı hizmet sayfası il adı ve ilçeleriyle birbirinden ayrışır.
     */
    public function test_province_service_pages_do_not_copy_the_service_body(): void
    {
        $service = Service::where('slug', 'toplu-telefon-alimi')->firstOrFail();
        $paragraphs = preg_match_all('/<p[^>]*>(.*?)<\/p>/su', $service->description, $m) ? $m[1] : [];
        $lastParagraph = trim(strip_tags(end($paragraphs)));

        $istanbul = $this->get('/istanbul/toplu-telefon-alimi')->getContent();
        $ankara = $this->get('/ankara/toplu-telefon-alimi')->getContent();

        $this->assertStringNotContainsString(e($lastParagraph), $istanbul, 'hizmet metninin tamamı il sayfasına kopyalanmamalı');
        $this->assertStringContainsString('Kadıköy', $istanbul);
        $this->assertStringContainsString('Çankaya', $ankara);
        $this->assertStringNotContainsString('Kadıköy', $ankara);
    }

    /**
     * İnce içerik koruması: kendine özgü metni olmayan bölge sayfası noindex döner ve
     * site haritasına girmez; metin yazıldığı anda ikisi birden açılır.
     */
    public function test_regions_without_unique_content_are_noindex_and_left_out_of_sitemap(): void
    {
        $this->get('/istanbul')->assertSee('content="index, follow"', false);
        $this->get('/istanbul/toplu-telefon-alimi')->assertSee('content="index, follow"', false);
        $this->get('/mardin')->assertSee('content="noindex, follow"', false);
        $this->get('/mardin/toplu-tablet-alimi')->assertSee('content="noindex, follow"', false);
        $this->get('/istanbul/kadikoy')->assertSee('content="noindex, follow"', false);

        $this->get('/sitemap.xml')
            ->assertSee(url('/istanbul').'<', false)
            ->assertSee(url('/istanbul/toplu-telefon-alimi'), false)
            ->assertDontSee(url('/mardin').'<', false)
            ->assertDontSee(url('/istanbul/kadikoy').'<', false);

        District::where('slug', 'kadikoy')->update(['content' => '<p>'.str_repeat('Kadıköy özgün metin kelime ', 40).'</p>']);
        cache()->forget('sitemap.xml');

        $this->get('/istanbul/kadikoy')->assertSee('content="index, follow"', false);
        $this->get('/sitemap.xml')->assertSee(url('/istanbul/kadikoy').'<', false);
    }

    public function test_inactive_service_is_hidden(): void
    {
        Service::where('slug', 'toplu-tablet-alimi')->update(['is_active' => false]);

        $this->get('/toplu-tablet-alimi')->assertNotFound();
        $this->get('/istanbul/toplu-tablet-alimi')->assertNotFound();
        $this->get('/hizmetler')->assertDontSee('Toplu Tablet Alımı');
    }

    public function test_testimonials_section_only_shows_real_reviews(): void
    {
        $this->get('/')->assertDontSee('Bizimle Çalışanlar Ne Diyor?');

        Testimonial::create(['name' => 'Ayşe K.', 'location' => 'Kadıköy', 'comment' => '40 telefonu tek seferde sattık.', 'rating' => 5]);

        $this->get('/')->assertSee('Bizimle Çalışanlar Ne Diyor?')->assertSee('40 telefonu tek seferde sattık.');
    }

    public function test_sitemap_lists_services_and_regions(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(url('/toplu-telefon-alimi'), false)
            ->assertSee(url('/arizali-hurda-elektronik-alimi'), false)
            ->assertSee(url('/ankara/toplu-bilgisayar-alimi'), false)
            ->assertSee(url('/markalar/apple'), false);
    }

    public function test_robots_txt_points_to_absolute_sitemap_and_hides_admin(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: '.url('/sitemap.xml'), false)
            ->assertSee('Disallow: /admin', false);
    }
}

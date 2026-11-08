<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\District;
use App\Models\Province;
use App\Models\Setting;
use App\Services\Ai\RegionContentEnricher;
use App\Services\InternalLink\RuleBuilder;
use App\Services\Seo\TargetSynchroniser;
use App\Support\Console\CommandCatalog;
use App\Support\SeoConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Canlıda görülen iki boşluk: iç link motoru illerin yalnız ilk 25'ini (plaka sırası)
 * tarıyordu; marka sayfaları AI zenginleştirmesinin hiç kapsamında değildi.
 */
class SeoCoverageTest extends TestCase
{
    use RefreshDatabase;

    private function longText(string $phrase): string
    {
        return '<p>'.str_repeat('Bu bölgeden stoklarınızı değerlendiriyoruz. ', 40).' Ayrıca '.$phrase.' talepleri de alıyoruz.</p>';
    }

    public function test_link_apply_reaches_provinces_beyond_the_first_twenty_five_but_not_districts(): void
    {
        SeoConfig::set('internal_links_enabled', true);
        SeoConfig::set('internal_links_auto_apply', true);
        Setting::flush();

        // İstanbul plaka/sıra olarak 34.; eski sınır (ilk 25) yüzünden hiç işlenmiyordu.
        Province::where('slug', 'istanbul')->update(['content' => $this->longText('toplu telefon alımı')]);
        Province::where('slug', 'zonguldak')->update(['content' => $this->longText('toplu telefon alımı')]);
        District::where('slug', 'kadikoy')->update(['content' => $this->longText('toplu telefon alımı')]);

        app(TargetSynchroniser::class)->sync();
        app(RuleBuilder::class)->build();

        $this->artisan('links:apply')->assertSuccessful();

        $this->assertStringContainsString('href="/toplu-telefon-alimi"', Province::where('slug', 'istanbul')->value('content'));
        $this->assertStringContainsString('href="/toplu-telefon-alimi"', Province::where('slug', 'zonguldak')->value('content'));
        // İlçeler bot SEO dışında: metni olsa da otomatik link yazılmaz.
        $this->assertStringNotContainsString('href=', District::where('slug', 'kadikoy')->value('content'));
    }

    /**
     * Doğal dilde yazılmış metinde kural ifadesi birebir geçmez; canlıda 125 içerikten 5'i
     * link alıyordu. Yedek paragraf her sayfaya gerçek iç link kazandırır, ikinci koşuda
     * tekrar eklenmez, marka hukuki notu en sonda kalır.
     */
    public function test_pages_without_matching_anchors_get_a_related_links_paragraph_once(): void
    {
        SeoConfig::set('internal_links_enabled', true);
        SeoConfig::set('internal_links_auto_apply', true);
        Setting::flush();

        Province::where('slug', 'ankara')->update(['content' => '<p>'.str_repeat('Kurumların yenilediği cihazları adetli olarak satın alıyoruz. ', 30).'</p>']);

        app(TargetSynchroniser::class)->sync();
        app(RuleBuilder::class)->build();

        $this->artisan('links:apply')->assertSuccessful();

        $ankara = Province::where('slug', 'ankara')->value('content');
        $this->assertStringContainsString('class="related-links"', $ankara);
        $this->assertMatchesRegularExpression('#href="/ankara/[a-z-]+"#', $ankara);
        $this->assertStringNotContainsString('href="/ankara"', $ankara, 'sayfa kendine link vermemeli');

        $apple = Brand::where('slug', 'apple')->value('content');
        $this->assertStringContainsString('href="/toplu-telefon-alimi"', $apple);
        $this->assertMatchesRegularExpression('#related-links.*<small>#s', $apple, 'hukuki not sonda kalmalı');

        // İl paragrafları birbirinin kopyası değil.
        $istanbul = Province::where('slug', 'istanbul')->value('content');
        preg_match('#<p class="related-links">.*?</p>#s', $istanbul, $a);
        preg_match('#<p class="related-links">.*?</p>#s', $ankara, $b);
        $this->assertNotSame(
            str_replace(['İstanbul', 'istanbul'], '', $a[0] ?? 'x'),
            str_replace(['Ankara', 'ankara'], '', $b[0] ?? 'y'),
        );

        // İkinci koşu hiçbir şey değiştirmez.
        $this->artisan('links:apply')->assertSuccessful();
        $this->assertSame($ankara, Province::where('slug', 'ankara')->value('content'));
        $this->assertSame(1, substr_count($ankara, 'related-links'));

        // Metinsiz il boş kalır (paragraf tek başına içerik sayılmaz).
        $this->assertSame('', (string) Province::where('slug', 'agri')->value('content'));
    }

    public function test_brand_pages_are_enriched_and_keep_the_trademark_notice(): void
    {
        config(['seo.ai.api_key' => 'test-key', 'seo.ai.base_url' => 'https://openrouter.ai/api/v1']);

        $body = '<p>'.str_repeat('Apple telefon ve tabletlerinizi toplu satarken model ve kapasite bilgisi önemlidir. ', 30).'</p>'
            .'<h2>Teklif için hangi bilgiler gerekir?</h2><p>Model, adet ve kilit durumu.</p>';

        Http::fake(['openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => json_encode([
                'body_html' => $body, 'meta_title' => 'Apple Toplu Alım | iPhone, iPad', 'meta_description' => str_repeat('Apple ürünlerinizi toplu satın. ', 5),
            ], JSON_UNESCAPED_UNICODE)]]],
        ])]);

        $brand = Brand::where('slug', 'apple')->firstOrFail();
        $this->assertStringContainsString('<small>', $brand->content, 'tohumda hukuki not olmalı');

        $result = app(RegionContentEnricher::class)->enrich($brand);

        $fresh = $brand->fresh();
        $this->assertGreaterThan($result['words_before'], $result['words_after']);
        $this->assertStringContainsString('Teklif için hangi bilgiler gerekir?', $fresh->content);
        $this->assertStringContainsString('sahiplerinin tescilli markalarıdır', $fresh->content, 'hukuki not korunmalı');

        Http::assertSent(fn ($request) => str_contains(json_decode((string) $request->body(), true)['messages'][1]['content'] ?? '', 'MARKA HAKKINDA İDDİA YASAK'));
    }

    public function test_enrich_command_accepts_brands(): void
    {
        config(['seo.ai.api_key' => null]);

        // AI yokken sessizce çıkar; ama brand tipi artık geçerli bir seçenek.
        $this->artisan('seo:enrich', ['--type' => ['brand'], '--limit' => 1])->assertSuccessful();
        $this->assertSame(['--type' => ['brand'], '--limit' => 10], CommandCatalog::all()['enrich-brands']['arguments']);
    }
}

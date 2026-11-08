<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\District;
use App\Models\InternalLinkRule;
use App\Models\Province;
use App\Models\Setting;
use App\Services\Ai\AiException;
use App\Services\Ai\ContentSanitizer;
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

    public function test_service_and_blog_pages_show_an_illustration_when_there_is_no_photo(): void
    {
        $this->seed();

        $this->get('/toplu-telefon-alimi')->assertOk()->assertSee('class="detail-art', false)->assertSee('device-art--phone', false);

        $blog = Blog::query()->published()->firstOrFail();
        $this->get($blog->path())->assertOk()->assertSee('class="detail-art', false);
    }

    /**
     * AI'ya istenen ifadeler iç link kurallarıyla BİREBİR aynı olmalı; yoksa metne giren
     * ifade linke dönüşmez ("toplu bilgisayar alımı" ≠ "toplu bilgisayar ve laptop alımı").
     */
    public function test_province_prompt_asks_for_phrases_that_match_link_rules_and_600_words(): void
    {
        config(['seo.ai.api_key' => 'test-key', 'seo.ai.base_url' => 'https://openrouter.ai/api/v1']);
        Http::fake(['openrouter.ai/*' => Http::response(['choices' => [['message' => ['content' => json_encode([
            'body_html' => '<p>'.str_repeat('Ankara stoklarını değerlendiriyoruz. ', 80).'</p>', 'meta_title' => 'Ankara Toplu Elektronik Alımı', 'meta_description' => str_repeat('Ankara toplu alım. ', 7),
        ], JSON_UNESCAPED_UNICODE)]]]])]);

        app(RegionContentEnricher::class)->enrich(Province::where('slug', 'ankara')->firstOrFail());

        app(TargetSynchroniser::class)->sync();
        app(RuleBuilder::class)->build();
        $anchors = InternalLinkRule::query()->active()->pluck('anchor_text')->all();

        Http::assertSent(function ($request) use ($anchors) {
            $prompt = json_decode((string) $request->body(), true)['messages'][1]['content'] ?? '';
            if (! str_contains($prompt, 'İSTENEN YAPI')) {
                return false; // ikinci (genişletme) çağrısı
            }
            $this->assertStringContainsString('EN AZ 600', $prompt);
            $this->assertStringContainsString('"Ankara toplu bilgisayar ve laptop alımı"', $prompt);
            $this->assertContains('Ankara toplu bilgisayar ve laptop alımı', $anchors);

            return true;
        });
    }

    /** AI 650-800 istense de ~300 kelime yazıyordu; kısa metin ikinci çağrıyla uzatılır. */
    public function test_short_ai_output_is_expanded_with_a_second_call(): void
    {
        config(['seo.ai.api_key' => 'test-key', 'seo.ai.base_url' => 'https://openrouter.ai/api/v1']);
        $reply = fn (int $n) => Http::response(['choices' => [['message' => ['content' => json_encode([
            'body_html' => '<h2>Teklif süreci</h2><p>'.str_repeat('Manisa stoklarını adetli olarak alıyoruz. ', $n).'</p>',
            'meta_title' => 'Manisa Toplu Elektronik Alımı', 'meta_description' => str_repeat('Manisa toplu alım. ', 7),
        ], JSON_UNESCAPED_UNICODE)]]]]);

        Http::fakeSequence('openrouter.ai/*')->pushResponse($reply(60))->pushResponse($reply(170));

        $result = app(RegionContentEnricher::class)->enrich(Province::where('slug', 'manisa')->firstOrFail());

        $this->assertGreaterThanOrEqual(600, $result['words_after']);
        Http::assertSentCount(2);
        // İlk metin korunur, yeni bölümler sona eklenir.
        $this->assertSame(2, substr_count((string) Province::where('slug', 'manisa')->value('content'), '<h2>Teklif süreci</h2>'));
    }

    public function test_a_longer_existing_text_is_never_replaced_by_a_shorter_one(): void
    {
        config(['seo.ai.api_key' => 'test-key', 'seo.ai.base_url' => 'https://openrouter.ai/api/v1']);
        $long = '<p>'.str_repeat('Ankara kurumlarından cihaz alıyoruz. ', 250).'</p>';
        Province::where('slug', 'ankara')->update(['content' => $long]);

        Http::fake(['openrouter.ai/*' => Http::response(['choices' => [['message' => ['content' => json_encode([
            'body_html' => '<p>'.str_repeat('Kısa metin kelime. ', 110).'</p>', 'meta_title' => 'Ankara', 'meta_description' => 'x',
        ], JSON_UNESCAPED_UNICODE)]]]])]);

        try {
            app(RegionContentEnricher::class)->enrich(Province::where('slug', 'ankara')->firstOrFail());
            $this->fail('kısa metin kaydedilmemeliydi');
        } catch (AiException $e) {
            $this->assertStringContainsString('kısa', $e->getMessage());
        }

        $this->assertSame($long, Province::where('slug', 'ankara')->value('content'));
    }

    /**
     * Skoru 90'ı geçtiği için zenginleştirmeye girmeyen ama 600 kelimenin altında kalan
     * sayfalar (canlıda 112): metin korunur, yeni bölümler eklenir, sondaki bloklar sonda kalır.
     */
    public function test_short_pages_are_lengthened_without_touching_existing_text(): void
    {
        config(['seo.ai.api_key' => 'test-key', 'seo.ai.base_url' => 'https://openrouter.ai/api/v1']);
        $original = '<p>'.str_repeat('Trabzon stoklarını adetli alıyoruz. ', 70).'</p><h2>Teklif süreci</h2><p>Arayın.</p>';
        Province::where('slug', 'trabzon')->update(['content' => $original."\n".'<p class="related-links">İlgili: <a href="/trabzon/toplu-telefon-alimi">x</a></p>']);

        app(TargetSynchroniser::class)->sync();
        $this->artisan('seo:score')->assertSuccessful();

        Http::fake(['openrouter.ai/*' => Http::response(['choices' => [['message' => ['content' => json_encode([
            'body_html' => '<h2>Satış öncesi hazırlık</h2><p>'.str_repeat('Cihazları sıfırlayıp listeleyin. ', 90).'</p>',
        ], JSON_UNESCAPED_UNICODE)]]]])]);

        $this->artisan('seo:enrich', ['--short' => true, '--type' => ['province'], '--limit' => 20])->assertSuccessful();

        $content = (string) Province::where('slug', 'trabzon')->value('content');
        $this->assertStringStartsWith($original, $content, 'mevcut metin aynen korunmalı');
        $this->assertStringContainsString('<h2>Satış öncesi hazırlık</h2>', $content);
        $this->assertMatchesRegularExpression('#Satış öncesi hazırlık.*related-links#s', $content, 'ilgili sayfalar sonda kalmalı');
        $this->assertGreaterThanOrEqual(600, app(ContentSanitizer::class)->wordCount($content));
        $this->assertArrayHasKey('enrich-short', CommandCatalog::all());
    }

    public function test_enrich_command_accepts_brands(): void
    {
        config(['seo.ai.api_key' => null]);

        // AI yokken sessizce çıkar; ama brand tipi artık geçerli bir seçenek.
        $this->artisan('seo:enrich', ['--type' => ['brand'], '--limit' => 1])->assertSuccessful();
        $this->assertSame(['--type' => ['brand'], '--limit' => 10], CommandCatalog::all()['enrich-brands']['arguments']);
    }
}

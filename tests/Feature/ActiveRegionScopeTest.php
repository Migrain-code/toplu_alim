<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\District;
use App\Models\InternalLinkRule;
use App\Models\Province;
use App\Models\SeoAnalysis;
use App\Models\SeoKeyword;
use App\Models\SeoTarget;
use App\Models\Setting;
use App\Services\Ai\AiException;
use App\Services\Ai\RegionContentEnricher;
use App\Services\InternalLink\RuleBuilder;
use App\Services\Seo\CommercialKeywordAssigner;
use App\Services\Seo\ContentRegistry;
use App\Services\Seo\KeywordPool;
use App\Services\Seo\LocationKeywordBuilder;
use App\Services\Seo\TargetSynchroniser;
use App\Support\SeoConfig;
use App\Support\TurkishText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Bot SEO kapsamı: YALNIZ aktif İLLER.
 *
 * İlçe sayfaları yayında (ili aktifse açılır) ama otomasyona girmez: skorlanmaz, AI
 * metni almaz, kelime/hedef üretilmez, otomatik iç link verilmez. Kapalı il için de
 * hiçbir şey üretilmez (adres 404 döner).
 */
class ActiveRegionScopeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /** Yalnız Tekirdağ + Çorlu açık; Kırklareli kapalı ama Vize açık (tutarsız durum). */
    private function narrowToOneDistrict(): void
    {
        Province::query()->update(['is_active' => false]);
        District::query()->update(['is_active' => false]);

        Province::where('slug', 'tekirdag')->update(['is_active' => true]);
        District::where('slug', 'corlu')->update(['is_active' => true]);

        // Tuzak: ilçe açık ama ili kapalı → sayfa 404 döner.
        District::where('slug', 'vize')->update(['is_active' => true]);
    }

    public function test_district_of_an_inactive_province_is_unreachable(): void
    {
        $this->narrowToOneDistrict();

        $this->get('/tekirdag/corlu')->assertOk();
        $this->get('/kirklareli/vize')->assertNotFound();
    }

    public function test_registry_holds_only_active_provinces_never_districts(): void
    {
        $this->narrowToOneDistrict();

        $reachable = app(ContentRegistry::class)->reachable();
        $urls = $reachable->pluck('url')->filter()->map(fn ($u) => parse_url($u, PHP_URL_PATH))->all();

        $this->assertContains('/tekirdag', $urls);
        $this->assertNotContains('/edirne', $urls, 'kapalı il işlenmemeli');
        // İlçeler bot SEO kapsamı dışında: açık ilçe de kayıtta yer almaz.
        $this->assertNotContains('/tekirdag/corlu', $urls);
        $this->assertNotContains('/kirklareli/vize', $urls);
        $this->assertSame(0, app(ContentRegistry::class)->all()->where('contentType', 'district')->count());
    }

    public function test_scoring_skips_closed_provinces_and_never_scores_districts(): void
    {
        $this->artisan('seo:score', ['--all' => true])->assertSuccessful();
        $before = SeoAnalysis::count();
        $this->assertSame(0, SeoAnalysis::where('content_type', 'district')->count(), 'ilçe skorlanmamalı');

        $this->narrowToOneDistrict();
        $this->artisan('seo:score')->assertSuccessful();

        $this->assertLessThan($before, SeoAnalysis::count());

        // Kapalı ilin eski skoru KALMAMALI: panelde hayalet satır olmasın.
        $edirne = Province::where('slug', 'edirne')->first();
        $this->assertDatabaseMissing('seo_analyses', ['content_type' => 'province', 'content_id' => $edirne->id]);

        $tekirdag = Province::where('slug', 'tekirdag')->first();
        $this->assertDatabaseHas('seo_analyses', ['content_type' => 'province', 'content_id' => $tekirdag->id]);
    }

    /** Bölgeye kendine özgü (≥150 kelime) metin verir: sayfa dizine açılır. */
    private function giveContent(string $slug, ?string $provinceSlug = null): void
    {
        $html = '<p>'.str_repeat('Bu bölgeden toplu telefon ve elektronik alımı yapıyoruz. ', 30).'</p>';

        if ($provinceSlug === null) {
            Province::where('slug', $slug)->update(['content' => $html]);

            return;
        }

        $province = Province::where('slug', $provinceSlug)->firstOrFail();
        District::where('province_id', $province->id)->where('slug', $slug)->update(['content' => $html]);
    }

    public function test_location_keywords_are_built_only_for_active_provinces(): void
    {
        $this->narrowToOneDistrict();

        $result = app(LocationKeywordBuilder::class)->build();

        $this->assertSame(['Tekirdağ (il)'], $result['regions'], 'ilçeler bölge listesine girmemeli');

        $keywords = SeoKeyword::where('note', 'like', 'Bölge otomasyonu%')->pluck('keyword');

        // Müşteri listesindeki il kalıpları (İl Bazlı Liste) + tamamlayıcılar: il başına 35.
        $this->assertCount(count(LocationKeywordBuilder::PATTERNS), $keywords);
        $this->assertTrue($keywords->contains('tekirdağ toplu elektronik alımı'));
        $this->assertTrue($keywords->contains('tekirdağ toptan elektronik alımı'));
        $this->assertTrue($keywords->contains('tekirdağ stok fazlası elektronik alanlar'));
        $this->assertFalse($keywords->contains('çorlu toplu elektronik alımı'), 'ilçe kelimesi üretilmemeli');
        $this->assertFalse($keywords->contains('edirne toplu elektronik alımı'), 'kapalı il için kelime üretilmemeli');
    }

    public function test_location_keywords_follow_the_client_province_list(): void
    {
        $this->narrowToOneDistrict();
        app(LocationKeywordBuilder::class)->build();

        $keywords = SeoKeyword::pluck('keyword')->map(fn ($k) => TurkishText::lower($k));

        foreach (['tekirdağ toplu telefon alanlar', 'tekirdağ toplu iphone alanlar', 'tekirdağ elektronik hurda alan firmalar',
            'tekirdağ teşhir elektronik ürün alanlar', 'tekirdağ depo tasfiyesi elektronik alımı', 'tekirdağ toplu dyson alanlar',
            'tekirdağ şirket bilgisayarlarını alan firmalar', 'tekirdağ toplu yazıcı alanlar'] as $expected) {
            $this->assertTrue($keywords->contains($expected), $expected.' üretilmeli');
        }

        // İlçe × ürün de, ilçe varyantı da üretilmez.
        $this->assertFalse($keywords->contains('çorlu toplu telefon alımı'));
        $this->assertFalse($keywords->contains('çorlu toplu tablet alımı'));
    }

    public function test_region_keywords_are_deactivated_when_the_province_closes(): void
    {
        app(TargetSynchroniser::class)->sync();
        app(LocationKeywordBuilder::class)->build();

        $this->assertTrue((bool) SeoKeyword::where('keyword', 'tekirdağ toplu elektronik alımı')->value('status'));

        Province::where('slug', 'tekirdag')->update(['is_active' => false]);
        app(TargetSynchroniser::class)->sync();
        app(LocationKeywordBuilder::class)->build();

        $keyword = SeoKeyword::where('keyword', 'tekirdağ toplu elektronik alımı')->first();

        $this->assertNotNull($keyword, 'kelime SİLİNMEMELİ, yalnız pasife alınmalı');
        $this->assertFalse((bool) $keyword->status);

        // İl tekrar açılırsa kelime geri gelir.
        Province::where('slug', 'tekirdag')->update(['is_active' => true]);
        app(TargetSynchroniser::class)->sync();
        app(LocationKeywordBuilder::class)->build();

        $this->assertTrue((bool) $keyword->refresh()->status);
    }

    public function test_region_keywords_are_owned_by_the_page_that_answers_them(): void
    {
        $this->narrowToOneDistrict();
        app(TargetSynchroniser::class)->sync();
        app(LocationKeywordBuilder::class)->build();

        $province = SeoKeyword::where('keyword', 'tekirdağ toplu elektronik alımı')->firstOrFail();
        $this->assertSame('/tekirdag', $province->target?->url);
        $this->assertSame(SeoKeyword::ASSIGN_ACTIVE, $province->assignment_status);

        // Yakın varyasyonlar aynı il × hizmet sayfasında toplanır.
        $owners = [
            'tekirdağ toplu telefon alanlar' => '/tekirdag/toplu-telefon-alimi',
            'tekirdağ toplu iphone alanlar' => '/tekirdag/toplu-telefon-alimi',
            'tekirdağ elektronik hurda alan firmalar' => '/tekirdag/arizali-hurda-elektronik-alimi',
            'tekirdağ stok fazlası elektronik alanlar' => '/tekirdag/stok-fazlasi-elektronik-alimi',
            'tekirdağ iade elektronik ürün alanlar' => '/tekirdag/teshir-iade-elektronik-alimi',
        ];

        foreach ($owners as $keyword => $url) {
            $row = SeoKeyword::where('keyword', $keyword)->firstOrFail();
            $this->assertSame($url, $row->target?->url, $keyword);
            $this->assertSame('province_service', $row->target?->target_type);
        }

        // İlçeler hedef üretmez.
        $this->assertSame(0, SeoTarget::where('target_type', 'district')->where('status', true)->count());
    }

    public function test_commercial_keywords_go_to_service_pages(): void
    {
        app(TargetSynchroniser::class)->sync();

        $result = app(CommercialKeywordAssigner::class)->assign();
        $this->assertGreaterThan(0, $result['assigned']);

        $keyword = SeoKeyword::where('keyword', 'toplu laptop alanlar')->firstOrFail();

        // Ticari kelimeyi blog değil, para kazandıran hizmet sayfası sahiplenmeli.
        $this->assertSame('/toplu-bilgisayar-alimi', $keyword->target?->url);
        $this->assertFalse(
            app(KeywordPool::class)->available()->contains('keyword', 'toplu laptop alanlar'),
            'sahiplenen ticari kelime blog havuzunda kalmamalı',
        );
    }

    public function test_link_rules_only_target_active_indexable_provinces(): void
    {
        $this->narrowToOneDistrict();
        $this->giveContent('tekirdag');
        $this->giveContent('corlu', 'tekirdag');
        $this->giveContent('vize', 'kirklareli');

        app(TargetSynchroniser::class)->sync();
        app(RuleBuilder::class)->build();

        $active = InternalLinkRule::where('is_active', true)->pluck('target_url')->unique();

        $this->assertTrue($active->contains('/tekirdag'));
        $this->assertTrue($active->contains('/tekirdag/toplu-telefon-alimi'), 'dizine açık ilin × hizmet sayfası link hedefi olmalı');
        $this->assertTrue($active->contains('/tekirdag/stok-fazlasi-elektronik-alimi'), 'durum hizmetleri de il altında link alır');
        // İlçe metinli olsa bile otomatik link almaz (bot SEO yalnız illerde).
        $this->assertFalse($active->contains('/tekirdag/corlu'));
        $this->assertFalse($active->contains('/kirklareli/vize'));
        $this->assertFalse($active->contains('/edirne'));

        $this->assertFalse(InternalLinkRule::where('anchor_text', 'Tekirdağ')->exists(), 'yalın il adı anchor olmamalı');
    }

    public function test_thin_region_pages_get_no_link_rules(): void
    {
        // Tohumda Tekirdağ ve ilçeleri metinsizdir: noindex sayfaya link basılmaz.
        app(TargetSynchroniser::class)->sync();
        app(RuleBuilder::class)->build();

        $active = InternalLinkRule::where('is_active', true)->pluck('target_url');

        $this->assertFalse($active->contains('/tekirdag/corlu'));
        $this->assertFalse($active->contains('/tekirdag'));
    }

    public function test_link_rules_are_deactivated_when_a_province_closes(): void
    {
        $this->giveContent('tekirdag');
        app(TargetSynchroniser::class)->sync();
        app(RuleBuilder::class)->build();

        $this->assertTrue(InternalLinkRule::where('target_url', '/tekirdag')->where('is_active', true)->exists());

        Province::where('slug', 'tekirdag')->update(['is_active' => false]);
        app(TargetSynchroniser::class)->sync();
        app(RuleBuilder::class)->build();

        $rule = InternalLinkRule::where('target_url', '/tekirdag')->first();
        $this->assertNotNull($rule, 'kural SİLİNMEMELİ');
        $this->assertFalse((bool) $rule->is_active);
    }

    public function test_published_blog_gets_internal_links_at_render_time(): void
    {
        app(TargetSynchroniser::class)->sync();
        app(RuleBuilder::class)->build();

        SeoConfig::set('internal_links_enabled', true);
        Setting::flush();

        $blog = Blog::create([
            'title' => 'Laptopları Toplu Satarken Dikkat Edilecekler',
            'slug' => 'laptoplari-toplu-satarken-dikkat',
            'body_html' => '<p>Toplu laptop alımı sırasında model listesini hazırlamak önemlidir.</p>',
            'status' => Blog::STATUS_PUBLISHED,
            'publish_at' => now()->subHour(),
        ]);

        $html = $this->get($blog->path())->assertOk()->getContent();

        // Link RENDER ANINDA basılır; links:apply cron'unu beklemez.
        $this->assertStringContainsString('class="internal-link"', $html);
        $this->assertStringContainsString('/toplu-bilgisayar-alimi', $html);

        // Veritabanındaki gövde değişmemiş olmalı (kalıcı yazma ayrı bir iştir).
        $this->assertStringNotContainsString('internal-link', $blog->refresh()->body_html);
    }

    public function test_render_time_links_are_off_when_the_engine_is_off(): void
    {
        app(TargetSynchroniser::class)->sync();
        app(RuleBuilder::class)->build();

        $blog = Blog::create([
            'title' => 'Motor Kapalıyken', 'slug' => 'motor-kapaliyken',
            'body_html' => '<p>Toplu laptop alımı ve toplu telefon alımı.</p>',
            'status' => Blog::STATUS_PUBLISHED, 'publish_at' => now()->subHour(),
        ]);

        $html = $this->get($blog->path())->assertOk()->getContent();

        $this->assertStringNotContainsString('class="internal-link"', $html);
    }

    public function test_districts_never_get_ai_content(): void
    {
        config(['seo.ai.api_key' => 'test-key']);
        Http::fake();

        $district = District::where('slug', 'corlu')->firstOrFail();

        try {
            app(RegionContentEnricher::class)->enrich($district);
            $this->fail('ilçe zenginleştirilmemeli');
        } catch (AiException $e) {
            $this->assertStringContainsString('İlçe', $e->getMessage());
        }

        // --type=district verilse de ilçe işlenmez; AI çağrısı yapılmaz.
        $this->artisan('seo:enrich', ['--type' => ['district'], '--thin' => true, '--limit' => 3]);
        Http::assertNotSent(fn ($r) => str_contains($r->body(), 'İLÇE:'));
        $this->assertNull($district->fresh()->content);
    }
}

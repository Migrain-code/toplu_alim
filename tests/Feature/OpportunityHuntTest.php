<?php

namespace Tests\Feature;

use App\Filament\Pages\OpportunityHunt;
use App\Models\InternalLinkRule;
use App\Models\Province;
use App\Models\SeoKeyword;
use App\Models\SeoKeywordAction;
use App\Models\SeoTarget;
use App\Models\User;
use App\Services\Ai\RegionContentEnricher;
use App\Services\Google\GoogleClient;
use App\Services\Seo\KeywordSections;
use App\Services\Seo\OpportunityHunter;
use App\Services\Seo\RankBooster;
use App\Services\Seo\TargetSynchroniser;
use App\Support\PathNormalizer;
use App\Support\SeoConfig;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class OpportunityHuntTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        // Ağa çıkılmaz: Google kimliği sahte, istekler Http::fake ile karşılanır.
        $this->app->instance(GoogleClient::class, new class extends GoogleClient
        {
            public function isConfigured(): bool
            {
                return true;
            }

            public function property(): ?string
            {
                return 'sc-domain:ornek.test';
            }

            public function token(string $scope = self::SCOPE_WEBMASTERS): string
            {
                return 'sahte-anahtar';
            }
        });

        config([
            'seo.ai.api_key' => 'test-key',
            'seo.ai.base_url' => 'https://openrouter.ai/api/v1',
            'seo.ai.model' => 'test/model',
            // Fırsat avının MANTIĞI sınanır; iş alanına özgü ayar burada sabitlenir ki
            // config/seo.php'deki canlı değerler değişince testler anlamını yitirmesin.
            // Bot SEO yalnız İLLERDE çalışır: odak bölgeler il; ilçe adı (Biga) takma adla
            // kendi ilinin sayfasına bağlanır. Muş, "muşamba" ön ek tuzağını sınar.
            'seo.opportunity.focus_regions' => ['Çanakkale', 'Kocaeli', 'Biga', 'Muş'],
            'seo.opportunity.region_aliases' => ['Biga' => '/canakkale'],
            'seo.opportunity.seed_patterns' => ['%s toplu elektronik alımı', '%s toplu elektronik alanlar'],
            'seo.opportunity.business_terms' => ['toplu', 'toptan', 'alım', 'alımı', 'alan', 'alanlar', 'satmak', 'satış'],
        ]);

        app(TargetSynchroniser::class)->sync();
    }

    /**
     * Search Console + AI sahte yanıtları.
     *
     * @param  array<int, array{0: string, 1: float, 2: int, 3?: string}>  $rows  [sorgu, sıra, gösterim, sayfa]
     */
    /** Search Console'un o anki yanıtı; turlar arasında değiştirilebilir. */
    private array $gscRows = [];

    private bool $faked = false;

    private function fake(array $rows, array $ai = []): void
    {
        // Http::fake ikinci kez çağrılınca ilk kayıt geçerli kalır; yalnız veri değişir.
        $this->gscRows = $rows;

        if ($this->faked) {
            return;
        }

        $this->faked = true;

        $ai += [
            'meta_title' => 'Çanakkale Toplu Elektronik Alımı | Hızlı Fiyat',
            'meta_description' => 'Çanakkale toplu elektronik alımı: telefon, tablet ve bilgisayar stoklarınıza hızlı toplu fiyat. Hemen arayın, ürünleri yerinden alalım.',
            'html' => '<h2>Çanakkale toplu elektronik alımı</h2><p>'.str_repeat('Kepez ve Biga çevresindeki bayi ve mağazalardan stok fazlası telefon ve laptop alımı yapıyoruz. ', 12).'</p>',
            'faqs' => [['question' => 'Çanakkale toplu elektronik alımı için nasıl teklif alırım?', 'answer' => 'Ürünlerin marka, model, adet ve durum bilgisini telefonla ya da WhatsApp üzerinden iletin; toplu fiyatı bu bilgilere göre bildiririz.']],
            'body_html' => '<p>'.str_repeat('Yeni metin kelimesi ', 220).'</p>',
        ];

        Http::fake([
            'searchconsole.googleapis.com/*' => function (Request $request) {
                $rows = $this->gscRows;
                $withPage = in_array('page', $request['dimensions'] ?? [], true);

                return Http::response(['rows' => array_map(fn ($r) => [
                    'keys' => $withPage ? [$r[0], 'https://ornek.test'.($r[3] ?? '/canakkale')] : [$r[0]],
                    'clicks' => 1, 'impressions' => $r[2], 'ctr' => 1 / max(1, $r[2]), 'position' => $r[1],
                ], $rows)]);
            },
            'indexing.googleapis.com/*' => Http::response(['urlNotificationMetadata' => []]),
            'openrouter.ai/*' => Http::response(['choices' => [['message' => ['content' => json_encode($ai, JSON_UNESCAPED_UNICODE)]]]]),
        ]);
    }

    private function keyword(string $text): ?SeoKeyword
    {
        return SeoKeyword::query()->get()->first(fn ($k) => $k->keyword === $text);
    }

    private function canakkaleTarget(): SeoTarget
    {
        return SeoTarget::query()->where('url_hash', PathNormalizer::hash('/canakkale'))->firstOrFail();
    }

    // ---------- eşleştirme ----------

    public function test_turkish_and_ascii_spellings_match_the_same_region(): void
    {
        $hunter = app(OpportunityHunter::class);

        $this->assertSame('Çanakkale', $hunter->matchRegion('canakkale toplu elektronik alimi')['name']);
        $this->assertSame('Çanakkale', $hunter->matchRegion('ÇANAKKALE TOPLU ELEKTRONİK ALIMI')['name']);
        // İlçeler bot SEO dışında: ilçe adı kendi bölgesi olarak eşleşmez…
        $this->assertNull($hunter->matchRegion('kepez toplu telefon alanlar'));
        // …ama takma adla ilin sayfasına bağlanabilir (Biga → /canakkale).
        $this->assertSame('/canakkale', $hunter->matchRegion('biga toplu elektronik alimi')['url']);
        $this->assertNull($hunter->matchRegion('toplu telefon alanlar'));

        // Bitişik hâl ekleri ("izmirde …" de İzmir'dir).
        $this->assertSame('Çanakkale', $hunter->matchRegion('çanakkalede toplu telefon alan')['name']);
        $this->assertSame('Kocaeli', $hunter->matchRegion("kocaeli'deki toplu alım")['name']);
        $this->assertSame('İzmir', $hunter->matchRegion('izmirde toplu laptop alan var mı')['name']);
        // Ek olmayan devam başka bir kelimedir: "muşamba" Muş değildir.
        $this->assertNull($hunter->matchRegion('muşamba toplu alım'));
    }

    public function test_region_queries_unrelated_to_the_business_are_not_opportunities(): void
    {
        $hunter = app(OpportunityHunter::class);

        $this->assertFalse($hunter->isBusiness('çanakkale nöbetçi eczane'));
        // Ürün SATIN ALMAK isteyen kişi bizim müşterimiz değil (biz alıcıyız).
        $this->assertFalse($hunter->isBusiness('çanakkale iphone fiyatları'));
        $this->assertTrue($hunter->isBusiness('çanakkale toplu telefon alanlar'));
        $this->assertTrue($hunter->isBusiness('kocaeli stok fazlası elektronik alımı'));
        $this->assertTrue($hunter->isBusiness('kocaeli laptop toptan satmak'));
    }

    // ---------- fırsat ekleme ve sıra ----------

    public function test_hunt_tracks_local_queries_and_binds_them_to_the_region_page(): void
    {
        $this->fake([
            ['çanakkale toplu laptop alanlar', 6.0, 60],
            ['canakkale toplu laptop alanlar', 8.0, 40],   // aynı arama, Türkçe karaktersiz
            ['kocaeli toplu elektronik alımı', 2.0, 30, '/kocaeli'],
            ['çanakkale nöbetçi eczane', 4.0, 500],
            ['toplu telefon alanlar', 9.0, 80, '/toplu-telefon-alimi'],
        ]);

        $result = app(OpportunityHunter::class)->run();

        $wardrobe = $this->keyword('çanakkale toplu laptop alanlar');
        $this->assertNotNull($wardrobe, 'İki yazım tek kelime olarak eklenmeli');
        $this->assertNull($this->keyword('canakkale toplu laptop alanlar'));
        $this->assertSame(SeoKeyword::CAMPAIGN_ACTIVE, $wardrobe->campaign_status);
        $this->assertSame(100, $wardrobe->gsc_impressions);
        // Sıra gösterime göre ağırlıklı: (6×60 + 8×40) / 100
        $this->assertEqualsWithDelta(6.8, $wardrobe->last_position, 0.01);
        $this->assertSame($this->canakkaleTarget()->id, $wardrobe->target_id);

        $this->assertSame(SeoKeyword::CAMPAIGN_WON, $this->keyword('kocaeli toplu elektronik alımı')->campaign_status);
        $this->assertNull($this->keyword('çanakkale nöbetçi eczane'));
        // Bölgesiz kelime kampanyaya girmez.
        $this->assertNull($this->keyword('toplu telefon alanlar')?->campaign_status);

        // Biga (takma ad) GSC verisi olmadan da takipte ve Çanakkale il sayfasına bağlı.
        $this->assertSame($this->canakkaleTarget()->id, $this->keyword('biga toplu elektronik alımı')->target_id);
        $this->assertGreaterThan(0, $result['seeded']);
    }

    public function test_keyword_that_drops_out_of_the_top_three_is_picked_up_again(): void
    {
        $this->fake([['kocaeli toplu elektronik alımı', 2.0, 30, '/kocaeli']]);
        app(OpportunityHunter::class)->run();
        $this->assertSame(SeoKeyword::CAMPAIGN_WON, $this->keyword('kocaeli toplu elektronik alımı')->campaign_status);

        $this->fake([['kocaeli toplu elektronik alımı', 5.4, 30, '/kocaeli']]);
        $result = app(OpportunityHunter::class)->run();

        $keyword = $this->keyword('kocaeli toplu elektronik alımı');
        $this->assertSame(SeoKeyword::CAMPAIGN_ACTIVE, $keyword->campaign_status);
        $this->assertSame(1, $result['lost']);
        $this->assertEqualsWithDelta(-3.4, $keyword->positionDelta(), 0.01);
    }

    public function test_missing_gsc_data_does_not_erase_the_last_known_position(): void
    {
        $this->fake([['muş toplu elektronik alımı', 7.0, 20, '/mus']]);
        app(OpportunityHunter::class)->run();

        $this->fake([]);
        app(OpportunityHunter::class)->run();

        $this->assertSame(7.0, $this->keyword('muş toplu elektronik alımı')->last_position);
    }

    public function test_new_keywords_per_run_are_capped(): void
    {
        SeoConfig::set('opportunity_max_new_per_run', 2);
        $this->fake([
            ['izmir toplu elektronik alımı', 9.0, 20, '/izmir'],
            ['izmir toplu laptop alanlar', 12.0, 15, '/izmir/toplu-bilgisayar-alimi'],
            ['ankara toplu telefon alanlar', 14.0, 10, '/ankara/toplu-telefon-alimi'],
        ]);

        $this->assertSame(2, app(OpportunityHunter::class)->run()['hunted']);
    }

    // ---------- iyileştirme ----------

    private function activeCanakkaleKeyword(float $position = 8.0, int $impressions = 50): SeoKeyword
    {
        return SeoKeyword::create([
            'keyword' => 'çanakkale toplu elektronik alımı', 'keyword_type' => 'COMMERCIAL_PRIMARY', 'status' => true,
            'campaign_status' => SeoKeyword::CAMPAIGN_ACTIVE, 'target_position' => 3, 'region' => 'Çanakkale',
            'last_position' => $position, 'gsc_impressions' => $impressions, 'target_id' => $this->canakkaleTarget()->id,
        ]);
    }

    public function test_keyword_in_the_top_three_is_never_touched(): void
    {
        $this->fake([]);
        $keyword = $this->activeCanakkaleKeyword(2.0);
        $keyword->update(['campaign_status' => SeoKeyword::CAMPAIGN_WON]);
        $before = Province::where('slug', 'canakkale')->first()->only(['meta_title', 'content', 'faqs']);

        $result = app(RankBooster::class)->boost($keyword);

        $this->assertNotNull($result['blocked']);
        $this->assertSame([], $result['ran']);
        $this->assertSame($before, Province::where('slug', 'canakkale')->first()->only(['meta_title', 'content', 'faqs']));
        Http::assertNotSent(fn (Request $r) => str_contains($r->url(), 'openrouter.ai'));
    }

    public function test_boost_strengthens_the_page_and_every_write_can_be_reverted(): void
    {
        $this->fake([]);
        $keyword = $this->activeCanakkaleKeyword();
        $province = Province::where('slug', 'canakkale')->first();
        // Seed'deki başlık kelimeyi zaten içeriyor; başlık adımının çalışması için kelimesiz başlık.
        $province->update(['meta_title' => 'Çanakkale Hizmet Bölgesi']);
        $original = $province->only(['meta_title', 'meta_description', 'content', 'faqs']);

        $result = app(RankBooster::class)->boost($keyword);

        $this->assertSame(['meta', 'content', 'faq', 'link_rule', 'index'], $result['ran']);

        $province->refresh();
        $this->assertSame('Çanakkale Toplu Elektronik Alımı | Hızlı Fiyat', $province->meta_title);
        $this->assertStringContainsString('data-kw="canakkale-toplu-elektronik-alimi"', $province->content);
        $this->assertCount(count($original['faqs']) + 1, $province->faqs);
        $this->assertTrue(InternalLinkRule::where('anchor_text', 'çanakkale toplu elektronik alımı')->where('target_url', '/canakkale')->exists());
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'indexing.googleapis.com'));

        // İkinci tur aynı şeyleri tekrar yazmaz.
        $this->assertSame([], app(RankBooster::class)->boost($keyword->fresh())['ran']);

        $booster = app(RankBooster::class);
        foreach (SeoKeywordAction::where('status', 'done')->get()->filter->isRevertible() as $action) {
            $booster->revert($action);
        }

        $province->refresh();
        $this->assertSame($original, $province->only(['meta_title', 'meta_description', 'content', 'faqs']));
        $this->assertFalse(InternalLinkRule::where('anchor_text', 'çanakkale toplu elektronik alımı')->first()->is_active);
    }

    /** Canlıda yaşandı: servis hesabı "Sahip" değil, Indexing API her denemede 403 döndü. */
    public function test_index_step_waits_after_a_permission_error(): void
    {
        $this->fake([]);
        $keyword = $this->activeCanakkaleKeyword();
        $booster = app(RankBooster::class);
        $booster->runStep($keyword, 'content');

        SeoKeywordAction::create([
            'keyword_id' => $keyword->id, 'action' => 'index', 'status' => SeoKeywordAction::FAILED,
            'message' => 'Google "bu siteye sahip değilsiniz" diyor (403).', 'page_url' => '/canakkale',
        ]);

        $this->assertSame('blocked', $booster->diagnose($keyword)['index']['state']);
    }

    public function test_revert_refuses_to_overwrite_a_later_manual_edit(): void
    {
        $this->fake([]);
        $keyword = $this->activeCanakkaleKeyword();
        Province::where('slug', 'canakkale')->first()->update(['meta_title' => 'Çanakkale Hizmet Bölgesi']);
        $action = app(RankBooster::class)->runStep($keyword, 'meta');
        $this->assertSame(SeoKeywordAction::DONE, $action->status);

        Province::where('slug', 'canakkale')->first()->update(['meta_title' => 'Elle yazılmış başlık']);

        $this->expectExceptionMessage('değiştirilmiş');
        app(RankBooster::class)->revert($action);
    }

    public function test_model_output_without_the_keyword_is_not_saved(): void
    {
        $this->fake([], ['meta_title' => 'Genel Elektronik Alım Hizmeti']);
        $keyword = $this->activeCanakkaleKeyword();
        $before = Province::where('slug', 'canakkale')->value('meta_title');

        $action = app(RankBooster::class)->runStep($keyword, 'meta');

        $this->assertSame(SeoKeywordAction::FAILED, $action->status);
        $this->assertSame($before, Province::where('slug', 'canakkale')->value('meta_title'));
    }

    public function test_page_title_is_reserved_for_the_page_primary_keyword(): void
    {
        $this->fake([]);
        $this->activeCanakkaleKeyword(8.0, 200);
        $secondary = SeoKeyword::create([
            'keyword' => 'biga toplu elektronik alımı', 'status' => true, 'campaign_status' => SeoKeyword::CAMPAIGN_ACTIVE,
            'region' => 'Biga', 'gsc_impressions' => 10, 'target_id' => $this->canakkaleTarget()->id,
        ]);

        $meta = app(RankBooster::class)->diagnose($secondary)['meta'];

        $this->assertSame('done', $meta['state']);
        $this->assertStringContainsString('çanakkale toplu elektronik alımı', $meta['reason']);
    }

    public function test_autopilot_respects_the_daily_limit(): void
    {
        $this->fake([]);
        SeoConfig::set('opportunity_daily_limit', 1);
        $this->activeCanakkaleKeyword();
        SeoKeyword::create([
            'keyword' => 'kocaeli toplu elektronik alımı', 'status' => true, 'campaign_status' => SeoKeyword::CAMPAIGN_ACTIVE,
            'region' => 'Kocaeli', 'last_position' => 9, 'target_id' => SeoTarget::where('url_hash', PathNormalizer::hash('/kocaeli'))->value('id'),
        ]);

        $this->artisan('seo:opportunities')->assertSuccessful();

        $this->assertSame(1, SeoKeyword::whereNotNull('last_boost_at')->count());

        // Aynı gün ikinci tur: işlenen kelime bekleme süresinde, sıradaki alınır.
        $this->artisan('seo:opportunities')->assertSuccessful();
        $this->assertSame(2, SeoKeyword::whereNotNull('last_boost_at')->count());
    }

    public function test_autopilot_off_still_tracks_but_writes_nothing(): void
    {
        $this->fake([['çanakkale toplu elektronik alımı', 8.0, 50]]);
        SeoConfig::set('opportunity_autopilot', false);

        $this->artisan('seo:opportunities')->assertSuccessful();

        $this->assertSame(8.0, $this->keyword('çanakkale toplu elektronik alımı')->last_position);
        $this->assertSame(0, SeoKeywordAction::count());
    }

    public function test_content_enrichment_keeps_keyword_sections(): void
    {
        $this->fake([]);
        $province = Province::where('slug', 'canakkale')->first();
        $block = KeywordSections::wrap('çanakkale toplu elektronik alımı', '<h2>Çanakkale toplu elektronik alımı</h2><p>Bölüm</p>');
        $province->update(['content' => $province->content.$block]);

        app(RegionContentEnricher::class)->enrich($province->fresh());

        $this->assertStringContainsString($block, $province->fresh()->content);
    }

    // ---------- panel ----------

    /**
     * Canlıda yaşandı: kod migration'dan önce sunucuya ulaştı, menü rozeti olmayan sütunu
     * sorguladı ve BÜTÜN panel 500 verdi — migration'ı çalıştıracak Sistem Komutları dahil.
     */
    public function test_panel_still_opens_before_the_migration_has_run(): void
    {
        Schema::table('seo_keywords', function ($table) {
            $table->dropIndex(['campaign_status']);
            $table->dropColumn('campaign_status');
        });

        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail());

        $this->get('/admin')->assertOk();
        $this->get('/admin/system-commands')->assertOk();
    }

    public function test_page_renders_and_requires_permission(): void
    {
        $this->fake([]);
        $this->activeCanakkaleKeyword();

        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail())
            ->get('/admin/seo-merkezi/kelime-sirasi')
            ->assertOk()
            ->assertSee('çanakkale toplu elektronik alımı')
            ->assertSee('Biga');
    }

    public function test_run_now_button_queues_the_daily_round(): void
    {
        $this->fake([]);
        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail());
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(OpportunityHunt::class)
            ->callAction('runNow')
            ->assertNotified('Otomatik tur sıraya alındı');

        $this->assertDatabaseHas('command_runs', ['command_key' => 'opportunities']);
    }

    /** Yeni sitede veri az: kullanıcı filtreleri kapatıp GSC'de ne varsa görmek istedi. */
    public function test_filters_can_be_turned_off_from_the_panel(): void
    {
        $this->fake([
            ['toplu telefon alanlar', 9.0, 1, '/toplu-telefon-alimi'],
            ['toptanelektronikalimi', 1.0, 2, '/'],
            ['çanakkale toplu laptop alanlar', 6.0, 1],
        ]);
        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail());
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        // Varsayılan filtrelerle (en az 3 gösterim, bölge + iş terimi) hiçbiri girmez.
        app(OpportunityHunter::class)->run();
        $this->assertNull($this->keyword('toplu telefon alanlar')?->campaign_status);
        $this->assertNull($this->keyword('çanakkale toplu laptop alanlar')?->campaign_status);

        Livewire::test(OpportunityHunt::class)
            ->callAction('filters', data: [
                'min_impressions' => 1, 'max_position' => 100,
                'require_region' => false, 'require_business' => false, 'max_new' => 100,
            ])
            ->assertNotified('Filtreler kaydedildi');

        app(OpportunityHunter::class)->run();

        $this->assertSame(SeoKeyword::CAMPAIGN_ACTIVE, $this->keyword('toplu telefon alanlar')->campaign_status);
        $this->assertNull($this->keyword('toplu telefon alanlar')->region);
        $this->assertNotNull($this->keyword('toptanelektronikalimi')?->campaign_status, 'İş terimi şartı kapalıyken marka sorgusu da girer.');
        $this->assertSame($this->canakkaleTarget()->id, $this->keyword('çanakkale toplu laptop alanlar')->target_id);
    }
}

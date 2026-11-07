<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\InternalLinkRule;
use App\Models\SeoKeyword;
use App\Models\SeoTarget;
use App\Models\Service;
use App\Services\Discovery\LlmsTxtGenerator;
use App\Services\InternalLink\RuleBuilder;
use App\Services\Seo\BrandKeywordBuilder;
use App\Services\Seo\ContentRegistry;
use App\Services\Seo\TargetSynchroniser;
use App\Support\PathNormalizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Marka ekseni.
 *
 * En kritik kural: bir marka bir hizmet sayfasına BAĞLANMIŞSA (service_id) ayrıca
 * marka sayfası açılmaz. İki sayfa aynı sorguda yarıştığında ikisi de kaybeder.
 * Tohumda hiçbir marka bağlı değildir; bağlı marka senaryosu testte kurulur.
 */
class BrandTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /** Oppo'yu toplu telefon alımı sayfasına bağlar (kendi sayfası kapanır). */
    private function linkOppo(): Brand
    {
        $service = Service::query()->where('slug', 'toplu-telefon-alimi')->firstOrFail();
        $brand = Brand::query()->where('slug', 'oppo')->firstOrFail();
        $brand->update(['service_id' => $service->getKey()]);

        return $brand->refresh();
    }

    public function test_brand_index_lists_active_brands(): void
    {
        $response = $this->get('/markalar');

        $response->assertOk();
        $response->assertSee('Apple', false);
        $response->assertSee('Roborock', false);
    }

    public function test_brand_detail_page_renders(): void
    {
        $brand = Brand::query()->whereNull('service_id')->where('slug', 'samsung')->firstOrFail();

        $response = $this->get($brand->path());

        $response->assertOk();
        $response->assertSee($brand->heading, false);
        // Marka sahipliği açıkça belirtilmeli: yetkili satıcı/servis izlenimi verilmemeli.
        $response->assertSee('yetkili satıcısı veya servisi değiliz', false);
    }

    public function test_inactive_brand_returns_404(): void
    {
        $brand = Brand::query()->whereNull('service_id')->firstOrFail();
        $brand->update(['is_active' => false]);

        // Route model binding aktif olmayan kaydı da bulur; sayfa yine de gizlenmeli.
        $this->get($brand->path())->assertNotFound();
    }

    public function test_brand_with_own_service_page_redirects_instead_of_competing(): void
    {
        $brand = $this->linkOppo();

        $this->assertFalse($brand->hasOwnPage());

        $this->get('/markalar/oppo')->assertRedirect('/toplu-telefon-alimi');
    }

    public function test_category_link_does_not_close_the_brand_page(): void
    {
        $brand = Brand::query()->where('slug', 'apple')->firstOrFail();

        // Ürün grubu (category_service_id) yalnız ilişkilendirme içindir.
        $this->assertNotNull($brand->category_service_id);
        $this->assertTrue($brand->hasOwnPage());
        $this->get('/markalar/apple')->assertOk();
    }

    public function test_linked_brand_gets_no_separate_target_and_is_not_scored(): void
    {
        $this->linkOppo();
        app(TargetSynchroniser::class)->sync();

        $this->assertFalse(
            SeoTarget::query()->where('url_hash', PathNormalizer::hash('/markalar/oppo'))->where('status', true)->exists(),
            'Hizmet sayfasına bağlı marka için ayrı hedef açılmamalı.'
        );

        $registry = app(ContentRegistry::class);
        $brandUrls = $registry->all()->where('contentType', 'brand')->pluck('url')->all();

        $this->assertNotContains(url('/markalar/oppo'), $brandUrls);
        $this->assertContains(url('/markalar/samsung'), $brandUrls);
    }

    public function test_brand_keywords_are_owned_by_the_page_that_actually_answers_them(): void
    {
        $this->linkOppo();
        app(TargetSynchroniser::class)->sync();
        app(BrandKeywordBuilder::class)->build();

        $oppo = SeoKeyword::query()->where('keyword', 'oppo toplu alımı')->first();
        $this->assertNotNull($oppo);
        $this->assertSame('/toplu-telefon-alimi', $oppo->target?->url, 'Bağlı markanın kelimesi hizmet sayfasına ait olmalı.');

        $samsung = SeoKeyword::query()->where('keyword', 'samsung toplu alımı')->first();
        $this->assertNotNull($samsung);
        $this->assertSame('/markalar/samsung', $samsung->target?->url);
    }

    public function test_region_brand_keywords_stay_unowned_and_only_for_featured_provinces(): void
    {
        app(TargetSynchroniser::class)->sync();
        app(BrandKeywordBuilder::class)->build();

        $keyword = SeoKeyword::query()->where('keyword', 'istanbul samsung toplu alımı')->where('keyword_type', 'BLOG_PRIMARY')->first();

        $this->assertNotNull($keyword, 'Öne çıkan il × öne çıkan marka kelimesi üretilmeli.');
        $this->assertFalse($keyword->hasOwner(), 'Blog hattına bırakılan kelime sahipsiz kalmalı.');

        // 973 ilçe × marka üretilmez; öne çıkmayan il de üretilmez.
        $this->assertNull(SeoKeyword::query()->where('keyword', 'kadıköy samsung toplu alımı')->first());
        $this->assertNull(SeoKeyword::query()->where('keyword', 'bartın samsung toplu alımı')->first());
        // Öne çıkmayan marka bölgeyle çarpılmaz.
        $this->assertNull(SeoKeyword::query()->where('keyword', 'istanbul oppo toplu alımı')->first());
    }

    public function test_deactivating_a_brand_deactivates_its_keywords_without_deleting_them(): void
    {
        app(TargetSynchroniser::class)->sync();
        app(BrandKeywordBuilder::class)->build();

        $before = SeoKeyword::query()->where('keyword', 'xiaomi toplu alımı')->firstOrFail();
        $this->assertTrue($before->status);

        Brand::query()->where('slug', 'xiaomi')->update(['is_active' => false]);
        app(BrandKeywordBuilder::class)->build();

        $after = SeoKeyword::query()->where('keyword', 'xiaomi toplu alımı')->first();

        $this->assertNotNull($after, 'Kelime SİLİNMEMELİ, yalnız pasife alınmalı.');
        $this->assertFalse($after->status);
    }

    public function test_brand_link_rules_avoid_ambiguous_single_word_anchors(): void
    {
        app(TargetSynchroniser::class)->sync();
        app(RuleBuilder::class)->build();

        $anchors = InternalLinkRule::query()->where('is_active', true)->pluck('anchor_text')->all();

        // "Apple" tek başına metinde başka bağlamda geçer; anchor olmamalı.
        $this->assertNotContains('Apple', $anchors);
        $this->assertContains('Apple toplu alımı', $anchors);
    }

    public function test_sitemap_includes_brand_pages_but_not_the_redirecting_one(): void
    {
        $this->linkOppo();
        Cache::forget('sitemap.xml');

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertSee(url('/markalar/samsung'), false);
        $response->assertSee(url('/markalar'), false);
        $response->assertDontSee(url('/markalar/oppo'), false);
    }

    public function test_llms_txt_declares_brands_and_the_independence_notice(): void
    {
        $this->linkOppo();
        app(LlmsTxtGenerator::class)->generate();

        $index = file_get_contents(public_path('llms.txt'));

        $this->assertStringContainsString('Toplu alım yaptığımız markalar', $index);
        $this->assertStringContainsString('/markalar/samsung', $index);
        $this->assertStringContainsString('yetkili satıcısı veya servisi değildir', $index);
        $this->assertStringNotContainsString('/markalar/oppo', $index);
    }
}

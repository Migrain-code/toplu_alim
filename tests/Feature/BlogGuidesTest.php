<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\SeoKeyword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogGuidesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guides_are_seeded_published_and_substantial(): void
    {
        $posts = Blog::query()->published()->get();

        $this->assertGreaterThanOrEqual(12, $posts->count());

        foreach ($posts as $post) {
            $words = count(preg_split('/\s+/u', trim(strip_tags($post->body_html))));
            $this->assertGreaterThanOrEqual(900, $words, $post->slug.' çok kısa ('.$words.' kelime)');
            $this->assertNotNull($post->blog_category_id, $post->slug.' kategorisiz');
            $this->assertLessThanOrEqual(60, mb_strlen($post->meta_title), $post->slug.' meta başlığı uzun');
            $this->assertCount(4, $post->faqs, $post->slug);
            // Uydurma bilgi koruması: yüzde/istatistik ve telefon numarası yok.
            $this->assertDoesNotMatchRegularExpression('/%\s?\d|\d\s?%|\+90\s?\d/u', strip_tags($post->body_html), $post->slug);
        }
    }

    public function test_every_internal_link_in_guides_resolves(): void
    {
        $paths = Blog::query()->published()->pluck('body_html')
            ->flatMap(fn ($html) => preg_match_all('#href="(/[^"\#?]*)"#', $html, $m) ? $m[1] : [])
            ->unique();

        $this->assertNotEmpty($paths);

        foreach ($paths as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_guide_page_has_toc_article_and_faq_schema(): void
    {
        $post = Blog::query()->where('slug', 'telefonlari-toplu-nereye-satabilirim')->firstOrFail();

        $this->get($post->url)
            ->assertOk()
            ->assertSee('<nav class="toc', false)
            ->assertSee('"@type":"Article"', false)
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('content="index, follow"', false);
    }

    public function test_question_keywords_are_owned_by_their_guides(): void
    {
        $keyword = SeoKeyword::query()->where('keyword', 'telefonları toplu nereye satabilirim')->first();

        $this->assertNotNull($keyword);
        $this->assertSame('telefonlari-toplu-nereye-satabilirim', $keyword->ownerBlog?->slug);
    }

    public function test_guides_get_links_from_home_service_pages_feed_and_sitemap(): void
    {
        $post = Blog::query()->where('slug', 'telefonlari-toplu-nereye-satabilirim')->firstOrFail();

        $this->get('/')->assertSee('Toplu Satış Öncesi Bilmeniz Gerekenler');
        $this->get('/toplu-telefon-alimi')->assertSee('İlgili rehberler')->assertSee($post->url, false);
        $this->get('/blog/feed.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->assertSee('<link>'.$post->url.'</link>', false);
        $this->get('/sitemap.xml')->assertSee($post->url, false);
        $this->get('/')->assertSee('type="application/rss+xml"', false);
    }

    public function test_blog_pagination_uses_site_template_in_turkish(): void
    {
        // Laravel'in varsayılan (Tailwind) şablonu ön yüzde stilsiz kalıp dev oklar çiziyordu.
        $this->get('/blog')
            ->assertOk()
            ->assertSee('class="site-pagination"', false)
            ->assertSee('Sonraki')
            ->assertDontSee('Showing');

        $this->get('/blog?page=2')->assertOk()->assertSee('Önceki');
    }
}

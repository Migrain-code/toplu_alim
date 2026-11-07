<?php

namespace Tests\Feature;

use App\Jobs\SubmitIndexNow;
use App\Models\Blog;
use App\Models\District;
use App\Models\Province;
use App\Services\Seo\IndexNow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class IndexNowTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = '0123456789abcdef0123456789abcdef';

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'https://toptanelektronikalimi.com']);
        url()->forceRootUrl('https://toptanelektronikalimi.com');
    }

    public function test_key_file_is_served_only_for_the_configured_key(): void
    {
        $this->get('/'.self::KEY.'.txt')->assertNotFound();

        config(['seo.indexnow.key' => self::KEY]);

        $this->get('/'.self::KEY.'.txt')->assertOk()->assertSee(self::KEY);
        $this->get('/ffffffffffffffffffffffffffffffff.txt')->assertNotFound();
    }

    public function test_submit_posts_only_same_host_urls(): void
    {
        config(['seo.indexnow.key' => self::KEY]);
        Http::fake([IndexNow::ENDPOINT => Http::response('', 200)]);

        $sent = app(IndexNow::class)->submit([
            'https://toptanelektronikalimi.com/istanbul',
            'https://baska-site.com/x',
        ]);

        $this->assertSame(1, $sent);
        Http::assertSent(fn ($request) => $request['host'] === 'toptanelektronikalimi.com'
            && $request['key'] === self::KEY
            && $request['keyLocation'] === 'https://toptanelektronikalimi.com/'.self::KEY.'.txt'
            && $request['urlList'] === ['https://toptanelektronikalimi.com/istanbul']);
    }

    public function test_nothing_is_sent_without_a_key(): void
    {
        Http::fake();

        $this->assertSame(0, app(IndexNow::class)->submit(['https://toptanelektronikalimi.com/istanbul']));
        Http::assertNothingSent();
    }

    public function test_publishing_a_post_queues_a_notification(): void
    {
        config(['seo.indexnow.key' => self::KEY]);
        Queue::fake();

        $post = Blog::query()->where('status', Blog::STATUS_PUBLISHED)->firstOrFail();
        $post->update(['body_html' => $post->body_html.'<p>Güncelleme.</p>']);

        Queue::assertPushed(SubmitIndexNow::class, fn ($job) => in_array($post->url, $job->urls, true));
    }

    public function test_draft_post_is_not_notified(): void
    {
        config(['seo.indexnow.key' => self::KEY]);
        Queue::fake();

        Blog::query()->create(['title' => 'Taslak', 'slug' => 'taslak-yazi', 'body_html' => '<p>x</p>', 'status' => Blog::STATUS_DRAFT]);

        Queue::assertNotPushed(SubmitIndexNow::class);
    }

    /**
     * İnce içerik kuralıyla uyumlu: ilçe metni yazılıp sayfa dizine açılınca bildirilir;
     * metinsiz (noindex) ilçe değişikliği bildirilmez.
     */
    public function test_region_is_notified_only_when_it_becomes_indexable(): void
    {
        config(['seo.indexnow.key' => self::KEY]);
        Queue::fake();

        $district = District::where('slug', 'kadikoy')->firstOrFail();
        $district->update(['description' => 'kısa değişiklik']);
        Queue::assertNotPushed(SubmitIndexNow::class);

        $district->update(['content' => '<p>'.str_repeat('Kadıköy özgün metin kelime ', 40).'</p>']);
        Queue::assertPushed(SubmitIndexNow::class, fn ($job) => $job->urls === [$district->url]);
    }

    public function test_province_opening_notifies_its_category_pages_too(): void
    {
        config(['seo.indexnow.key' => self::KEY]);
        Queue::fake();

        $province = Province::where('slug', 'mardin')->firstOrFail();
        $province->update(['content' => '<p>'.str_repeat('Mardin özgün metin kelime ', 40).'</p>']);

        Queue::assertPushed(SubmitIndexNow::class, fn ($job) => in_array($province->url, $job->urls, true)
            && in_array(url('/mardin/toplu-telefon-alimi'), $job->urls, true));
    }

    public function test_command_submits_the_sitemap_urls(): void
    {
        config(['seo.indexnow.key' => self::KEY]);
        Http::fake([IndexNow::ENDPOINT => Http::response('', 202)]);

        $this->artisan('seo:indexnow', ['--all' => true])->assertSuccessful();

        Http::assertSent(fn ($request) => in_array(url('/istanbul/toplu-telefon-alimi'), $request['urlList'], true)
            && ! in_array(url('/mardin'), $request['urlList'], true));
    }

    public function test_saving_content_refreshes_the_cached_sitemap(): void
    {
        $this->get('/sitemap.xml')->assertDontSee(url('/mardin').'<', false);

        Province::where('slug', 'mardin')->firstOrFail()
            ->update(['content' => '<p>'.str_repeat('Mardin özgün metin kelime ', 40).'</p>']);

        $this->get('/sitemap.xml')->assertSee(url('/mardin').'<', false);
    }
}

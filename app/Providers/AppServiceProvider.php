<?php

namespace App\Providers;

use App\Models\AiCrawlerVisit;
use App\Models\AiGeneration;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Brand;
use App\Models\District;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\InternalLinkRule;
use App\Models\NotFoundLog;
use App\Models\Page;
use App\Models\Province;
use App\Models\QuoteRequest;
use App\Models\Redirect;
use App\Models\SeoAnalysis;
use App\Models\SeoKeyword;
use App\Models\SeoTarget;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Observers\IndexNowObserver;
use App\Policies\ContentPolicy;
use App\Policies\QuoteRequestPolicy;
use App\Policies\SeoPolicy;
use App\Policies\UserPolicy;
use App\Support\Queue\SharedHostingWorker;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Queue\Worker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Hosting pcntl fonksiyonlarını kapatıyor; Laravel'in işçisi bunlarla çöküyordu.
        $this->app->extend('queue.worker', fn (Worker $worker) => SharedHostingWorker::from($worker));

        // İstek başına bir kez: ayarlar ve menü verisi. "scoped" kayıtlar her kuyruk
        // işinin başında da sıfırlanır, böylece uzun yaşayan işçi eski veriyle kalmaz.
        $this->app->scoped(Setting::MEMO, fn () => Setting::loadFromStore());

        // Ana sayfa hem kendisi hem içindeki personel kartları için aynı listeyi ister.
        $this->app->scoped('site.staff', fn () => User::query()
            ->public()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->ordered()
            ->get());

        $this->app->scoped('site.nav', fn () => [
            'navCategories' => Service::query()->active()->categories()->ordered()->get(['id', 'title', 'slug', 'icon', 'group']),
            'navConditions' => Service::query()->active()->conditions()->ordered()->get(['id', 'title', 'slug', 'icon', 'group']),
            'navBrands' => Brand::query()->active()->ordered()->get(['id', 'name', 'slug', 'service_id']),
            // 81 il menüye sığmaz: yalnız öne çıkan büyük şehirler; tamamı /bolgeler'de.
            'navProvinces' => Province::query()->active()->featured()->get(['id', 'name', 'slug']),
            'footerPages' => Page::query()->active()->where('show_in_footer', true)->ordered()->get(['id', 'title', 'slug']),
        ]);
    }

    /**
     * Yetki ilkeleri.
     *
     * Model başına ayrı dosya yerine dört ortak ilke kullanılır: içerik, SEO,
     * teklif talebi ve kullanıcı. On sekiz neredeyse aynı dosya bakımı zorlaştırır
     * ve bir tanesini güncellemeyi unutmak sessiz bir yetki açığı yaratır.
     */
    private function registerPolicies(): void
    {
        $content = [
            Service::class, Province::class, District::class,
            Brand::class,
            Page::class, Blog::class, BlogCategory::class,
            Testimonial::class, Faq::class, Feature::class,
        ];

        foreach ($content as $model) {
            Gate::policy($model, ContentPolicy::class);
        }

        $seo = [
            SeoKeyword::class, SeoTarget::class,
            InternalLinkRule::class, AiGeneration::class,
            Redirect::class, NotFoundLog::class,
            AiCrawlerVisit::class, SeoAnalysis::class,
        ];

        foreach ($seo as $model) {
            Gate::policy($model, SeoPolicy::class);
        }

        Gate::policy(QuoteRequest::class, QuoteRequestPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));

        // APP_URL https ise tüm üretilen adresler (canonical, site haritası, IndexNow) https
        // olur; istek http ile gelse bile kopya http adresleri yayılmaz.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        $this->registerPolicies();

        // Yayına giren / metni değişen dizine açık sayfalar IndexNow'a bildirilir; her
        // içerik kaydında site haritası ve RSS önbelleği düşer.
        foreach ([Blog::class, Province::class, District::class,
            Service::class, Brand::class, Page::class] as $model) {
            $model::observe(IndexNowObserver::class);
        }

        RateLimiter::for('quote', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

        // Header / footer / teklif formu için ortak veriler
        // Üç görünüm aynı veriyi kullanır; sorgular üç kez değil, istek başına bir kez çalışır.
        View::composer(['partials.header', 'partials.footer', 'partials.mobile-nav'], function ($view) {
            $view->with($this->app->make('site.nav'));
        });

        /*
         * Sitede gösterilecek personel.
         *
         * Müşteri, ana numaraya değil doğrudan ilgili kişiye ulaşabilsin diye
         * telefonu girilmiş ve "web sitesinde göster" işaretli personel yayınlanır.
         * Telefonu olmayan kayıt listelenmez — tıklanacak bir şey olmadan kart göstermek
         * ziyaretçiyi çıkmaza sokar.
         */
        View::composer(['partials.staff-cards', 'contact', 'home'], function ($view) {
            $view->with('staff', $this->app->make('site.staff'));
        });

        View::composer('partials.quote-form', function ($view) {
            $view->with([
                // İlçe listesi forma GÖMÜLMEZ (973 kayıt her sayfayı şişirirdi); ilçe
                // sayfasından gelen talepte ilçe gizli alanla taşınır.
                'formProvinces' => Province::query()->active()->ordered()->get(['id', 'name']),
                'formServices' => Service::query()->active()->ordered()->get(['id', 'title', 'group']),
                'kvkkPage' => Page::query()->active()->where('slug', 'like', '%kvkk%')->first(['id', 'title', 'slug']),
            ]);
        });
    }
}

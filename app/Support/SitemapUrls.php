<?php

namespace App\Support;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\District;
use App\Models\Page;
use App\Models\Province;
use App\Models\Service;

/**
 * Dizine açık adreslerin TEK listesi: site haritası da IndexNow bildirimi de bunu kullanır.
 * İki ayrı liste tutulursa biri güncellenip diğeri unutulur ve noindex bir sayfa
 * arama motorlarına "yeni sayfa" diye bildirilir.
 *
 * @return array<int, array{loc: string, lastmod?: mixed, priority: string, changefreq: string}>
 */
class SitemapUrls
{
    public static function all(): array
    {
        $urls = [
            ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => route('services.index'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => route('brands.index'), 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['loc' => route('blog.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => route('regions.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => route('about'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => route('faq'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => route('contact'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('quote.create'), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        foreach (Service::query()->active()->ordered()->get() as $service) {
            $urls[] = ['loc' => $service->url, 'lastmod' => $service->updated_at, 'priority' => '0.9', 'changefreq' => 'monthly'];
        }

        // Kendi hizmet sayfasına bağlı marka haritaya GİRMEZ: adresi 301 döner.
        foreach (Brand::query()->active()->whereNull('service_id')->ordered()->get() as $brand) {
            $urls[] = ['loc' => $brand->url, 'lastmod' => $brand->updated_at, 'priority' => '0.7', 'changefreq' => 'monthly'];
        }

        // Bölge sayfaları YALNIZ kendine özgü metni olanlar (HasIndexableContent).
        // Diğerleri noindex döner; haritaya koymak Google'a çelişkili sinyal verir.
        $services = Service::query()->active()->ordered()->get();

        foreach (Province::query()->active()->ordered()->get() as $province) {
            if (! $province->isIndexable()) {
                continue;
            }

            $urls[] = ['loc' => $province->url, 'lastmod' => $province->updated_at, 'priority' => '0.8', 'changefreq' => 'monthly'];

            // İl × hizmet: 12 hizmetin hepsi (kategori + durum).
            foreach ($services as $service) {
                $urls[] = ['loc' => $province->serviceUrl($service), 'lastmod' => max($province->updated_at, $service->updated_at), 'priority' => '0.8', 'changefreq' => 'monthly'];
            }
        }

        foreach (District::query()->active()->whereNotNull('content')->with('province')->get() as $district) {
            if ($district->province?->is_active && $district->isIndexable()) {
                $urls[] = ['loc' => $district->url, 'lastmod' => $district->updated_at, 'priority' => '0.7', 'changefreq' => 'monthly'];
            }
        }

        // Blog: yayın tarihi gelmiş yazılar; yeni yazı haritada öne alınır (yüksek öncelik).
        foreach (Blog::query()->published()->orderByDesc('publish_at')->get() as $post) {
            $urls[] = ['loc' => $post->url, 'lastmod' => $post->updated_at, 'priority' => '0.7', 'changefreq' => 'weekly'];
        }

        foreach (Page::query()->active()->get() as $page) {
            $urls[] = ['loc' => $page->url, 'lastmod' => $page->updated_at, 'priority' => '0.3', 'changefreq' => 'yearly'];
        }

        return $urls;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\Province;
use App\Models\Service;
use App\Support\SchemaOrg;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('services.index', [
            'categories' => Service::query()->active()->categories()->ordered()->get(),
            'conditions' => Service::query()->active()->conditions()->ordered()->get(),
            'metaTitle' => 'Toplu Alım Yaptığımız Ürünler | Telefon, Tablet, Bilgisayar',
            'metaDescription' => 'Toplu telefon, tablet, bilgisayar, robot süpürge ve elektronik cihaz alımı; stok fazlası, depo boşaltma, tasfiye, teşhir, iade ve arızalı ürün alımı.',
            'canonical' => route('services.index'),
            'jsonLd' => [SchemaOrg::breadcrumbs([
                ['name' => 'Ana Sayfa', 'url' => url('/')],
                ['name' => 'Hizmetler', 'url' => route('services.index')],
            ])],
        ]);
    }

    public function show(Service $service): View
    {
        $breadcrumbs = [
            ['name' => 'Ana Sayfa', 'url' => url('/')],
            ['name' => 'Hizmetler', 'url' => route('services.index')],
            ['name' => $service->title, 'url' => $service->url],
        ];

        $jsonLd = [
            SchemaOrg::breadcrumbs($breadcrumbs),
            SchemaOrg::service($service),
        ];

        if ($faq = SchemaOrg::faq($service->faqs ?? [])) {
            $jsonLd[] = $faq;
        }

        return view('services.show', [
            'service' => $service,
            'otherServices' => Service::query()->active()->ordered()->whereKeyNot($service->getKey())->get(),
            'featuredProvinces' => Province::query()->active()->featured()->get(['id', 'name', 'slug']),
            'allProvinces' => Province::query()->active()->ordered()->get(['id', 'name', 'slug']),
            'brands' => Brand::query()->active()->where('category_service_id', $service->id)->ordered()->get(['id', 'name', 'slug', 'service_id']),
            'breadcrumbs' => $breadcrumbs,
            'metaTitle' => $service->meta_title ?: $service->title.' | Türkiye Geneli',
            'metaDescription' => $service->meta_description ?: $service->short_description,
            'canonical' => $service->url,
            'jsonLd' => $jsonLd,
            'whatsappUrl' => whatsapp_url(null, $service->title),
            'formService' => $service->id,
            // Bu hizmete gövdesinden link veren rehberler: hizmet sayfası → yazı, yazı → hizmet
            // çift yönlü bağ kurar; yeni yazı güçlü bir sayfadan link alıp hızlı keşfedilir.
            'guides' => Blog::query()->published()
                ->where('body_html', 'like', '%href="/'.$service->slug.'"%')
                ->orderByDesc('publish_at')->limit(3)->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\District;
use App\Models\Province;
use App\Models\Service;
use App\Support\SchemaOrg;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function index(): View
    {
        $provinces = Province::query()->active()->ordered()->withCount('activeDistricts')->get(['id', 'name', 'slug']);
        $collator = new \Collator('tr_TR');

        return view('regions.index', [
            'provinces' => $provinces,
            'featured' => Province::query()->active()->featured()->withCount('activeDistricts')->get(['id', 'name', 'slug']),
            // Türkçe alfabe sırası (Ç, İ, Ş kendi yerinde); SORT_LOCALE_STRING sistem
            // yereline bağlı ve sunucuda genelde C/en olduğundan onları sona atıyordu.
            'letters' => $provinces
                ->sort(fn (Province $a, Province $b) => $collator->compare($a->name, $b->name))
                ->groupBy(fn (Province $p) => mb_substr($p->name, 0, 1)),
            'categories' => Service::query()->active()->categories()->ordered()->get(['id', 'title', 'slug', 'icon']),
            'metaTitle' => 'Toplu Elektronik Alımı Yaptığımız İller | 81 İl',
            'metaDescription' => 'Türkiye\'nin 81 ilinde ve tüm ilçelerinde toplu telefon, tablet, bilgisayar, robot süpürge ve elektronik alımı. İlinizi seçin, hemen fiyat teklifi alın.',
            'canonical' => route('regions.index'),
            'jsonLd' => [SchemaOrg::breadcrumbs([
                ['name' => 'Ana Sayfa', 'url' => url('/')],
                ['name' => 'Bölgeler', 'url' => route('regions.index')],
            ])],
        ]);
    }

    public function province(Province $province): View
    {
        $province->load('activeDistricts:id,province_id,name,slug');

        $breadcrumbs = [
            ['name' => 'Ana Sayfa', 'url' => url('/')],
            ['name' => 'Bölgeler', 'url' => route('regions.index')],
            ['name' => $province->name, 'url' => $province->url],
        ];

        $jsonLd = [SchemaOrg::breadcrumbs($breadcrumbs)];

        if ($faq = SchemaOrg::faq($province->faqs ?? [])) {
            $jsonLd[] = $faq;
        }

        return view('regions.province', [
            'province' => $province,
            'categories' => Service::query()->active()->categories()->ordered()->get(),
            'conditions' => Service::query()->active()->conditions()->ordered()->get(),
            'nearby' => $this->otherFeatured($province),
            'breadcrumbs' => $breadcrumbs,
            'metaTitle' => $province->meta_title ?: $province->name.' Toplu Elektronik Alımı',
            'metaDescription' => $province->meta_description ?: $province->description,
            'canonical' => $province->url,
            'robots' => $province->robotsMeta(),
            'jsonLd' => $jsonLd,
            'whatsappUrl' => whatsapp_url($province->name),
            'formProvince' => $province->id,
        ]);
    }

    /**
     * /{il}/{ikinci-parça}: ikinci parça bir hizmet (12'sinin hepsi) ya da ilçe olabilir.
     * Çakışmaz: hizmet slug'ları "toplu-…-alimi" / "…-elektronik-…" kalıbında, ilçe adı değil.
     */
    public function district(Province $province, string $district): View
    {
        abort_unless($province->is_active, 404);

        if ($service = Service::query()->active()->where('slug', $district)->first()) {
            return $this->provinceService($province, $service);
        }

        /** @var District $model */
        $model = $province->activeDistricts()->where('slug', $district)->firstOrFail();
        $model->setRelation('province', $province);

        $breadcrumbs = [
            ['name' => 'Ana Sayfa', 'url' => url('/')],
            ['name' => 'Bölgeler', 'url' => route('regions.index')],
            ['name' => $province->name, 'url' => $province->url],
            ['name' => $model->name, 'url' => $model->url],
        ];

        $jsonLd = [SchemaOrg::breadcrumbs($breadcrumbs)];

        if ($faq = SchemaOrg::faq($model->faqs ?? [])) {
            $jsonLd[] = $faq;
        }

        return view('regions.district', [
            'province' => $province,
            'district' => $model,
            'categories' => Service::query()->active()->categories()->ordered()->get(),
            'conditions' => Service::query()->active()->conditions()->ordered()->get(),
            'otherDistricts' => $province->activeDistricts()->whereKeyNot($model->getKey())->get(['id', 'province_id', 'name', 'slug']),
            'breadcrumbs' => $breadcrumbs,
            'metaTitle' => $model->meta_title ?: $model->name.' Toplu Elektronik Alımı | '.$province->name,
            'metaDescription' => $model->meta_description ?: $model->description,
            'canonical' => $model->url,
            'robots' => $model->robotsMeta(),
            'jsonLd' => $jsonLd,
            'whatsappUrl' => whatsapp_url($model->name.', '.$province->name),
            'formProvince' => $province->id,
            'formDistrict' => $model->id,
        ]);
    }

    /**
     * İl × hizmet: "İstanbul Toplu Telefon Alımı", "İstanbul Stok Fazlası Elektronik Alımı".
     *
     * Kopya içerik önlemi: hizmetin uzun metni burada TEKRARLANMAZ (yalnız ilk paragrafı
     * özet olarak gösterilir, ayrıntı ana hizmet sayfasına bağlanır). Gövde ile
     * özgü bölümlerden oluşur: il adıyla giriş, ilin ilçeleri, ile göre SSS.
     * Sayfa yalnız il sayfası dizine açıksa (il kendine özgü metne sahipse) index alır.
     */
    public function provinceService(Province $province, Service $service): View
    {
        $province->load('activeDistricts:id,province_id,name,slug');
        $url = $province->serviceUrl($service);
        $title = $province->name.' '.$service->title;
        $serviceName = mb_strtolower($service->title, 'UTF-8');

        $breadcrumbs = [
            ['name' => 'Ana Sayfa', 'url' => url('/')],
            ['name' => $province->name, 'url' => $province->url],
            ['name' => $service->title, 'url' => $url],
        ];

        $faqs = array_merge($this->localFaqs($province, $service), array_slice((array) $service->faqs, 0, 2));

        $jsonLd = [
            SchemaOrg::breadcrumbs($breadcrumbs),
            SchemaOrg::service($service, $province->name, $url),
        ];

        if ($faq = SchemaOrg::faq($faqs)) {
            $jsonLd[] = $faq;
        }

        return view('regions.service', [
            'province' => $province,
            'service' => $service,
            'title' => $title,
            'serviceName' => $serviceName,
            'summary' => $this->firstParagraph((string) $service->description),
            'faqs' => $faqs,
            'otherServices' => Service::query()->active()->ordered()->whereKeyNot($service->getKey())->get(),
            'brands' => Brand::query()->active()->where('category_service_id', $service->id)->ordered()->get(['id', 'name', 'slug', 'service_id']),
            'nearby' => $this->otherFeatured($province),
            'breadcrumbs' => $breadcrumbs,
            'metaTitle' => $this->limitWords($title.' | Hemen Fiyat Alın', 60),
            'metaDescription' => $this->limitWords($province->name.' ve ilçelerinde '.$serviceName.'. Model, adet ve durumu iletin; telefonla hemen toplu fiyat teklifi alın.', 155),
            'canonical' => $url,
            'robots' => $province->robotsMeta(),
            'jsonLd' => $jsonLd,
            'whatsappUrl' => whatsapp_url($province->name, $service->title),
            'formProvince' => $province->id,
            'formService' => $service->id,
        ]);
    }

    /**
     * İle özgü sorular: il adı + ilin ilçeleri metne girer, böylece 81 sayfa birbirinin
     * kopyası olmaz. Uydurma bilgi yoktur (süre/fiyat sözü verilmez).
     *
     * @return array<int, array{question: string, answer: string}>
     */
    private function localFaqs(Province $province, Service $service): array
    {
        $il = $province->name;
        $name = mb_strtolower($service->title, 'UTF-8');
        $districts = $province->activeDistricts->pluck('name')->take(4)->implode(', ');

        return [
            [
                'question' => $il.' içinde '.$name.' için ürünleri yerinden teslim alıyor musunuz?',
                'answer' => 'Evet. '.$il.' merkez ve ilçelerinden'.($districts ? ' ('.$districts.' gibi)' : '').' gelen talepleri değerlendiriyoruz. Teslim alma şekli; ürün adedi, hacmi ve adrese göre teklifle birlikte planlanır, uygun durumlarda kargo veya nakliye ile de alım yapılır.',
            ],
            [
                'question' => $il.' için fiyat teklifi almak üzere hangi bilgileri iletmeliyim?',
                'answer' => 'Marka ve model, adet, ürün durumu (sıfır, teşhir, ikinci el, arızalı), kutu/aksesuar bilgisi ve ürünlerin '.$il.' içindeki konumu yeterli. Varsa stok listesini ya da birkaç fotoğrafı WhatsApp üzerinden gönderebilirsiniz.',
            ],
            [
                'question' => $il.' dışındaki depolarımızdaki ürünleri de birlikte satabilir miyiz?',
                'answer' => 'Evet. Türkiye\'nin 81 ilinden alım yapıyoruz; '.$il.' ve diğer illerdeki stoklarınızı tek teklifte toplayabiliriz. Her lokasyon için teslim alma planı ayrıca netleştirilir.',
            ],
        ];
    }

    private function firstParagraph(string $html): string
    {
        return preg_match('/<p[^>]*>(.*?)<\/p>/su', $html, $m) ? trim(strip_tags($m[1])) : '';
    }

    /** Metni kelime ortasından kesmeden en fazla $max karaktere indirir. */
    private function limitWords(string $text, int $max): string
    {
        if (mb_strlen($text) <= $max) {
            return $text;
        }

        $cut = mb_substr($text, 0, $max + 1);

        return rtrim(mb_substr($cut, 0, (int) mb_strrpos($cut, ' ')), ' ,;:|');
    }

    private function otherFeatured(Province $province): Collection
    {
        return Province::query()->active()->featured()->whereKeyNot($province->getKey())->get(['id', 'name', 'slug']);
    }
}

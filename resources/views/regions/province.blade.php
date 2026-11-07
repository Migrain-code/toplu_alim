@extends('layouts.app')

@section('content')
    @include('partials.page-banner', [
        'title' => $province->name.' Toplu Elektronik Alımı',
        'subtitle' => $province->description,
        'breadcrumbs' => $breadcrumbs,
        'actions' => true,
        'whatsappUrl' => $whatsappUrl,
    ])

    <section class="section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    @if (filled($province->content))
                        <div class="content-prose mb-5">{!! $province->content !!}</div>
                    @else
                        <div class="content-prose mb-5">
                            <p>{{ $province->name }} ve ilçelerinde elinde toplu telefon, tablet, bilgisayar, robot süpürge ya da başka elektronik ürün bulunan bayi, mağaza, şirket ve bireylerden adetli alım yapıyoruz. Ürünlerin sıfır, teşhir, ikinci el ya da arızalı olması fark etmez; fiyatı model, adet ve durum belirler.</p>
                            <p>Ürün listesini telefonla ya da WhatsApp'tan iletin, size toplu fiyat teklifimizi bildirelim. Anlaşma sonrası teslim alma şeklini ({{ $province->name }} içinde yerinde teslim ya da kargo/nakliye) birlikte planlıyoruz.</p>
                        </div>
                    @endif

                    <h2 class="h3 mb-3">{{ $province->name }} genelinde aldığımız ürünler</h2>
                    <div class="row g-3 mb-5">
                        @foreach ($categories as $i => $service)
                            <div class="col-md-6">
                                <a class="service-list-item" href="{{ $province->serviceUrl($service) }}"><i class="{{ $service->icon_class }}"></i>{{ $province->name }} {{ mb_strtolower($service->title, 'UTF-8') }}</a>
                            </div>
                        @endforeach
                    </div>

                    @if ($conditions->isNotEmpty())
                        <h2 class="h3 mb-3">{{ $province->name }} içinde hangi durumdaki ürünleri alıyoruz?</h2>
                        <div class="row g-3 mb-5">
                            @foreach ($conditions as $service)
                                <div class="col-md-6">
                                    <a class="service-list-item" href="{{ $province->serviceUrl($service) }}"><i class="{{ $service->icon_class }}"></i>{{ $province->name }} {{ mb_strtolower($service->title, 'UTF-8') }}</a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($province->activeDistricts->isNotEmpty())
                        <h2 class="h3 mb-3">{{ $province->name }} ilçeleri</h2>
                        <div class="district-grid mb-5">
                            @foreach ($province->activeDistricts as $district)
                                <a href="{{ url('/'.$province->slug.'/'.$district->slug) }}"><i class="fa-solid fa-location-dot"></i>{{ $district->name }}</a>
                            @endforeach
                        </div>
                    @endif

                    @if (! empty($province->faqs))
                        <h2 class="h3 mb-3">{{ $province->name }} için sık sorulan sorular</h2>
                        <div class="mb-5">
                            @include('partials.faq-accordion', ['faqs' => $province->faqs, 'accordionId' => 'provinceFaq', 'openFirst' => true])
                        </div>
                    @endif
                </div>
                <aside class="col-lg-4">
                    <div class="sidebar">
                        @include('partials.sidebar-cta', ['ctaTitle' => $province->name.' için fiyat alın', 'whatsappUrl' => $whatsappUrl])
                        <div class="sidebar-widget">
                            <h3>Diğer büyük şehirler</h3>
                            <div class="chips">
                                @foreach ($nearby as $other)
                                    <a href="{{ $other->url }}">{{ $other->name }}</a>
                                @endforeach
                                <a href="{{ route('regions.index') }}">Tüm iller</a>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="section bg-mist">
        <div class="container" style="max-width:860px">
            <x-section-title subtitle="Teklif formu" :title="$province->name.' için teklif isteyin'" :center="true" class="mb-4" />
            @include('partials.quote-form', ['formId' => 'provinceQuoteForm'])
        </div>
    </section>

    <div class="pt-5">@include('partials.cta-band', ['whatsappUrl' => $whatsappUrl, 'ctaTitle' => $province->name.' içinde toplu ürününüz mü var?'])</div>
@endsection

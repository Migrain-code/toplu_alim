@extends('layouts.app')

{{-- İl × hizmet: /istanbul/toplu-telefon-alimi, /istanbul/stok-fazlasi-elektronik-alimi --}}
@section('content')
    @include('partials.page-banner', [
        'title' => $title,
        'subtitle' => $province->name.' ve ilçelerinde '.$serviceName.'. Ürün listenizi iletin, telefonla hemen toplu fiyat teklifi alın.',
        'breadcrumbs' => $breadcrumbs,
        'actions' => true,
        'whatsappUrl' => $whatsappUrl,
    ])

    <section class="section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="content-prose mb-5">
                        <p>
                            <strong>{{ $province->name }}</strong> genelinde {{ $serviceName }} için elinizdeki ürünleri tek seferde değerlendiriyoruz.
                            {{ $province->name }} merkez ve ilçelerindeki bayi, mağaza, şirket, depo ve e-ticaret satıcılarından gelen talepleri
                            model, adet ve durum bilgisine göre fiyatlandırıyor; anlaşma sonrası teslim almayı adresinize göre planlıyoruz.
                        </p>
                        @if ($summary)
                            <p>{{ $summary }}</p>
                        @endif
                        <p>Ayrıntılı bilgi için <a href="{{ $service->url }}">{{ $service->title }}</a> sayfamıza da göz atabilirsiniz.</p>
                    </div>

                    @if (! empty($service->what_we_do))
                        <h2 class="h3 mb-3">{{ $province->name }} içinde neleri alıyoruz?</h2>
                        <ul class="check-list two-col mb-5">
                            @foreach ($service->what_we_do as $item)
                                <li><i class="fa-solid fa-circle-check"></i><span class="text-dark">{{ $item }}</span></li>
                            @endforeach
                        </ul>
                    @endif

                    <h2 class="h3 mb-3">{{ $province->name }} için satış nasıl ilerler?</h2>
                    <div class="process-steps mb-5">
                        <div class="step"><div class="num">1</div><div><h3>Arayın ya da yazın</h3><p>{{ $province->name }} içindeki ürünleriniz için telefon veya WhatsApp üzerinden bize ulaşın.</p></div></div>
                        <div class="step"><div class="num">2</div><div><h3>Ürün bilgisini iletin</h3><p>Marka/model, adet, durum ve varsa stok listesini ya da birkaç fotoğrafı paylaşın.</p></div></div>
                        <div class="step"><div class="num">3</div><div><h3>Toplu fiyat teklifi alın</h3><p>Partinin tamamı için tek fiyat; farklı durumdaki ürünler ayrı gruplarda değerlendirilir.</p></div></div>
                        <div class="step"><div class="num">4</div><div><h3>Teslim ve ödeme</h3><p>Anlaşma sonrası ürünler {{ $province->name }} içindeki adresinizden teslim alınır; ödeme şekli teklifle netleşir.</p></div></div>
                    </div>

                    @if ($brands->isNotEmpty())
                        <h2 class="h3 mb-3">{{ $province->name }} içinde sık aldığımız markalar</h2>
                        <div class="chips-standalone mb-5">
                            @foreach ($brands as $brand)
                                <a href="{{ $brand->url }}">{{ $brand->name }}</a>
                            @endforeach
                        </div>
                    @endif

                    @if ($province->activeDistricts->isNotEmpty())
                        <h2 class="h3 mb-3">{{ $province->name }} ilçeleri</h2>
                        <p class="text-muted">{{ $province->name }} içindeki tüm ilçelerden {{ $serviceName }} talebi alıyoruz:</p>
                        <div class="district-grid mb-5">
                            @foreach ($province->activeDistricts as $district)
                                <a href="{{ url('/'.$province->slug.'/'.$district->slug) }}"><i class="fa-solid fa-location-dot"></i>{{ $district->name }}</a>
                            @endforeach
                        </div>
                    @endif

                    <h2 class="h3 mb-3">{{ $province->name }} için sık sorulan sorular</h2>
                    <div class="mb-5">
                        @include('partials.faq-accordion', ['faqs' => $faqs, 'accordionId' => 'provinceServiceFaq', 'openFirst' => true])
                    </div>
                </div>
                <aside class="col-lg-4">
                    <div class="sidebar">
                        @include('partials.sidebar-cta', ['ctaTitle' => $title.' için fiyat alın', 'whatsappUrl' => $whatsappUrl])
                        <div class="sidebar-widget">
                            <h3>{{ $province->name }} içinde diğer alımlar</h3>
                            <ul class="widget-list">
                                <li><a class="service-list-item" href="{{ $province->url }}"><i class="fa-solid fa-location-dot"></i>{{ $province->name }} toplu elektronik alımı</a></li>
                                @foreach ($otherServices as $other)
                                    <li><a class="service-list-item" href="{{ $province->serviceUrl($other) }}"><i class="{{ $other->icon_class }}"></i>{{ $other->title }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="sidebar-widget">
                            <h3>Diğer şehirlerde</h3>
                            <div class="chips">
                                @foreach ($nearby as $other)
                                    <a href="{{ $other->serviceUrl($service) }}">{{ $other->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="section bg-mist">
        <div class="container" style="max-width:860px">
            <x-section-title subtitle="Teklif formu" :title="$title.' için teklif isteyin'" :center="true" class="mb-4" />
            @include('partials.quote-form', ['formId' => 'provinceServiceQuoteForm'])
        </div>
    </section>

    <div class="pt-5">@include('partials.cta-band', ['whatsappUrl' => $whatsappUrl])</div>
@endsection

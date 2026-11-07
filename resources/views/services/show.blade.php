@extends('layouts.app')

@section('content')
    @include('partials.page-banner', [
        'title' => $service->title,
        'subtitle' => $service->short_description,
        'breadcrumbs' => $breadcrumbs,
        'actions' => true,
        'whatsappUrl' => $whatsappUrl,
    ])

    <section class="section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="content-prose mb-5">{!! $service->description !!}</div>

                    @if (! empty($service->what_we_do))
                        <h2 class="h3 mb-3">Neleri alıyoruz?</h2>
                        <ul class="check-list two-col mb-5">
                            @foreach ($service->what_we_do as $item)
                                <li><i class="fa-solid fa-circle-check"></i><span class="text-dark">{{ $item }}</span></li>
                            @endforeach
                        </ul>
                    @endif

                    @if (! empty($service->suitable_for))
                        <h2 class="h3 mb-3">Kimlerden alıyoruz?</h2>
                        <ul class="check-list two-col mb-5">
                            @foreach ($service->suitable_for as $item)
                                <li><i class="fa-solid fa-building"></i><span class="text-dark">{{ $item }}</span></li>
                            @endforeach
                        </ul>
                    @endif

                    @if (! empty($service->process_steps))
                        <h2 class="h3 mb-3">Nasıl çalışıyoruz?</h2>
                        <div class="process-steps mb-5">
                            @foreach ($service->process_steps as $i => $step)
                                <div class="step">
                                    <div class="num">{{ $i + 1 }}</div>
                                    <div><h3>{{ $step['title'] ?? '' }}</h3><p>{{ $step['description'] ?? '' }}</p></div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($brands->isNotEmpty())
                        <h2 class="h3 mb-3">Sık aldığımız markalar</h2>
                        <div class="chips-standalone mb-5">
                            @foreach ($brands as $brand)
                                <a href="{{ $brand->url }}">{{ $brand->name }}</a>
                            @endforeach
                        </div>
                    @endif

                    @if ($guides->isNotEmpty())
                        <h2 class="h3 mb-3">İlgili rehberler</h2>
                        <div class="row g-3 mb-5">
                            @foreach ($guides as $guide)
                                <div class="col-md-4">
                                    <a class="service-list-item h-100" href="{{ $guide->url }}"><i class="fa-regular fa-file-lines"></i>{{ $guide->title }}</a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (! empty($service->faqs))
                        <h2 class="h3 mb-3">Sık sorulan sorular</h2>
                        <div class="mb-5">
                            @include('partials.faq-accordion', ['faqs' => $service->faqs, 'accordionId' => 'serviceFaq', 'openFirst' => true])
                        </div>
                    @endif

                    <h2 class="h3 mb-2">{{ $service->title }} yaptığımız iller</h2>
                    <p class="text-muted">Türkiye'nin 81 ilinde alım yapıyoruz. Büyük şehirler:</p>
                    <div class="city-grid mb-3">
                        @foreach ($featuredProvinces as $province)
                            <a class="city-card" href="{{ $province->serviceUrl($service) }}">
                                <i class="fa-solid fa-location-dot"></i><span>{{ $province->name }}</span>
                            </a>
                        @endforeach
                    </div>
                    <details class="mb-5">
                        <summary class="btn-link-arrow" style="cursor:pointer">Diğer iller</summary>
                        <div class="chips-standalone mt-3">
                            @foreach ($allProvinces as $province)
                                <a href="{{ $province->serviceUrl($service) }}">{{ $province->name }}</a>
                            @endforeach
                        </div>
                    </details>
                </div>

                <aside class="col-lg-4">
                    <div class="sidebar">
                        @include('partials.sidebar-cta', ['ctaTitle' => $service->title.' için fiyat alın', 'whatsappUrl' => $whatsappUrl])
                        <div class="sidebar-widget">
                            <h3>Diğer ürün ve durumlar</h3>
                            <ul class="widget-list">
                                @foreach ($otherServices as $other)
                                    <li><a class="service-list-item" href="{{ $other->url }}"><i class="{{ $other->icon_class }}"></i>{{ $other->title }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="section bg-mist pt-5">
        <div class="container" style="max-width:860px">
            <x-section-title subtitle="Teklif formu" :title="$service->title.' için teklif isteyin'" :center="true" class="mb-4" />
            @include('partials.quote-form', ['formId' => 'serviceQuoteForm'])
        </div>
    </section>

    <div class="pt-5">@include('partials.cta-band', ['whatsappUrl' => $whatsappUrl])</div>
@endsection

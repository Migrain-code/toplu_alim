@extends('layouts.app')

@section('content')
    @include('partials.page-banner', [
        'title' => $brand->heading,
        'subtitle' => $brand->description,
        'breadcrumbs' => $breadcrumbs,
        'actions' => true,
        'whatsappUrl' => $whatsappUrl,
    ])

    <section class="section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    @if (! empty($brand->products))
                        <h2 class="h3 mb-3">{{ $brand->name }} ürünlerinden neleri alıyoruz?</h2>
                        <ul class="check-list two-col mb-5">
                            @foreach ($brand->products as $item)
                                <li><i class="fa-solid fa-circle-check"></i><span class="text-dark">{{ $item }}</span></li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="content-prose mb-5">{!! $brand->content !!}</div>

                    @if (! empty($brand->faqs))
                        <h2 class="h3 mb-3">Sık sorulan sorular</h2>
                        <div class="mb-5">
                            @include('partials.faq-accordion', ['faqs' => $brand->faqs, 'accordionId' => 'brandFaq', 'openFirst' => true])
                        </div>
                    @endif

                    @isset($featuredProvinces)
                        <h2 class="h3 mb-3">{{ $brand->name }} toplu alımı yaptığımız iller</h2>
                        <div class="city-grid mb-4">
                            @foreach ($featuredProvinces as $province)
                                <a class="city-card" href="{{ $province->url }}"><i class="fa-solid fa-location-dot"></i><span>{{ $province->name }}</span></a>
                            @endforeach
                        </div>
                        <a class="btn-link-arrow mb-5 d-inline-flex" href="{{ route('regions.index') }}">81 ilin tamamı <i class="fa-solid fa-arrow-right"></i></a>
                    @endisset
                </div>

                <aside class="col-lg-4">
                    <div class="sidebar">
                        @include('partials.sidebar-cta', ['ctaTitle' => $brand->heading.' için fiyat alın', 'whatsappUrl' => $whatsappUrl])
                        @if (! empty($services) && count($services))
                            <div class="sidebar-widget">
                                <h3>İlgili ürün grupları</h3>
                                <ul class="widget-list">
                                    @foreach ($services as $service)
                                        <li><a class="service-list-item" href="{{ $service->url }}"><i class="{{ $service->icon_class }}"></i>{{ $service->title }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (! empty($otherBrands) && count($otherBrands))
                            <div class="sidebar-widget">
                                <h3>Diğer markalar</h3>
                                <div class="chips">
                                    @foreach ($otherBrands as $other)
                                        <a href="{{ $other->url }}">{{ $other->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @include('partials.cta-band', ['whatsappUrl' => $whatsappUrl])
@endsection

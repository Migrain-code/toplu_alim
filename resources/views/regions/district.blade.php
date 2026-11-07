@extends('layouts.app')

@section('content')
    @include('partials.page-banner', [
        'title' => $district->name.' Toplu Elektronik Alımı',
        'subtitle' => $district->description,
        'breadcrumbs' => $breadcrumbs,
        'actions' => true,
        'whatsappUrl' => $whatsappUrl,
    ])

    <section class="section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    @if (filled($district->content))
                        <div class="content-prose mb-5">{!! $district->content !!}</div>
                    @else
                        <div class="content-prose mb-5">
                            <p>{{ $district->full_name }} bölgesinde toplu telefon, tablet, bilgisayar, robot süpürge ve elektronik cihaz alımı yapıyoruz. Stok fazlası, teşhir, iade, ikinci el ya da arızalı ürünleriniz için model, adet ve durum bilgisini iletmeniz yeterli.</p>
                            <p>Fiyatı telefonda ya da WhatsApp üzerinden bildiriyor, anlaşma sonrası teslim almayı {{ $district->name }} adresinize göre planlıyoruz.</p>
                        </div>
                    @endif

                    <h2 class="h3 mb-3">{{ $district->name }} ilçesinde aldığımız ürünler</h2>
                    <div class="row g-3 mb-5">
                        @foreach ($categories as $service)
                            <div class="col-md-6">
                                <a class="service-list-item" href="{{ $province->serviceUrl($service) }}"><i class="{{ $service->icon_class }}"></i>{{ $service->title }}</a>
                            </div>
                        @endforeach
                    </div>

                    @if (! empty($district->neighborhoods))
                        <h2 class="h3 mb-3">{{ $district->name }} mahalleleri</h2>
                        <p class="text-muted">Aşağıdaki mahalleler başta olmak üzere {{ $district->name }} genelinden alım yapıyoruz.</p>
                        <div class="neighborhood-chips mb-5">
                            @foreach ($district->neighborhoods as $n)
                                <span><i class="fa-solid fa-location-dot"></i>{{ $n }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if (! empty($district->faqs))
                        <h2 class="h3 mb-3">{{ $district->name }} için sık sorulan sorular</h2>
                        <div class="mb-5">
                            @include('partials.faq-accordion', ['faqs' => $district->faqs, 'accordionId' => 'districtFaq', 'openFirst' => true])
                        </div>
                    @endif
                </div>
                <aside class="col-lg-4">
                    <div class="sidebar">
                        @include('partials.sidebar-cta', ['ctaTitle' => $district->name.' için fiyat alın', 'whatsappUrl' => $whatsappUrl])
                        <div class="sidebar-widget">
                            <h3>{{ $province->name }} ilindeki diğer ilçeler</h3>
                            <div class="chips">
                                <a href="{{ $province->url }}"><strong>{{ $province->name }} geneli</strong></a>
                                @foreach ($otherDistricts as $other)
                                    <a href="{{ url('/'.$province->slug.'/'.$other->slug) }}">{{ $other->name }}</a>
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
            <x-section-title subtitle="Teklif formu" :title="$district->name.' için teklif isteyin'" :center="true" class="mb-4" />
            @include('partials.quote-form', ['formId' => 'districtQuoteForm'])
        </div>
    </section>

    <div class="pt-5">@include('partials.cta-band', ['whatsappUrl' => $whatsappUrl, 'ctaTitle' => $district->name.' içinde toplu ürününüz mü var?'])</div>
@endsection

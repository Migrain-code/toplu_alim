@extends('layouts.app')

@section('content')
    @php
        // Hızlı seçim çipleri: kategori başlıkları uzun, hero'da kısa adla gösterilir.
        $shortNames = [
            'toplu-telefon-alimi' => 'Telefon', 'toplu-bilgisayar-alimi' => 'Bilgisayar & Laptop',
            'toplu-tablet-alimi' => 'Tablet', 'toplu-robot-supurge-alimi' => 'Robot Süpürge',
            'toplu-elektronik-cihaz-alimi' => 'TV & Konsol', 'kurumsal-elektronik-alimi' => 'Kurumsal IT',
        ];
    @endphp

    {{-- Hero: solda başlık + arama/çip + telefon, sağda cihaz kompozisyonu (fotoğraf yok, inline SVG). --}}
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 hero-copy">
                    @if (setting('hero_badge'))
                        <span class="hero-badge"><span class="dot"></span>{{ setting('hero_badge') }}</span>
                    @endif
                    <h1>{!! preg_replace('/(Elektronik)/iu', '<span>$1</span>', e(setting('hero_title', site_name()))) !!}</h1>
                    <p class="lead">{{ setting('hero_subtitle') }}</p>

                    <div class="hero-actions">
                        @if (site_phone())
                            <a class="hero-call" href="{{ phone_href() }}">
                                <span class="icon"><i class="fa-solid fa-phone"></i></span>
                                <span><small>Hemen arayın, fiyatı konuşalım</small><strong>{{ site_phone() }}</strong></span>
                            </a>
                        @endif
                        <a class="btn btn-whatsapp btn-lg" href="{{ whatsapp_url() }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i>WhatsApp</a>
                    </div>

                    <div class="hero-picks">
                        <span class="label">Ne satmak istiyorsunuz?</span>
                        <div class="picks">
                            @foreach ($categories as $service)
                                <a href="{{ $service->url }}"><i class="{{ $service->icon_class }}"></i>{{ $shortNames[$service->slug] ?? $service->title }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-visual" aria-hidden="true">
                        <div class="orb"></div>
                        <div class="ring"></div>
                        <x-device-art slug="bilgisayar" class="dv dv-laptop" />
                        <x-device-art slug="telefon" class="dv dv-phone" />
                        <x-device-art slug="tablet" class="dv dv-tablet" />
                        <x-device-art slug="supurge" class="dv dv-vacuum" />

                        <div class="float-card fc-1">
                            <span class="fc-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
                            <span><strong>Stok fazlası</strong><small>Sıfır · kapalı kutu</small></span>
                        </div>
                        <div class="float-card fc-2">
                            <span class="fc-icon amber"><i class="fa-solid fa-screwdriver-wrench"></i></span>
                            <span><strong>Arızalı & hurda</strong><small>Onlar da alınır</small></span>
                        </div>
                        <div class="float-card fc-3">
                            <span class="fc-icon green"><i class="fa-solid fa-location-dot"></i></span>
                            <span><strong>{{ $provinceCount }} il</strong><small>Yerinden teslim</small></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Güven şeridi --}}
    @if ($trustItems->isNotEmpty())
        <div class="trust-strip">
            <div class="container">
                <div class="trust-card">
                    @foreach ($trustItems as $item)
                        <div class="trust-item">
                            <div class="icon"><i class="{{ $item->icon_class }}"></i></div>
                            <div>
                                <h3>{{ $item->title }}</h3>
                                @if ($item->description)<p>{{ $item->description }}</p>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Ürün grupları --}}
    <section class="section" id="urunler">
        <div class="container">
            <x-section-title subtitle="Ne alıyoruz?" title="Toplu Alım Yaptığımız Ürünler"
                text="Sıfır, kapalı kutu, teşhir, ikinci el ya da arızalı: telefondan sunucuya kadar elektronik ürünleri adetli olarak alıyoruz." :center="true" />
            <div class="row g-4">
                @foreach ($categories as $i => $service)
                    <div class="col-lg-6 reveal">
                        @include('partials.cat-banner', ['service' => $service, 'tone' => $i])
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Ürün durumları --}}
    @if ($conditions->isNotEmpty())
        <section class="section conditions-band" id="durum">
            <div class="container">
                <x-section-title subtitle="Hangi durumda olursa olsun" title="Stok Fazlasından Hurdaya, Her Durumda Alım"
                    text="Ürünlerinizin durumu fiyatı belirler, alıp almayacağımızı değil." :center="true" :light="true" />
                <div class="row g-3 g-lg-4">
                    @foreach ($conditions as $service)
                        <div class="col-lg-4 col-md-6 reveal">
                            <div class="cond-card">
                                <x-device-art :slug="$service->slug" :title="$service->title" />
                                <div>
                                    <h3><a href="{{ $service->url }}">{{ $service->title }}</a></h3>
                                    <p>{{ $service->short_description }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Nasıl çalışıyoruz --}}
    <section class="section">
        <div class="container">
            <x-section-title :subtitle="setting('process_subtitle', 'Nasıl çalışıyoruz?')" :title="setting('process_title', 'Dört adımda satış')"
                text="Tek bir telefonla başlayın; listeyi alalım, fiyatı verelim, ürünleri yerinden teslim alalım." :center="true" />
            <div class="steps-row mb-5">
                @foreach ($processSteps as $i => $step)
                    <div class="step-card reveal">
                        <div class="num">{{ $i + 1 }}</div>
                        <i class="icon {{ $step->icon_class }}"></i>
                        <h3>{{ $step->title }}</h3>
                        <p>{{ $step->description }}</p>
                    </div>
                @endforeach
            </div>

            <div class="row g-4 align-items-stretch">
                <div class="col-lg-7">
                    <x-section-title :subtitle="setting('about_subtitle', 'Hakkımızda')" :title="setting('about_title')" class="mb-3" />
                    <div class="content-prose mb-4">{!! setting('about_text') !!}</div>
                    <div class="d-flex flex-wrap gap-3">
                        <a class="btn btn-outline-navy" href="{{ route('about') }}">Hakkımızda</a>
                        <a class="btn btn-navy" href="{{ route('quote.create') }}"><i class="fa-solid fa-tag"></i>Teklif Formu</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="why-panel h-100">
                        <h3>Neden bizi arıyorlar?</h3>
                        @if ($whyUs->isNotEmpty())
                            <ul class="check-list">
                                @foreach ($whyUs->take(5) as $item)
                                    <li><i class="fa-solid fa-circle-check"></i><div><strong>{{ $item->title }}</strong><span>{{ $item->description }}</span></div></li>
                                @endforeach
                            </ul>
                        @endif
                        @if (site_phone())
                            <a class="call-block" href="{{ phone_href() }}">
                                <span class="icon"><i class="fa-solid fa-phone"></i></span>
                                <span><small>Hemen arayın</small><strong>{{ site_phone() }}</strong></span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Sayaçlar --}}
    @if ($stats->isNotEmpty())
        @php $statIcons = ['fa-solid fa-map-location-dot', 'fa-solid fa-city', 'fa-solid fa-layer-group', 'fa-solid fa-tag']; @endphp
        <section class="stats-band">
            <div class="container">
                <div class="stats-card">
                    <div class="row g-3">
                        @foreach ($stats as $i => $stat)
                            <div class="col-6 col-md-3">
                                <div class="stat">
                                    <i class="{{ $statIcons[$i] ?? 'fa-solid fa-check' }}"></i>
                                    <div class="num" data-count="{{ $stat['value'] }}">{{ $stat['value'] }}</div>
                                    <div class="label">{{ $stat['label'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Markalar --}}
    @if ($brands->isNotEmpty())
        <section class="section bg-mist" id="markalar">
            <div class="container">
                <x-section-title subtitle="Markalar" title="Hangi Markaları Alıyoruz?"
                    text="En sık toplu alım yaptığımız markalar aşağıda. Listede olmayan bir marka için de arayın." :center="true" />
                <div class="brand-strip reveal">
                    @foreach ($brands as $brand)
                        <a href="{{ $brand->url }}">{{ $brand->name }}</a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Bölgeler --}}
    <section class="section" id="bolgeler">
        <div class="container">
            <x-section-title subtitle="Türkiye geneli" title="{{ $provinceCount }} İlde Toplu Elektronik Alımı"
                text="Büyük şehirlerde yerinde teslim alıyoruz; diğer illerden ürünleri anlaşmalı kargo veya nakliye ile alıyoruz." :center="true" />
            <div class="city-grid mb-4">
                @foreach ($featuredProvinces as $province)
                    @include('partials.region-card', ['province' => $province])
                @endforeach
            </div>
            <div class="text-center">
                <a class="btn btn-outline-navy" href="{{ route('regions.index') }}"><i class="fa-solid fa-map"></i>81 ilin tamamını gör</a>
            </div>
        </div>
    </section>

    {{-- Teklif formu + SSS --}}
    <section class="section bg-mist" id="teklif">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <x-section-title subtitle="Teklif al" title="Ürünlerinize Toplu Fiyat Alın" text="Formu doldurun, kısa sürede telefonla dönelim. Aceleniz varsa doğrudan arayın." class="mb-4" />
                    @include('partials.quote-form', ['formId' => 'homeQuoteForm'])
                </div>
                <div class="col-lg-6">
                    <x-section-title subtitle="Sık sorulan sorular" title="Merak Edilenler" class="mb-4" />
                    @include('partials.faq-accordion', ['faqs' => $faqs, 'accordionId' => 'homeFaq', 'openFirst' => true])
                    <a class="btn-link-arrow d-inline-flex mt-4" href="{{ route('faq') }}">Tüm sorular <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    {{-- Rehberler: ana sayfa sitenin en güçlü sayfası; yeni yazılar buradan link alıp hızlı keşfedilir. --}}
    @if ($posts->isNotEmpty())
        <section class="section">
            <div class="container">
                <x-section-title subtitle="Rehberler" title="Toplu Satış Öncesi Bilmeniz Gerekenler"
                    text="Ürünlerinizi hazırlamak, fiyatı doğru almak ve veriyi güvenle silmek için kısa rehberler." :center="true" />
                <div class="row g-4">
                    @foreach ($posts as $post)
                        <div class="col-lg-4 col-md-6 reveal">
                            @include('partials.post-card', ['post' => $post])
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-4">
                    <a class="btn btn-outline-navy" href="{{ route('blog.index') }}">Tüm rehberler <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </section>
    @endif

    @if ($testimonials->isNotEmpty())
        <section class="section">
            <div class="container">
                <x-section-title subtitle="Yorumlar" title="Bizimle Çalışanlar Ne Diyor?" :center="true" />
                <div class="row g-4">
                    @foreach ($testimonials->take(3) as $t)
                        <div class="col-lg-4">
                            <div class="sidebar-widget h-100">
                                <div class="text-warning mb-2">{{ str_repeat('★', $t->rating) }}</div>
                                <p>“{{ $t->comment }}”</p>
                                <strong class="text-dark">{{ $t->name }}</strong>
                                <small class="d-block text-muted">{{ collect([$t->location, $t->service?->title])->filter()->implode(' · ') }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <div class="pt-5">
        @include('partials.cta-band')
    </div>
@endsection

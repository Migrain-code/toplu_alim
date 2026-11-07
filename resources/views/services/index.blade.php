@extends('layouts.app')

@section('content')
    @include('partials.page-banner', [
        'title' => 'Toplu Alım Yaptığımız Ürünler',
        'subtitle' => 'Telefon, tablet, bilgisayar, robot süpürge ve her türlü elektronik cihazı adetli olarak alıyoruz; stok fazlasından arızalı ürüne kadar.',
        'breadcrumbs' => [['name' => 'Ana Sayfa', 'url' => url('/')], ['name' => 'Ne Alıyoruz?', 'url' => route('services.index')]],
        'actions' => true,
    ])

    <section class="section">
        <div class="container">
            <x-section-title subtitle="Ürün grupları" title="Hangi Ürünleri Alıyoruz?" :center="true" />
            <div class="row g-4">
                @foreach ($categories as $i => $service)
                    <div class="col-lg-6 reveal">
                        @include('partials.cat-banner', ['service' => $service, 'tone' => $i])
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if ($conditions->isNotEmpty())
        <section class="section conditions-band" id="durum">
            <div class="container">
                <x-section-title subtitle="Ürün durumu" title="Hangi Durumdaki Ürünleri Alıyoruz?" text="Sıfır stok fazlasından hurdaya kadar; durum fiyatı belirler." :center="true" :light="true" />
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
        <div class="pt-5"></div>
    @endif

    @include('partials.cta-band')
@endsection

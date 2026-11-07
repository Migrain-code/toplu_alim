@extends('layouts.app')

@section('content')
    @include('partials.page-banner', [
        'title' => 'Toplu Alım Yaptığımız Markalar',
        'subtitle' => 'Telefon, bilgisayar, tablet, robot süpürge ve oyun konsolu markalarında adetli alım. Listede olmayan marka için de arayın.',
        'breadcrumbs' => [['name' => 'Ana Sayfa', 'url' => url('/')], ['name' => 'Markalar', 'url' => route('brands.index')]],
        'actions' => true,
    ])

    <section class="section">
        <div class="container">
            <div class="row g-4">
                @foreach ($brands as $brand)
                    <div class="col-xl-3 col-md-6 reveal">
                        @include('partials.brand-card', ['brand' => $brand])
                    </div>
                @endforeach
            </div>

            <p class="text-muted small text-center mt-5 mb-0">
                Marka adları ve logoları ilgili sahiplerine aittir. {{ site_name() }} bu markaların yetkili satıcısı
                veya servisi değildir; ürünleri bağımsız bir alıcı olarak satın alır.
            </p>
        </div>
    </section>

    @include('partials.cta-band')
@endsection

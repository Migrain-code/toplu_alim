@extends('layouts.app')

@section('content')
    @include('partials.page-banner', [
        'title' => '81 İlde Toplu Elektronik Alımı',
        'subtitle' => 'İlinizi seçin: o ildeki ilçeleri, aldığımız ürünleri ve teslim alma seçeneklerini görün.',
        'breadcrumbs' => [['name' => 'Ana Sayfa', 'url' => url('/')], ['name' => 'Bölgeler', 'url' => route('regions.index')]],
        'actions' => true,
    ])

    <section class="section">
        <div class="container">
            <x-section-title subtitle="Büyük şehirler" title="En Çok Alım Yaptığımız İller" :center="true" />
            <div class="city-grid mb-5">
                @foreach ($featured as $province)
                    @include('partials.region-card', ['province' => $province])
                @endforeach
            </div>

            <x-section-title subtitle="A'dan Z'ye" title="Tüm İller" :center="true" class="mt-5" />
            <div class="letter-index">
                @foreach ($letters as $letter => $group)
                    <div class="letter-group">
                        <h3>{{ $letter }}</h3>
                        @foreach ($group as $province)
                            <a href="{{ $province->url }}">{{ $province->name }} <small>{{ $province->active_districts_count }} ilçe</small></a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.cta-band')
@endsection

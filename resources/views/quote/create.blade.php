@extends('layouts.app')

@section('content')
    @include('partials.page-banner', [
        'title' => 'Teklif Al',
        'subtitle' => 'Satmak istediğiniz ürünlerin türünü, adedini ve durumunu iletin; toplu fiyat teklifimizi kısa sürede bildirelim.',
        'breadcrumbs' => [['name' => 'Ana Sayfa', 'url' => url('/')], ['name' => 'Teklif Al', 'url' => route('quote.create')]],
    ])

    <section class="section bg-mist">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    @include('partials.quote-form', [
                        'formId' => 'quotePageForm',
                        'formTitle' => 'Toplu Alım Teklif Formu',
                        'formText' => 'Yalnız ad ve telefon zorunlu. Ürün, adet ve durum bilgisi ne kadar net olursa fiyat o kadar hızlı çıkar.',
                        'selectedService' => request('hizmet'),
                    ])
                </div>
                <div class="col-lg-4">
                    <div class="sidebar">
                        @include('partials.sidebar-cta', ['ctaTitle' => 'Form doldurmak istemiyor musunuz?', 'ctaText' => 'Arayın ya da WhatsApp\'tan ürün listesini gönderin.'])
                        <div class="sidebar-widget">
                            <h3>Fiyatı hızlandıran bilgiler</h3>
                            <ul class="check-list">
                                <li><i class="fa-solid fa-circle-check"></i><span>Marka, model ve kapasite (ör. 128 GB)</span></li>
                                <li><i class="fa-solid fa-circle-check"></i><span>Adet ve varsa stok listesi</span></li>
                                <li><i class="fa-solid fa-circle-check"></i><span>Durum: sıfır, teşhir, ikinci el, arızalı</span></li>
                                <li><i class="fa-solid fa-circle-check"></i><span>Kutu, aksesuar ve fatura durumu</span></li>
                                <li><i class="fa-solid fa-circle-check"></i><span>Kilit durumu (iCloud / FRP / BIOS)</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

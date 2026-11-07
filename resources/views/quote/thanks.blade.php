@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="container text-center" style="max-width:720px">
            <div class="thanks-icon"><i class="fa-solid fa-check"></i></div>
            <h1 class="h2 mb-3">Talebiniz bize ulaştı</h1>
            <p class="lead text-muted mb-4">Teşekkür ederiz. Ürün bilgilerinizi inceleyip en kısa sürede telefonla dönüş yapacağız. Acil durumlar için bizi doğrudan arayabilirsiniz.</p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                @if (site_phone())
                    <a class="btn btn-call btn-lg" href="{{ phone_href() }}"><i class="fa-solid fa-phone"></i>Hemen Ara</a>
                @endif
                <a class="btn btn-whatsapp btn-lg" href="{{ whatsapp_url() }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i>WhatsApp'tan Yazın</a>
                <a class="btn btn-outline-navy btn-lg" href="{{ route('home') }}">Ana Sayfaya Dön</a>
            </div>
        </div>
    </section>
@endsection

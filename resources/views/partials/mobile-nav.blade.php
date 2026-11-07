<div class="offcanvas offcanvas-end mobile-nav" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header">
        <span class="brand" id="mobileNavLabel"><x-logo /></span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
    </div>
    <div class="offcanvas-body">
        <div class="d-grid gap-2 mb-3">
            @if (site_phone())
                <a class="btn btn-call btn-lg" href="{{ phone_href() }}"><i class="fa-solid fa-phone"></i>Hemen Ara · {{ site_phone() }}</a>
            @endif
            <a class="btn btn-whatsapp" href="{{ whatsapp_url() }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i>WhatsApp'tan Teklif Al</a>
        </div>
        <ul class="mnav">
            <li><a class="mnav-link" href="{{ route('home') }}">Ana Sayfa</a></li>
            <li>
                <a class="mnav-link" data-bs-toggle="collapse" href="#mnavServices" role="button" aria-expanded="false" aria-controls="mnavServices">Ne Alıyoruz? <i class="fa-solid fa-chevron-down toggle-icon"></i></a>
                <ul class="collapse mnav-sub" id="mnavServices">
                    <li class="sub-title">Ürün grupları</li>
                    @foreach ($navCategories as $service)
                        <li><a href="{{ $service->url }}">{{ $service->title }}</a></li>
                    @endforeach
                    @if ($navConditions->isNotEmpty())
                        <li class="sub-title">Ürün durumu</li>
                        @foreach ($navConditions as $service)
                            <li><a href="{{ $service->url }}">{{ $service->title }}</a></li>
                        @endforeach
                    @endif
                </ul>
            </li>
            @if ($navBrands->isNotEmpty())
                <li>
                    <a class="mnav-link" data-bs-toggle="collapse" href="#mnavBrands" role="button" aria-expanded="false" aria-controls="mnavBrands">Markalar <i class="fa-solid fa-chevron-down toggle-icon"></i></a>
                    <ul class="collapse mnav-sub" id="mnavBrands">
                        @foreach ($navBrands as $brand)
                            <li><a href="{{ $brand->url }}">{{ $brand->name }}</a></li>
                        @endforeach
                        <li><a href="{{ route('brands.index') }}" class="fw-semibold">Tüm markalar</a></li>
                    </ul>
                </li>
            @endif
            <li>
                <a class="mnav-link" data-bs-toggle="collapse" href="#mnavRegions" role="button" aria-expanded="false" aria-controls="mnavRegions">Bölgeler <i class="fa-solid fa-chevron-down toggle-icon"></i></a>
                <ul class="collapse mnav-sub" id="mnavRegions">
                    @foreach ($navProvinces as $province)
                        <li><a href="{{ $province->url }}">{{ $province->name }}</a></li>
                    @endforeach
                    <li><a href="{{ route('regions.index') }}" class="fw-semibold">81 ilin tamamı</a></li>
                </ul>
            </li>
            <li><a class="mnav-link" href="{{ route('quote.create') }}">Teklif Al</a></li>
            <li><a class="mnav-link" href="{{ route('blog.index') }}">Blog</a></li>
            <li><a class="mnav-link" href="{{ route('about') }}">Hakkımızda</a></li>
            <li><a class="mnav-link" href="{{ route('faq') }}">Sık Sorulan Sorular</a></li>
            <li><a class="mnav-link" href="{{ route('contact') }}">İletişim</a></li>
        </ul>
    </div>
</div>

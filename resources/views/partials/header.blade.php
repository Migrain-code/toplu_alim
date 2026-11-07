@php
    $phone = site_phone();
    $hours = setting('working_hours');
@endphp
<div class="topbar d-none d-lg-block">
    <div class="container d-flex align-items-center">
        <ul class="topbar-list">
            <li><i class="fa-solid fa-location-dot"></i><span>{{ setting('service_area_text') ?: 'Türkiye geneli' }} · toplu alım</span></li>
            @if ($hours)
                <li><i class="fa-regular fa-clock"></i><span>{{ $hours }}</span></li>
            @endif
        </ul>
        <ul class="topbar-social ms-auto">
            @if ($phone)
                <li><a href="{{ phone_href() }}"><i class="fa-solid fa-phone me-1"></i>{{ $phone }}</a></li>
            @endif
            <li><a class="wa-link" href="{{ whatsapp_url() }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp'tan yazın</a></li>
        </ul>
    </div>
</div>

<header class="site-header" id="siteHeader">
    <div class="container">
        <nav class="navbar navbar-expand-lg p-0">
            <a class="brand" href="{{ route('home') }}" aria-label="{{ site_name() }} ana sayfa">
                <x-logo />
            </a>

            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="Menüyü aç">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="d-none d-lg-flex align-items-center flex-grow-1">
                <ul class="navbar-nav main-nav mx-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">Ne Alıyoruz?</a>
                        <ul class="dropdown-menu">
                            <li><span class="dropdown-header">Ürün grupları</span></li>
                            @foreach ($navCategories as $service)
                                <li><a class="dropdown-item" href="{{ $service->url }}"><i class="{{ $service->icon_class }}"></i>{{ $service->title }}</a></li>
                            @endforeach
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item fw-bold" href="{{ route('services.index') }}"><i class="fa-solid fa-grip"></i>Tümü</a></li>
                        </ul>
                    </li>
                    @if ($navConditions->isNotEmpty())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="{{ route('services.index') }}#durum">Ürün Durumu</a>
                            <ul class="dropdown-menu">
                                <li><span class="dropdown-header">Hangi durumda olursa olsun</span></li>
                                @foreach ($navConditions as $service)
                                    <li><a class="dropdown-item" href="{{ $service->url }}"><i class="{{ $service->icon_class }}"></i>{{ $service->title }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                    @if ($navBrands->isNotEmpty())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('brands.*') ? 'active' : '' }}" href="{{ route('brands.index') }}">Markalar</a>
                            <div class="dropdown-menu dropdown-mega" style="min-width:420px">
                                <div class="mega-grid">
                                    @foreach ($navBrands as $brand)
                                        <a class="dropdown-item" href="{{ $brand->url }}">{{ $brand->name }}</a>
                                    @endforeach
                                </div>
                                <hr class="dropdown-divider">
                                <a class="dropdown-item fw-bold" href="{{ route('brands.index') }}"><i class="fa-solid fa-grip"></i>Tüm markalar</a>
                            </div>
                        </li>
                    @endif
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('regions.*') ? 'active' : '' }}" href="{{ route('regions.index') }}">Bölgeler</a>
                        <div class="dropdown-menu dropdown-mega">
                            <span class="dropdown-header">Büyük şehirler</span>
                            <div class="mega-grid">
                                @foreach ($navProvinces as $province)
                                    <a class="dropdown-item" href="{{ $province->url }}"><i class="fa-solid fa-location-dot"></i>{{ $province->name }}</a>
                                @endforeach
                            </div>
                            <hr class="dropdown-divider">
                            <a class="dropdown-item fw-bold" href="{{ route('regions.index') }}"><i class="fa-solid fa-map"></i>81 ilin tamamı</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.index') }}">Blog</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">İletişim</a></li>
                </ul>
                <div class="header-cta d-flex align-items-center gap-3">
                    @if ($phone)
                        <a class="header-phone d-none d-xxl-inline-flex" href="{{ phone_href() }}">
                            <span class="icon"><i class="fa-solid fa-phone"></i></span>
                            <span><small>Hemen arayın</small>{{ $phone }}</span>
                        </a>
                        <a class="btn btn-call d-xxl-none" href="{{ phone_href() }}"><i class="fa-solid fa-phone"></i>Hemen Ara</a>
                    @endif
                    <a class="btn btn-navy" href="{{ route('quote.create') }}"><i class="fa-solid fa-tag"></i>Teklif Al</a>
                </div>
            </div>
        </nav>
    </div>
</header>

@include('partials.mobile-nav')

@php
    $phone = site_phone();
    $email = setting('email');
    $socials = array_filter([
        'facebook' => setting('facebook_url'),
        'instagram' => setting('instagram_url'),
        'youtube' => setting('youtube_url'),
    ]);
@endphp
<footer class="footer">
    <div class="footer-widgets">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-4 col-md-6">
                    <a class="d-inline-block mb-3" href="{{ route('home') }}"><x-logo :light="true" /></a>
                    <p>{{ setting('footer_text') }}</p>
                    @if ($email)
                        <p class="mt-2 mb-1"><i class="fa-regular fa-envelope me-2"></i><!--email_off--><a href="mailto:{{ $email }}">{{ $email }}</a><!--/email_off--></p>
                    @endif
                    @if (setting('working_hours'))
                        <p class="mb-0"><i class="fa-regular fa-clock me-2"></i>{{ setting('working_hours') }}</p>
                    @endif
                    @if ($socials)
                        <div class="footer-social">
                            @foreach ($socials as $network => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"><i class="fa-brands fa-{{ $network }}"></i></a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="col-lg-3 col-md-6">
                    <h3>Ne alıyoruz?</h3>
                    <ul>
                        @foreach ($navCategories as $service)
                            <li><a href="{{ $service->url }}">{{ $service->title }}</a></li>
                        @endforeach
                        @foreach ($navConditions->take(3) as $service)
                            <li><a href="{{ $service->url }}">{{ $service->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h3>Bölgeler</h3>
                    <ul>
                        @foreach ($navProvinces->take(8) as $province)
                            <li><a href="{{ $province->url }}">{{ $province->name }} toplu elektronik alımı</a></li>
                        @endforeach
                        <li><a href="{{ route('regions.index') }}">81 ilin tamamı →</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h3>Kurumsal</h3>
                    <ul>
                        <li><a href="{{ route('about') }}">Hakkımızda</a></li>
                        <li><a href="{{ route('brands.index') }}">Markalar</a></li>
                        <li><a href="{{ route('blog.index') }}">Blog</a></li>
                        <li><a href="{{ route('faq') }}">S.S.S.</a></li>
                        <li><a href="{{ route('quote.create') }}">Teklif Al</a></li>
                        <li><a href="{{ route('contact') }}">İletişim</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} {{ site_name() }}. Tüm hakları saklıdır. Marka adları sahiplerine aittir.</span>
            <ul>
                @foreach ($footerPages as $page)
                    <li><a href="{{ $page->url }}">{{ $page->title }}</a></li>
                @endforeach
                <li><a href="{{ route('sitemap') }}">Site Haritası</a></li>
            </ul>
        </div>
    </div>
</footer>

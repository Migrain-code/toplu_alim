<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $pageTitle = seo_title($metaTitle ?? null);
        $pageDescription = $metaDescription ?? setting('meta_description');
        $pageCanonical = $canonical ?? url()->current();
        $pageImage = $ogImage ?? asset('images/brand/og-image.png');
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $pageDescription), 300, '') }}">
    <meta name="robots" content="{{ $robots ?? 'index, follow' }}">
    <link rel="canonical" href="{{ $pageCanonical }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ site_name() }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $pageDescription), 300, '') }}">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:locale" content="tr_TR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#0d1b3e">
    @if (setting('google_site_verification'))
        <meta name="google-site-verification" content="{{ setting('google_site_verification') }}">
    @endif
    <link rel="icon" href="{{ asset('images/brand/logo-mark.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="alternate" type="application/rss+xml" title="{{ site_name() }} Blog" href="{{ route('blog.feed') }}">
    {{-- Yazı tipleri app.scss içinde, kendi sunucumuzdan: Google Fonts bağlantısı yok. --}}
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    {{--
        İkonlar (Font Awesome) sayfanın ilk çizimini BEKLETMEZ: dosya "print" olarak iner
        (tarayıcı bunu beklemeden sayfayı çizer), yüklenince tüm ekranlara uygulanır.
    --}}
    <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/scss/icons.scss') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/scss/icons.scss') }}"></noscript>
    @foreach (($jsonLd ?? []) as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endforeach
    @if ($gaId = setting('google_analytics_id'))
        {{--
            Google Analytics ERTELENİR. gtag.js 167 KB ve açılışta ~230 ms işlemci harcıyordu
            (PageSpeed: TBT). Komutlar hemen kuyruğa yazılır; betik ziyaretçinin ilk
            etkileşiminde (kaydırma, dokunma, tuş) ya da sayfa yüklendikten 3,5 sn sonra iner
            ve kuyruktaki sayfa görüntülemeyi gönderir. Hiç etkileşmeden 3,5 sn içinde çıkan
            ziyaretçi sayılmayabilir; bu bilinçli bir ödünleşimdir.
        --}}
        <script>
            window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', @json($gaId));
            (function () {
                var loaded = false;
                function load() {
                    if (loaded) return; loaded = true;
                    var s = document.createElement('script'); s.async = true;
                    s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(@json($gaId));
                    document.head.appendChild(s);
                }
                ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach(function (e) { window.addEventListener(e, load, { once: true, passive: true }); });
                window.addEventListener('load', function () { setTimeout(load, 3500); });
            })();
        </script>
    @endif
    {{--
        E-postalar <!--email_off--> ile sarılır: Cloudflare aksi hâlde onları gizleyip
        sayfaya kritik yolda duran ek bir betik (email-decode.min.js) ekliyordu. Adres
        JSON-LD'de zaten düz metin olduğu için gizleme bir koruma da sağlamıyordu.
    --}}
    @stack('head')
</head>
<body class="{{ $bodyClass ?? '' }}">
    {{-- Marka işareti: logo ve favicon aynı çizimi kullanır (public/images/brand/logo-mark.svg). --}}
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <defs>
            <linearGradient id="brandGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#2b59ff"/><stop offset="1" stop-color="#5b3bff"/></linearGradient>
        </defs>
        <symbol id="brand-mark" viewBox="0 0 40 40">
            <rect width="40" height="40" rx="11" fill="url(#brandGrad)"/>
            <rect x="9" y="10" width="13" height="21" rx="3" fill="none" stroke="#fff" stroke-width="2.4" transform="rotate(-10 15.5 20.5)"/>
            <rect x="18" y="9" width="13" height="22" rx="3" fill="#fff"/>
            <path d="M21.5 23l3 3 5-6" fill="none" stroke="#2b59ff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="31" cy="31" r="5" fill="#ffb81c"/>
        </symbol>
    </svg>
    @include('partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.floating-cta')
    @stack('scripts')
</body>
</html>

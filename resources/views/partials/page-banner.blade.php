<section class="page-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $crumb)
                    @if ($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">{{ $crumb['name'] }}</li>
                    @else
                        <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a></li>
                    @endif
                @endforeach
            </ol>
        </nav>
        <h1>{{ $title }}</h1>
        @if (! empty($subtitle))
            <p class="lead mb-0">{{ $subtitle }}</p>
        @endif
        @if (! empty($actions))
            <div class="banner-actions">
                @if (site_phone())
                    <a class="btn btn-call" href="{{ phone_href() }}"><i class="fa-solid fa-phone"></i>Hemen Ara</a>
                @endif
                <a class="btn btn-outline-white" href="{{ $whatsappUrl ?? whatsapp_url() }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i>WhatsApp'tan Yazın</a>
            </div>
        @endif
    </div>
</section>

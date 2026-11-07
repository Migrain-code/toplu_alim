<article class="service-card {{ $compact ?? false ? 'compact' : '' }}">
    <div class="media">
        @if ($service->image)
            <a class="thumb" href="{{ $service->url }}" tabindex="-1" aria-hidden="true">
                <img src="{{ $service->image_url }}" alt="{{ $service->image_alt ?: $service->title }}" loading="lazy" width="600" height="338">
            </a>
        @else
            <a class="art" href="{{ $service->url }}" tabindex="-1" aria-hidden="true"><x-device-art :slug="$service->slug" :title="$service->title" /></a>
        @endif
        <span class="icon-badge"><i class="{{ $service->icon_class }}"></i></span>
    </div>
    <div class="body">
        <h3><a href="{{ $service->url }}">{{ $service->title }}</a></h3>
        <p>{{ $service->short_description }}</p>
        <a class="btn-link-arrow" href="{{ $service->url }}">Detaylar <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</article>

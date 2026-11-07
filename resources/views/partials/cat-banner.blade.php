{{-- Büyük kategori bannerı: başlık + kısa metin + neleri aldığımız + illüstrasyon. --}}
<article class="cat-banner tone-{{ ($tone ?? 0) % 6 + 1 }}">
    <div>
        <h3><a href="{{ $url ?? $service->url }}">{{ $heading ?? $service->title }}</a></h3>
        <p>{{ $service->short_description }}</p>
        @if (! empty($service->what_we_do))
            <div class="chips">
                @foreach (array_slice($service->what_we_do, 0, 3) as $item)
                    <span>{{ \Illuminate\Support\Str::limit($item, 34) }}</span>
                @endforeach
            </div>
        @endif
        <span class="btn-link-arrow">Fiyat teklifi al <i class="fa-solid fa-arrow-right"></i></span>
    </div>
    <x-device-art :slug="$service->slug" :title="$service->title" />
</article>

<article class="service-card compact h-100">
    <div class="media">
        @if ($post->image)
            <a class="thumb" href="{{ $post->url }}" tabindex="-1" aria-hidden="true">
                <img src="{{ $post->image_url }}" alt="{{ $post->image_alt ?: $post->title }}" loading="lazy" width="600" height="338">
            </a>
        @else
            {{-- Görselsiz yazı: slug'daki ürün kelimesine göre illüstrasyon (telefon, laptop, kutu…). --}}
            <a class="art" href="{{ $post->url }}" tabindex="-1" aria-hidden="true"><x-device-art :slug="$post->slug" :title="$post->title" /></a>
        @endif
    </div>
    <div class="body">
        @if ($post->category)
            <span class="post-cat">{{ $post->category->name }}</span>
        @endif
        <h3><a href="{{ $post->url }}">{{ $post->title }}</a></h3>
        <p>{{ \Illuminate\Support\Str::limit($post->summary, 140) }}</p>
        <div class="d-flex align-items-center justify-content-between mt-auto">
            <a class="btn-link-arrow" href="{{ $post->url }}">Okuyun <i class="fa-solid fa-arrow-right"></i></a>
            <small class="text-muted"><i class="fa-regular fa-clock me-1"></i>{{ $post->reading_minutes }} dk</small>
        </div>
    </div>
</article>

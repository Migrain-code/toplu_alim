@extends('layouts.app')

@section('content')
    @include('partials.page-banner', [
        'title' => $activeCategory?->name ?? 'Blog',
        'subtitle' => $activeCategory?->description ?? 'Toplu elektronik satışı, stok fazlası, veri silme ve fiyatlandırma hakkında pratik bilgiler.',
        'breadcrumbs' => array_filter([
            ['name' => 'Ana Sayfa', 'url' => url('/')],
            ['name' => 'Blog', 'url' => route('blog.index')],
            $activeCategory ? ['name' => $activeCategory->name, 'url' => $activeCategory->url] : null,
        ]),
    ])

    <section class="section">
        <div class="container">
            @if ($categories->count() > 1)
                <div class="filter-bar">
                    <a class="filter-btn {{ $activeCategory ? '' : 'active' }}" href="{{ route('blog.index') }}">Tümü</a>
                    @foreach ($categories as $category)
                        <a class="filter-btn {{ $activeCategory?->is($category) ? 'active' : '' }}" href="{{ $category->url }}">
                            {{ $category->name }} ({{ $category->published_posts_count }})
                        </a>
                    @endforeach
                </div>
            @endif

            @if ($posts->isEmpty())
                <p class="text-center text-muted py-5">Bu bölümde henüz yazı yok.</p>
            @else
                <div class="row g-4">
                    @foreach ($posts as $post)
                        <div class="col-lg-4 col-md-6 reveal">
                            @include('partials.post-card', ['post' => $post])
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $posts->links('partials.pagination') }}
                </div>
            @endif
        </div>
    </section>

    @include('partials.cta-band')
@endsection

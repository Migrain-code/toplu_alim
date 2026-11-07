@props(['slug' => '', 'title' => null])
{{--
    Ürün grubu illüstrasyonu (inline SVG, fotoğraf yok).
    Hangi çizimin basılacağı hizmet slug'ındaki anahtar kelimeden çözülür; panelden yeni
    bir hizmet eklenirse en yakın çizim ya da genel "cihazlar" çizimi kullanılır.
    Renkler CSS değişkenlerinden gelir: --art-ink (çizgi), --art-fill (yüzey), --art-accent.
--}}
@php
    $s = (string) $slug;
    $kind = match (true) {
        str_contains($s, 'telefon') => 'phone',
        str_contains($s, 'tablet') => 'tablet',
        str_contains($s, 'bilgisayar'), str_contains($s, 'laptop') => 'laptop',
        str_contains($s, 'supurge') => 'vacuum',
        str_contains($s, 'kurumsal') => 'server',
        str_contains($s, 'stok'), str_contains($s, 'depo') => 'boxes',
        str_contains($s, 'tasfiye') => 'tag',
        str_contains($s, 'arizali'), str_contains($s, 'hurda') => 'broken',
        str_contains($s, 'teshir'), str_contains($s, 'iade') => 'return',
        str_contains($s, 'ikinci-el') => 'cycle',
        default => 'devices',
    };
@endphp
<svg {{ $attributes->class(['device-art', 'device-art--'.$kind]) }} viewBox="0 0 200 160" fill="none" stroke-linecap="round" stroke-linejoin="round" role="img" aria-label="{{ $title ?? 'Ürün illüstrasyonu' }}">
    @switch($kind)
        @case('phone')
            <rect x="46" y="22" width="56" height="112" rx="12" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4" transform="rotate(-8 74 78)"/>
            <rect x="96" y="30" width="56" height="112" rx="12" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <rect x="104" y="40" width="40" height="80" rx="5" fill="var(--art-accent)" opacity=".9"/>
            <line x1="118" y1="131" x2="130" y2="131" stroke="var(--art-ink)" stroke-width="4"/>
            <circle cx="66" cy="44" r="4" fill="var(--art-ink)" transform="rotate(-8 74 78)"/>
            <path d="M112 64h24M112 76h16M112 88h20" stroke="#fff" stroke-width="4" opacity=".85"/>
            @break
        @case('tablet')
            <rect x="34" y="26" width="132" height="100" rx="12" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <rect x="44" y="36" width="112" height="80" rx="5" fill="var(--art-accent)" opacity=".9"/>
            <circle cx="160" cy="76" r="2.5" fill="var(--art-ink)"/>
            <path d="M58 98l22-26 16 16 12-12 26 22" stroke="#fff" stroke-width="4" opacity=".9"/>
            <circle cx="124" cy="56" r="7" fill="#fff" opacity=".9"/>
            <rect x="60" y="132" width="80" height="8" rx="4" fill="var(--art-ink)" opacity=".15"/>
            @break
        @case('laptop')
            <rect x="44" y="28" width="112" height="76" rx="8" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <rect x="54" y="38" width="92" height="56" rx="4" fill="var(--art-accent)" opacity=".9"/>
            <path d="M28 112h144l-10 16H38z" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <line x1="88" y1="120" x2="112" y2="120" stroke="var(--art-ink)" stroke-width="4"/>
            <path d="M66 54h40M66 66h56M66 78h30" stroke="#fff" stroke-width="4" opacity=".85"/>
            @break
        @case('vacuum')
            <ellipse cx="100" cy="112" rx="70" ry="14" fill="var(--art-ink)" opacity=".12"/>
            <circle cx="100" cy="82" r="56" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <circle cx="100" cy="82" r="38" stroke="var(--art-ink)" stroke-width="3" opacity=".35"/>
            <rect x="84" y="40" width="32" height="14" rx="7" fill="var(--art-accent)"/>
            <circle cx="100" cy="82" r="10" fill="var(--art-accent)"/>
            <path d="M60 120l-12 10M140 120l12 10" stroke="var(--art-ink)" stroke-width="4" opacity=".5"/>
            <path d="M150 56c8 6 12 14 12 26M50 56c-8 6-12 14-12 26" stroke="var(--art-accent)" stroke-width="4" opacity=".8"/>
            @break
        @case('server')
            <rect x="56" y="18" width="88" height="34" rx="6" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <rect x="56" y="62" width="88" height="34" rx="6" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <rect x="56" y="106" width="88" height="34" rx="6" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            @foreach ([35, 79, 123] as $y)
                <circle cx="72" cy="{{ $y }}" r="5" fill="var(--art-accent)"/>
                <path d="M88 {{ $y }}h40" stroke="var(--art-ink)" stroke-width="4" opacity=".35"/>
            @endforeach
            @break
        @case('boxes')
            <rect x="28" y="78" width="66" height="58" rx="6" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <rect x="104" y="78" width="66" height="58" rx="6" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <rect x="66" y="22" width="66" height="56" rx="6" fill="var(--art-accent)" stroke="var(--art-ink)" stroke-width="4"/>
            <path d="M52 78v18M128 78v18M90 22v16" stroke="var(--art-ink)" stroke-width="4"/>
            <path d="M80 54h38" stroke="#fff" stroke-width="4" opacity=".85"/>
            @break
        @case('tag')
            <path d="M40 74l50-50h52a8 8 0 018 8v52l-50 50a8 8 0 01-11 0L40 85a8 8 0 010-11z" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <circle cx="126" cy="48" r="9" fill="var(--art-accent)" stroke="var(--art-ink)" stroke-width="4"/>
            <path d="M76 96l32-32M82 70h0M104 92h0" stroke="var(--art-accent)" stroke-width="7"/>
            @break
        @case('broken')
            <rect x="70" y="20" width="60" height="118" rx="12" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <rect x="78" y="30" width="44" height="88" rx="5" fill="var(--art-accent)" opacity=".85"/>
            <path d="M92 30l8 22-12 14 18 16-6 20 10 16" stroke="#fff" stroke-width="4"/>
            <path d="M40 60l14 6M38 86h14M150 54l-12 8M156 84h-14" stroke="var(--art-ink)" stroke-width="4" opacity=".4"/>
            @break
        @case('return')
            <path d="M48 64l52-24 52 24v52l-52 24-52-24z" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <path d="M48 64l52 24 52-24M100 88v52" stroke="var(--art-ink)" stroke-width="4"/>
            <path d="M74 30a40 40 0 0160 8" stroke="var(--art-accent)" stroke-width="6"/>
            <path d="M136 22l-2 18-17-4" stroke="var(--art-accent)" stroke-width="6"/>
            @break
        @case('cycle')
            <path d="M58 92a44 44 0 0174-46" stroke="var(--art-accent)" stroke-width="8"/>
            <path d="M142 68a44 44 0 01-74 46" stroke="var(--art-ink)" stroke-width="8" opacity=".75"/>
            <path d="M134 30l0 20-20-2" stroke="var(--art-accent)" stroke-width="8"/>
            <path d="M66 130l0-20 20 2" stroke="var(--art-ink)" stroke-width="8" opacity=".75"/>
            <rect x="86" y="62" width="28" height="40" rx="6" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            @break
        @default
            <rect x="22" y="30" width="100" height="66" rx="8" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <rect x="30" y="38" width="84" height="50" rx="4" fill="var(--art-accent)" opacity=".9"/>
            <path d="M58 96v14M86 96v14M46 112h52" stroke="var(--art-ink)" stroke-width="4"/>
            <rect x="120" y="70" width="62" height="40" rx="18" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <path d="M138 84v12M132 90h12" stroke="var(--art-ink)" stroke-width="4"/>
            <circle cx="164" cy="86" r="3.5" fill="var(--art-accent)"/>
            <circle cx="170" cy="94" r="3.5" fill="var(--art-ink)"/>
            <circle cx="150" cy="40" r="16" fill="var(--art-fill)" stroke="var(--art-ink)" stroke-width="4"/>
            <circle cx="150" cy="40" r="6" fill="var(--art-accent)"/>
    @endswitch
</svg>

@props(['light' => false])
{{--
    Marka işareti HTML + CSS ile çizilir: <img> içindeki SVG metni sitenin yazı tipini
    kullanamadığı için cihazdan cihaza farklı görünüyordu. Panelden logo yüklenmişse o
    görsel kullanılır.
--}}
@if (setting('logo'))
    <img src="{{ media_url(setting('logo')) }}" alt="{{ site_name() }}" width="240" height="64" {{ $attributes->class(['logo-img']) }}>
@else
    <span {{ $attributes->class(['logo', 'logo--light' => $light]) }}>
        <span class="logo-mark" aria-hidden="true">
            <svg viewBox="0 0 40 40" width="40" height="40"><use href="#brand-mark"/></svg>
        </span>
        <span class="logo-text">
            <strong>Toptan Elektronik</strong>
            <em>Alımı</em>
        </span>
    </span>
@endif

<div class="cta-card">
    <h3>{{ $ctaTitle ?? 'Hemen fiyat alın' }}</h3>
    <p>{{ $ctaText ?? 'Ürün türünü, adedini ve durumunu söyleyin; kısa sürede toplu fiyat teklifi verelim.' }}</p>
    @if (site_phone())
        <a class="btn btn-call btn-lg" href="{{ phone_href() }}"><i class="fa-solid fa-phone"></i>Hemen Ara</a>
    @endif
    <a class="btn btn-whatsapp" href="{{ $whatsappUrl ?? whatsapp_url() }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i>WhatsApp'tan Yazın</a>
    @if (site_phone())
        <a class="cta-phone" href="{{ phone_href() }}">
            <span class="icon"><i class="fa-solid fa-phone"></i></span>
            <span><small>Telefon</small><strong>{{ site_phone() }}</strong></span>
        </a>
    @endif
</div>

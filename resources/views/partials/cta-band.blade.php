<section class="cta-band">
    <div class="container">
        <div class="cta-inner">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h2>{{ $ctaTitle ?? setting('cta_title') }}</h2>
                    <p class="mb-0">{{ $ctaText ?? setting('cta_text') }}</p>
                </div>
                <div class="col-lg-5">
                    <div class="cta-actions">
                        @if (site_phone())
                            <a class="call-block" href="{{ phone_href() }}">
                                <span class="icon"><i class="fa-solid fa-phone"></i></span>
                                <span><small>Hemen arayın</small><strong>{{ site_phone() }}</strong></span>
                            </a>
                        @endif
                        <a class="btn btn-whatsapp btn-lg" href="{{ $whatsappUrl ?? whatsapp_url() }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i>WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

import Collapse from 'bootstrap/js/dist/collapse';
import Offcanvas from 'bootstrap/js/dist/offcanvas';
import Carousel from 'bootstrap/js/dist/carousel';

// Bootstrap bileşenlerini data-* API'siyle etkinleştir (import edilmeleri yeterli)
window.bootstrap = { Collapse, Offcanvas, Carousel };

document.addEventListener('DOMContentLoaded', () => {
    stickyHeader();
    revealOnScroll();
    animateCounters();
    quoteForm();
    recaptcha();
    backToTop();
});

/*
 * Sayfa belli bir noktanın altına kaydırıldı mı?
 *
 * window.scrollY OKUNMAZ: Chrome bunu okurken düzeni yeniden hesaplamak zorunda kalıyor
 * (PageSpeed "zorunlu yeniden düzenleme"). Onun yerine sayfanın başından "offset" piksel
 * aşağıya görünmez bir işaret konur; tarayıcı işaretin ekrandan çıktığını kendisi bildirir.
 * Kaydırma dinleyicisi de gerekmez.
 */
function onScrolledPast(offset, callback) {
    if (!('IntersectionObserver' in window)) return;

    const marker = document.createElement('div');
    marker.setAttribute('aria-hidden', 'true');
    marker.style.cssText = `position:absolute;top:${offset}px;left:0;width:1px;height:1px;pointer-events:none;`;
    document.body.prepend(marker);

    new IntersectionObserver(([entry]) => {
        // İşaret ekranın ÜSTÜNDEN çıktıysa kaydırılmış demektir (altından değil).
        callback(!entry.isIntersecting && entry.boundingClientRect.top < 0);
    }).observe(marker);
}

/* Sticky header gölgesi */
function stickyHeader() {
    const header = document.getElementById('siteHeader');
    if (!header) return;
    onScrolledPast(10, (past) => header.classList.toggle('is-sticky', past));
}

function revealOnScroll() {
    const items = document.querySelectorAll('.reveal');
    if (!items.length || !('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('in'));
        return;
    }
    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) {
                e.target.classList.add('in');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });
    items.forEach((el) => io.observe(el));
}

/* Sayaç animasyonu (yalnızca sayısal değerler) */
function animateCounters() {
    const nums = document.querySelectorAll('[data-count]');
    if (!nums.length) return;
    const run = (el) => {
        const raw = el.dataset.count.trim();
        const match = raw.match(/^(\d+)(.*)$/);
        if (!match) { el.textContent = raw; return; }
        const target = parseInt(match[1], 10);
        const suffix = match[2] || '';
        const duration = 1200;
        const start = performance.now();
        const tick = (now) => {
            const p = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(target * eased) + suffix;
            if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    };
    if (!('IntersectionObserver' in window)) { nums.forEach(run); return; }
    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => { if (e.isIntersecting) { run(e.target); io.unobserve(e.target); } });
    }, { threshold: 0.5 });
    nums.forEach((el) => io.observe(el));
}

/* Teklif formu: fotoğraf önizleme (fotoğraf isteğe bağlı) */
function quoteForm() {
    document.querySelectorAll('form[data-quote-form]').forEach((form) => {
        const input = form.querySelector('input[type="file"][name="photos[]"]');
        const previews = form.querySelector('[data-photo-previews]');
        const drop = form.querySelector('.photo-drop');
        if (input && previews) {
            const render = () => {
                previews.innerHTML = '';
                const files = Array.from(input.files || []).slice(0, 5);
                files.forEach((file) => {
                    if (!file.type.startsWith('image/')) return;
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.onload = () => URL.revokeObjectURL(img.src);
                    previews.appendChild(img);
                });
                const label = form.querySelector('[data-photo-count]');
                if (label) label.textContent = files.length ? `${files.length} fotoğraf seçildi` : '';

                // Fotoğraf seçildiğinde alan yeşile döner, varsa hata durumu kalkar.
                if (drop) {
                    drop.classList.toggle('is-filled', files.length > 0);
                    if (files.length > 0) drop.classList.remove('is-invalid');
                }
            };
            input.addEventListener('change', render);
            if (drop) {
                ['dragenter', 'dragover'].forEach((ev) => drop.addEventListener(ev, (e) => { e.preventDefault(); drop.classList.add('dragover'); }));
                ['dragleave', 'drop'].forEach((ev) => drop.addEventListener(ev, () => drop.classList.remove('dragover')));
            }
        }
    });
}

/*
 * reCAPTCHA — İSTEĞE BAĞLI yükleme.
 *
 * Google betiği sayfa açılışında yüklenmez (~700 KB, 1 sn'den fazla işlemci). Ziyaretçi
 * forma dokununca iner; form doldurulurken hazır olur.
 *
 * v3: jeton GÖNDERİM ANINDA alınır. Jeton iki dakikada geçersizleşir; sayfa açılışında
 *     alınsaydı fotoğraf seçip formu dolduran kullanıcının jetonu çoktan ölmüş olurdu.
 * v2: kutucuk görünür olmalı; form ekrana yaklaşınca yüklenir.
 *
 * Betik yüklenemezse form OLDUĞU GİBİ gönderilir; kullanıcı üçüncü taraf bir betiğe
 * rehin kalmaz. Sunucu kendi politikasına göre karar verir.
 */
function recaptcha() {
    const cfg = window.__recaptcha;
    if (!cfg || !cfg.siteKey) return;

    // Eski sayfa şablonları "src" göndermiyordu. Yalnız derlenmiş dosyalar yüklenip
    // şablonlar eski kaldığında form jetonsuz gidip reddediliyordu; adres anahtardan üretilir.
    const version = cfg.version || 'v3';
    const src = cfg.src || (version === 'v2'
        ? 'https://www.google.com/recaptcha/api.js'
        : `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(cfg.siteKey)}`);

    const forms = document.querySelectorAll('form[data-recaptcha]');
    if (!forms.length) return;

    let loading = null;
    const load = () => loading || (loading = new Promise((resolve, reject) => {
        // Betik sayfada zaten yüklüyse (eski şablon) ikinci kez yükleme.
        if (window.grecaptcha && window.grecaptcha.execute) {
            resolve();
            return;
        }
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    }));
    const warmUp = () => load().catch(() => {});

    forms.forEach((form) => {
        ['focusin', 'pointerdown', 'touchstart'].forEach((ev) => form.addEventListener(ev, warmUp, { once: true, passive: true }));

        if (version === 'v2') {
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    if (entries.some((entry) => entry.isIntersecting)) {
                        observer.disconnect();
                        warmUp();
                    }
                }, { rootMargin: '300px' });
                observer.observe(form);
            } else {
                warmUp();
            }
            return;
        }

        const field = form.querySelector('input[name="g-recaptcha-response"]');
        if (!field) return;

        let tokenReady = false;

        form.addEventListener('submit', (event) => {
            if (tokenReady) return;                       // ikinci tur: gerçekten gönder
            event.preventDefault();

            const send = () => { tokenReady = true; form.requestSubmit(); };

            load()
                .then(() => new Promise((resolve) => window.grecaptcha.ready(resolve)))
                .then(() => window.grecaptcha.execute(cfg.siteKey, { action: cfg.action }))
                .then((token) => { field.value = token; })
                .catch(() => {})
                .finally(send);
        });
    });
}

/* Yukarı çık */
function backToTop() {
    const btn = document.querySelector('.back-to-top');
    if (!btn) return;
    onScrolledPast(500, (past) => btn.classList.toggle('show', past));
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

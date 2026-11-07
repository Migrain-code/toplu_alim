<?php

namespace App\Support;

/**
 * AI istemlerinin ortak iş bağlamı.
 *
 * Eskiden her üretici (blog, bölge metni, konu, meta) aynı "kim olduğumuz" paragrafını
 * kendi içinde tekrar ediyordu; iş değişince beş dosyanın beşini de bulmak gerekiyordu.
 * Tek kaynak burası.
 */
final class BusinessContext
{
    /** Sistem isteminin ilk cümlesi: yazarın kim adına yazdığı. */
    public static function intro(): string
    {
        return 'Sen Türkiye genelinde (81 il) toptan ve toplu elektronik alımı yapan bir firmanın';
    }

    /** Ne alıyoruz, kimden alıyoruz, hangi durumda alıyoruz. */
    public static function domainLines(): array
    {
        return [
            'İŞ: Firma elektronik ürünleri TOPLU olarak SATIN ALIR (satış yapmaz, tamir yapmaz).',
            'ÜRÜNLER: cep telefonu, tablet, bilgisayar ve laptop, robot süpürge ve dikey/şarjlı süpürge,',
            'televizyon, monitör, oyun konsolu, akıllı saat, kulaklık, yazıcı, network ekipmanı, POS cihazı, sunucu.',
            'KİMDEN: bayi, ithalatçı, distribütör, e-ticaret satıcısı, mağaza, şirket (demirbaş/IT yenileme), kurum.',
            'DURUMLAR: sıfır stok fazlası, depo boşaltma, tasfiye/mağaza kapanışı, teşhir, müşteri iadesi,',
            'kapalı kutu, ikinci el, arızalı ve hurda ürün; lot, palet ve parti mal.',
            'FİYAT NEYE BAĞLI: marka/model, adet, durum (sıfır/teşhir/ikinci el/arızalı), kutu ve aksesuar,',
            'kilit durumu (iCloud/FRP/BIOS), garanti. Kesin fiyat, ürün listesi görülmeden VERİLMEZ.',
            'KANAL: telefonla arama birinci, WhatsApp ikinci, teklif formu üçüncü iletişim yolu.',
        ];
    }

    /** Uydurma yasağı: ödeme süresi, adres, rakam gibi doğrulanamayan iş iddiaları. */
    public static function factRules(): array
    {
        return [
            '- ASLA uydurma: rakam, müşteri adı, yorum, ödül, "20 yıllık tecrübe", depo adresi, şube,',
            '  "aynı gün ödeme" gibi taahhüt, kesin fiyat veya yüzde KULLANMA.',
            '- Ödeme şekli ve teslim alma ayrıntısı için "teklifle birlikte netleşir" de; somut söz verme.',
        ];
    }
}

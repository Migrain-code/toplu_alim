<?php

namespace App\Enums;

/**
 * Panel rolleri.
 *
 * Tek bir "role" alanı kullanılır; dört rol için ayrı bir yetki paketi kurmak
 * gereksiz karmaşıklık olurdu. Yetkiler App\Policies altındaki ilkelerde tanımlıdır.
 */
enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case MusteriTemsilcisi = 'musteri_temsilcisi';
    case Personel = 'personel';
    // Değer eski adıyla kalır: veritabanında saklanıyor, yalnız etiket değişti.
    case SahaPersoneli = 'montajci';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Süper Yönetici',
            self::MusteriTemsilcisi => 'Müşteri Temsilcisi',
            self::Personel => 'Personel',
            self::SahaPersoneli => 'Saha Personeli',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Her şeye erişir: içerik, bölgeler, SEO, kullanıcılar ve ayarlar.',
            self::MusteriTemsilcisi => 'Tüm teklif taleplerini görür, saha personeline atar, durum takibi yapar.',
            self::Personel => 'İçerik, blog ve bölgeleri yönetir. Talepleri görür ama atayamaz.',
            self::SahaPersoneli => 'Yalnız kendisine atanan alım taleplerini görür ve durumunu günceller.',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SuperAdmin => 'danger',
            self::MusteriTemsilcisi => 'info',
            self::Personel => 'warning',
            self::SahaPersoneli => 'success',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $r) => [$r->value => $r->label()])->all();
    }
}

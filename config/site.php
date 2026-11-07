<?php

/*
|--------------------------------------------------------------------------
| Site varsayılan ayarları
|--------------------------------------------------------------------------
| Admin panelindeki "Site Ayarları" sayfasında bir değer girilmemişse
| buradaki (.env üzerinden gelen) değerler kullanılır. Kod içinde sabit
| telefon numarası bulunmaz.
*/

return [
    'phone' => env('SITE_PHONE', ''),
    'whatsapp' => env('SITE_WHATSAPP', ''),
    'email' => env('SITE_EMAIL', ''),
    'notification_email' => env('SITE_NOTIFICATION_EMAIL'),

    /*
     * Menüde, ana sayfada ve hizmet sayfalarında öne çıkan iller (bu sırayla).
     * 81 ilin tamamı /bolgeler sayfasında listelenir; menüye hepsini koymak
     * hem kullanılmaz hem her sayfayı ağırlaştırır.
     */
    'featured_provinces' => [
        'istanbul', 'ankara', 'izmir', 'bursa', 'antalya',
        'kocaeli', 'konya', 'adana', 'gaziantep', 'mersin',
    ],
];

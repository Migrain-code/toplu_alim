<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Toplu alımını yaptığımız markalar (içerik: data/brand_content.php).
 *
 * İÇERİK KURALI: metinler MARKA HAKKINDA iddia içermez; yalnız o markanın hangi
 * ürünlerini hangi durumda aldığımızı anlatır. Her metnin sonuna marka adlarının
 * sahiplerine ait olduğu ve bağımsız alıcı olduğumuz notu eklenir.
 *
 * service_id BİLEREK boş: Brand::hasOwnPage() service_id dolu markaya ayrı sayfa
 * açmaz (kartı hizmet sayfasına yönlendirir). Burada her markanın kendi sayfası olmalı.
 *
 * Tekrar çalıştırılabilir: var olan markanın panelden düzenlenmiş alanlarına dokunmaz.
 */
class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = require __DIR__.'/data/brand_content.php';

        // Markanın ana ürün grubu: hizmet ve il × hizmet sayfalarında "sık aldığımız
        // markalar" listesini besler. service_id'ye YAZILMAZ (o kolon markanın kendi
        // sayfasını kapatır; bkz. Brand::hasOwnPage).
        $categoryOf = [
            'apple' => 'toplu-telefon-alimi', 'samsung' => 'toplu-telefon-alimi', 'xiaomi' => 'toplu-telefon-alimi',
            'huawei' => 'toplu-telefon-alimi', 'oppo' => 'toplu-telefon-alimi',
            'lenovo' => 'toplu-bilgisayar-alimi', 'hp' => 'toplu-bilgisayar-alimi', 'dell' => 'toplu-bilgisayar-alimi',
            'asus' => 'toplu-bilgisayar-alimi', 'acer' => 'toplu-bilgisayar-alimi',
            'roborock' => 'toplu-robot-supurge-alimi', 'dreame' => 'toplu-robot-supurge-alimi', 'viomi' => 'toplu-robot-supurge-alimi',
            'irobot' => 'toplu-robot-supurge-alimi', 'dyson' => 'toplu-robot-supurge-alimi',
            'playstation' => 'toplu-elektronik-cihaz-alimi', 'xbox' => 'toplu-elektronik-cihaz-alimi',
            'nintendo' => 'toplu-elektronik-cihaz-alimi', 'lg' => 'toplu-elektronik-cihaz-alimi', 'philips' => 'toplu-elektronik-cihaz-alimi',
        ];
        $serviceIds = Service::query()->pluck('id', 'slug');

        foreach ($brands as $i => $data) {
            $brand = Brand::query()->firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'content' => $data['content'].$this->disclaimer($data['name']),
                    'products' => $data['products'],
                    'faqs' => $data['faqs'],
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                    'is_featured' => $data['is_featured'],
                    'is_active' => true,
                    'sort_order' => $i + 1,
                    'service_id' => null,
                ]
            );

            // Kategori bağı sonradan da kurulabilsin (hizmetler markalardan sonra
            // eklenmiş olabilir); panelden seçilmiş bir değerin üzerine yazılmaz.
            $categoryId = $serviceIds[$categoryOf[$data['slug']] ?? ''] ?? null;

            if ($categoryId && ! $brand->category_service_id) {
                $brand->forceFill(['category_service_id' => $categoryId])->save();
            }
        }
    }

    private function disclaimer(string $name): string
    {
        return "\n<p><small>".$name.' ve ilgili ürün adları sahiplerinin tescilli markalarıdır. '
            .'Bu markaların yetkili satıcısı veya servisi değiliz; ürünleri bağımsız bir alıcı olarak satın alıyoruz.</small></p>';
    }
}

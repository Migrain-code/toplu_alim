<?php

namespace App\Services\Seo;

use App\Models\SeoKeyword;
use App\Models\SeoTarget;
use App\Models\Service;
use App\Support\PathNormalizer;
use App\Support\TurkishText;

/**
 * Ticari kelimeleri ilgili HİZMET sayfasına atar — DETERMİNİSTİK (spec §3.1).
 *
 * Neden gerekli: sahipsiz bir ticari kelime blog üretim hattının havuzuna düşer ve
 * blog, para kazandıran hizmet sayfasıyla aynı kelimeyi hedefleyerek onu yer
 * (cannibalization). Ticari kelimelerin sahibi hizmet sayfası olmalıdır.
 *
 * Eşleştirme UYDURMAZ: kelimenin içindeki somut terime bakar, eşleşme yoksa
 * kelimeye DOKUNMAZ (sahipsiz bırakır).
 */
class CommercialKeywordAssigner
{
    /**
     * Terim → hizmet slug'ı. Sıra önemli: İLK eşleşen kazanır.
     *
     * Ürün kategorisi, durumdan önce denenir: "arızalı telefon alanlar" telefon
     * sayfasının kelimesidir (kategori sayfaları bu kelimeleri metinlerinde taşır).
     * Durum sayfaları ürün adı geçmeyen genel kelimeleri alır ("hurda elektronik alımı").
     */
    private const TERM_MAP = [
        // IT altyapısı: ürün adından önce, çünkü "sunucu" başka sayfaya düşmez.
        'sunucu' => 'kurumsal-elektronik-alimi',
        'server' => 'kurumsal-elektronik-alimi',
        'iş istasyonu' => 'kurumsal-elektronik-alimi',
        'it ekipman' => 'kurumsal-elektronik-alimi',

        // Ürün kategorileri
        'robot süpürge' => 'toplu-robot-supurge-alimi',
        'süpürge' => 'toplu-robot-supurge-alimi',
        'roborock' => 'toplu-robot-supurge-alimi',
        'dreame' => 'toplu-robot-supurge-alimi',
        'viomi' => 'toplu-robot-supurge-alimi',
        'irobot' => 'toplu-robot-supurge-alimi',
        'dyson' => 'toplu-robot-supurge-alimi',
        'tablet' => 'toplu-tablet-alimi',
        'ipad' => 'toplu-tablet-alimi',
        'bilgisayar' => 'toplu-bilgisayar-alimi',
        'laptop' => 'toplu-bilgisayar-alimi',
        'notebook' => 'toplu-bilgisayar-alimi',
        'telefon' => 'toplu-telefon-alimi',
        'iphone' => 'toplu-telefon-alimi',

        // Kurumsal, ürün adı geçmeyen
        'kurumsal' => 'kurumsal-elektronik-alimi',
        'demirbaş' => 'kurumsal-elektronik-alimi',
        'şirket' => 'kurumsal-elektronik-alimi',
        'ofis' => 'kurumsal-elektronik-alimi',

        // Ürün durumu / alım şekli
        'depo boşaltma' => 'elektronik-depo-bosaltma',
        'stok fazlası' => 'stok-fazlasi-elektronik-alimi',
        'tasfiye' => 'tasfiye-elektronik-alimi',
        'kapanış' => 'tasfiye-elektronik-alimi',
        'hurda' => 'arizali-hurda-elektronik-alimi',
        'arızalı' => 'arizali-hurda-elektronik-alimi',
        'çıkma' => 'arizali-hurda-elektronik-alimi',
        'teşhir' => 'teshir-iade-elektronik-alimi',
        'iade' => 'teshir-iade-elektronik-alimi',
        'kapalı kutu' => 'teshir-iade-elektronik-alimi',
        'outlet' => 'teshir-iade-elektronik-alimi',
        'ikinci el' => 'ikinci-el-elektronik-toplu-alimi',
        'palet' => 'ikinci-el-elektronik-toplu-alimi',
        'parti mal' => 'ikinci-el-elektronik-toplu-alimi',
        // Yalın "lot" yazılmaz: str_contains "pilot", "slot" kelimelerini de yakalardı.
        'lot alım' => 'ikinci-el-elektronik-toplu-alimi',
        'lot halinde' => 'ikinci-el-elektronik-toplu-alimi',

        // Diğer elektronik cihazlar
        'televizyon' => 'toplu-elektronik-cihaz-alimi',
        'monitör' => 'toplu-elektronik-cihaz-alimi',
        'konsol' => 'toplu-elektronik-cihaz-alimi',
        'playstation' => 'toplu-elektronik-cihaz-alimi',
        'xbox' => 'toplu-elektronik-cihaz-alimi',
        'nintendo' => 'toplu-elektronik-cihaz-alimi',
        'akıllı saat' => 'toplu-elektronik-cihaz-alimi',
        'kulaklık' => 'toplu-elektronik-cihaz-alimi',
        'hoparlör' => 'toplu-elektronik-cihaz-alimi',
        'kamera' => 'toplu-elektronik-cihaz-alimi',
        'fotoğraf makinesi' => 'toplu-elektronik-cihaz-alimi',
        'yazıcı' => 'toplu-elektronik-cihaz-alimi',
        'fotokopi' => 'toplu-elektronik-cihaz-alimi',
        'modem' => 'toplu-elektronik-cihaz-alimi',
        'network' => 'toplu-elektronik-cihaz-alimi',
        'pos cihaz' => 'toplu-elektronik-cihaz-alimi',
        'barkod' => 'toplu-elektronik-cihaz-alimi',
        'projeksiyon' => 'toplu-elektronik-cihaz-alimi',
        'elektronik cihaz' => 'toplu-elektronik-cihaz-alimi',
        'elektronik eşya' => 'toplu-elektronik-cihaz-alimi',
        'elektronik parça' => 'toplu-elektronik-cihaz-alimi',
        'elektronik kart' => 'toplu-elektronik-cihaz-alimi',
    ];

    /** @return array{assigned: int, skipped: int, details: array<int, string>} */
    public function assign(): array
    {
        $services = Service::query()->where('is_active', true)->get()->keyBy('slug');
        $assigned = 0;
        $skipped = 0;
        $details = [];

        $candidates = SeoKeyword::query()
            ->active()
            ->unassigned()
            ->whereIn('keyword_type', ['COMMERCIAL_PRIMARY', 'COMMERCIAL_VARIANT'])
            ->get();

        foreach ($candidates as $keyword) {
            $slug = $this->matchService($keyword->keyword);

            if (! $slug || ! $services->has($slug)) {
                $skipped++;

                continue;
            }

            $target = SeoTarget::query()
                ->where('url_hash', PathNormalizer::hash('/'.$slug))
                ->where('status', true)
                ->first();

            if (! $target) {
                $skipped++;

                continue;
            }

            $keyword->update(['target_id' => $target->getKey()]);
            $assigned++;
            $details[] = $keyword->keyword.' → '.$target->name;
        }

        return ['assigned' => $assigned, 'skipped' => $skipped, 'details' => $details];
    }

    private function matchService(string $keyword): ?string
    {
        $needle = TurkishText::lower($keyword);

        foreach (self::TERM_MAP as $term => $slug) {
            if (str_contains($needle, TurkishText::lower($term))) {
                return $slug;
            }
        }

        return null;
    }
}

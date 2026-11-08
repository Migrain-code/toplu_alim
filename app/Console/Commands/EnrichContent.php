<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Province;
use App\Models\SeoAnalysis;
use App\Models\Service;
use App\Services\Ai\AiClient;
use App\Services\Ai\RegionContentEnricher;
use App\Services\Seo\ContentRegistry;
use App\Services\Seo\ContentScorer;
use App\Services\Seo\PageRenderer;
use App\Support\AutomationLog;
use App\Support\SeoConfig;
use App\Support\SeoIssue;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Throwable;

/**
 * Hedef skorun altındaki YAYINDAKİ sayfaların içeriğini AI ile genişletir.
 *
 * Kapalı sayfalara dokunmaz: 404 dönen bir sayfayı zenginleştirmek boşa AI çağrısıdır.
 *
 * Bot SEO YALNIZ İLLERDE çalışır: ilçeler otomatik metin almaz (elle yazılabilir).
 * 81 ilin metinsiz olanlarını (noindex) doldurmak için elle toplu çalıştırılır:
 *   php artisan seo:enrich --thin --limit=81
 * Sıra: öne çıkan iller (config/site.featured_provinces) önce.
 */
class EnrichContent extends Command
{
    protected $signature = 'seo:enrich
                            {--type=* : province|brand|service (boş bırakılırsa hepsi)}
                            {--limit=50 : En fazla kaç sayfa}
                            {--force : Hedef skorun üstündekileri de işle}
                            {--thin : Yalnız henüz dizine açılmamış (ince metinli) il sayfaları}
                            {--short : 600 kelimenin altındaki sayfaları, metne dokunmadan yeni bölümlerle uzat}';

    protected $description = 'Yayındaki sayfaların içeriğini AI ile genişletir ve yeniden skorlar';

    public function handle(
        AiClient $ai,
        RegionContentEnricher $enricher,
        ContentRegistry $registry,
        ContentScorer $scorer,
        PageRenderer $renderer,
    ): int {
        if (! $ai->isConfigured()) {
            AutomationLog::summary('seo.enrich', ['configured' => false], reasons: ['not_configured']);
            $this->warn('AI yapılandırılmamış. .env dosyasına AI_API_KEY girin.');

            return self::SUCCESS;
        }

        $target = SeoConfig::int('score_target', 80, 1, 100);
        $short = (bool) $this->option('short');
        $candidates = ($short
            ? $this->shortCandidates((array) $this->option('type'))
            : $this->candidates($registry, $target, (array) $this->option('type'), (bool) $this->option('force'), (bool) $this->option('thin')))
            ->take((int) $this->option('limit'));

        if ($candidates->isEmpty()) {
            AutomationLog::summary('seo.enrich', ['processed' => 0], reasons: ['no_candidates']);
            $this->info('Zenginleştirilecek sayfa yok — yayındaki tüm içerik hedef skorun üstünde.');

            return self::SUCCESS;
        }

        $this->info($candidates->count().' sayfa işlenecek. Her sayfa bir AI çağrısı demektir.');
        $this->newLine();

        $done = 0;
        $errors = [];
        $renderer->prepare();

        foreach ($candidates as $model) {
            $label = $this->label($model);

            try {
                $result = $short ? $enricher->lengthen($model) : $enricher->enrich($model);
            } catch (Throwable $e) {
                $errors[] = $label.': '.$e->getMessage();
                $this->line("  <fg=red>HATA</> {$label} — ".$e->getMessage());

                continue;
            }

            // Hemen yeniden skorla ki sonucu görelim.
            $content = $registry->forModel($model->refresh());
            $content->renderedHtml = $renderer->mainHtml($content->url);
            $analysis = $scorer->persist($content);

            $this->line(sprintf(
                '  <fg=green>TAMAM</> %-28s %d → %d kelime · %d H2 · skor %d/100',
                $label, $result['words_before'], $result['words_after'], $result['h2'], $analysis->score,
            ));

            $done++;
        }

        AutomationLog::summary('seo.enrich', [
            'processed' => $done,
            'failed' => count($errors),
        ], errors: $errors, changed: $done > 0);

        $this->newLine();
        $this->info("{$done} sayfa zenginleştirildi.".(count($errors) ? ' '.count($errors).' hata.' : ''));

        return self::SUCCESS;
    }

    /**
     * Son skorlamada "600 kelimeden az" uyarısı alan il/marka/hizmet sayfaları — skordan
     * BAĞIMSIZ. En kısa olanlar önce. Metni hiç olmayan (150 kelime altı) sayfalar
     * --thin işidir, burada atlanır.
     *
     * @return Collection<int, Model>
     */
    private function shortCandidates(array $types): Collection
    {
        $types = array_values(array_intersect($types ?: ['province', 'brand', 'service'], ['province', 'brand', 'service'])) ?: ['province', 'brand', 'service'];

        return SeoAnalysis::query()
            ->whereIn('content_type', $types)
            ->get()
            ->filter(fn (SeoAnalysis $a) => array_intersect([SeoIssue::CONTENT_THIN, SeoIssue::CONTENT_TOO_SHORT], (array) $a->issues) !== [])
            ->map(fn (SeoAnalysis $a) => match ($a->content_type) {
                'province' => Province::query()->where('is_active', true)->find($a->content_id),
                'brand' => Brand::query()->where('is_active', true)->whereNull('service_id')->find($a->content_id),
                'service' => Service::query()->where('is_active', true)->find($a->content_id),
                default => null,
            })
            ->filter()
            ->map(fn (Model $m) => [$m, str_word_count(strip_tags((string) ($m instanceof Service ? $m->description : $m->content)), 0, 'çğıöşüÇĞİÖŞÜ')])
            ->filter(fn (array $row) => $row[1] >= 150)
            ->sortBy(fn (array $row) => $row[1])
            ->map(fn (array $row) => $row[0])
            ->values();
    }

    /** @return Collection<int, Model> */
    private function candidates(ContentRegistry $registry, int $target, array $types, bool $force, bool $thin = false): Collection
    {
        // İlçeler kapsam dışı: --type=district verilse de yok sayılır.
        $types = array_values(array_intersect($types ?: ['province', 'brand', 'service'], ['province', 'brand', 'service'])) ?: ['province', 'brand', 'service'];

        if ($thin) {
            // İnce metin = henüz indekslenmeyen il sayfası; skordan bağımsız olarak işlenir.
            $types = ['province'];
            $force = true;
        }

        $featured = array_flip((array) config('site.featured_provinces', []));
        $typeOrder = ['province' => 0, 'brand' => 1, 'service' => 2];

        return $registry->reachable()
            ->filter(fn ($c) => in_array($c->contentType, $types, true))
            ->filter(function ($c) use ($target, $force) {
                if ($force) {
                    return true;
                }

                $score = SeoAnalysis::query()
                    ->where('content_type', $c->contentType)
                    ->where('content_id', $c->contentId)
                    ->value('score');

                return $score === null || $score < $target;
            })
            ->map(fn ($c) => match ($c->contentType) {
                'province' => Province::find($c->contentId),
                'brand' => Brand::query()->whereNull('service_id')->find($c->contentId),
                'service' => Service::find($c->contentId),
                default => null,
            })
            ->filter()
            ->when($thin, fn (Collection $c) => $c->filter(fn ($m) => method_exists($m, 'isIndexable') && ! $m->isIndexable()))
            ->sortBy(function ($m) use ($featured, $typeOrder) {
                $type = match (true) {
                    $m instanceof Province => 'province',
                    $m instanceof Brand => 'brand',
                    default => 'service',
                };
                $provinceSlug = $m->slug ?? null;
                $rank = $featured[$provinceSlug] ?? 999;

                return sprintf('%d-%03d-%08d', $typeOrder[$type], $rank, $m->getKey());
            })
            ->values();
    }

    private function label(Model $model): string
    {
        return match (true) {
            $model instanceof Province => $model->name.' (il)',
            $model instanceof Brand => $model->name.' (marka)',
            $model instanceof Service => $model->title,
            default => class_basename($model),
        };
    }
}

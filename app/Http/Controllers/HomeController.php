<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\Province;
use App\Models\Service;
use App\Models\Testimonial;
use App\Support\SchemaOrg;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $stats = collect(range(1, 4))
            ->map(fn (int $i) => ['value' => setting("stat_{$i}_value"), 'label' => setting("stat_{$i}_label")])
            ->filter(fn (array $s) => filled($s['value']) && filled($s['label']))
            ->values();

        $faqs = Faq::query()->active()->where('show_on_home', true)->ordered()->take(6)->get();

        $jsonLd = [SchemaOrg::localBusiness()];

        if ($faqSchema = SchemaOrg::faq($faqs->map(fn ($f) => ['question' => $f->question, 'answer' => $f->answer]))) {
            $jsonLd[] = $faqSchema;
        }

        return view('home', [
            'categories' => Service::query()->active()->categories()->ordered()->get(),
            'conditions' => Service::query()->active()->conditions()->ordered()->get(),
            'brands' => Brand::query()->active()->ordered()->get(['id', 'name', 'slug', 'service_id']),
            'trustItems' => Feature::query()->active()->ofType(Feature::TYPE_TRUST)->ordered()->get(),
            'whyUs' => Feature::query()->active()->ofType(Feature::TYPE_WHY_US)->ordered()->get(),
            'processSteps' => Feature::query()->active()->ofType(Feature::TYPE_PROCESS)->ordered()->get(),
            'posts' => Blog::query()->published()->with('category')->orderByDesc('publish_at')->limit(3)->get(),
            'testimonials' => Testimonial::query()->active()->ordered()->take(9)->get(),
            'featuredProvinces' => Province::query()->active()->featured()->get(['id', 'name', 'slug']),
            'provinceCount' => Province::query()->active()->count(),
            'faqs' => $faqs,
            'stats' => $stats,
            'metaTitle' => setting('meta_title', site_name().' | '.setting('site_tagline')),
            'metaDescription' => setting('meta_description'),
            'canonical' => url('/'),
            'jsonLd' => $jsonLd,
        ]);
    }
}

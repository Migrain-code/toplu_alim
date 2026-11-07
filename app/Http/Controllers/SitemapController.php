<?php

namespace App\Http\Controllers;

use App\Support\SitemapUrls;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), fn () => view('sitemap', ['urls' => SitemapUrls::all()])->render());

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}

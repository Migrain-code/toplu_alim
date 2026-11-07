<?php

namespace App\Http\Controllers;

use App\Support\SchemaOrg;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact', [
            'metaTitle' => 'İletişim | Telefon ve WhatsApp ile Teklif Alın',
            'metaDescription' => 'Toplu telefon, tablet, bilgisayar ve elektronik satışı için bize telefon, WhatsApp veya form üzerinden ulaşın. '.setting('service_area_text').' genelinde alım yapıyoruz.',
            'canonical' => route('contact'),
            'jsonLd' => [
                SchemaOrg::localBusiness(),
                SchemaOrg::breadcrumbs([
                    ['name' => 'Ana Sayfa', 'url' => url('/')],
                    ['name' => 'İletişim', 'url' => route('contact')],
                ]),
            ],
        ]);
    }
}

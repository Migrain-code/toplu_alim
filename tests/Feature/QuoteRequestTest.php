<?php

namespace Tests\Feature;

use App\Mail\QuoteRequestReceived;
use App\Models\District;
use App\Models\Province;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuoteRequestTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function validPayload(array $overrides = []): array
    {
        $province = Province::where('slug', 'istanbul')->first();

        return array_merge([
            'photos' => [UploadedFile::fake()->image('telefonlar.jpg', 800, 600)],
            'name' => 'Test Müşteri',
            'phone' => '0532 111 22 33',
            'province_id' => $province->id,
            'district_id' => District::where('province_id', $province->id)->where('slug', 'kadikoy')->value('id'),
            'service_id' => Service::where('slug', 'toplu-telefon-alimi')->value('id'),
            'product_type' => 'iPhone 13 128 GB',
            'quantity' => '40 adet',
            'condition' => 'ikinci_el',
            'message' => 'Kutulu, iCloud kapalı.',
            'preferred_date' => now()->addDays(2)->toDateString(),
            'kvkk' => '1',
        ], $overrides);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post('/teklif-al', [])
            ->assertSessionHasErrors(['name', 'phone', 'kvkk'])
            ->assertSessionDoesntHaveErrors(['photos', 'product_type', 'quantity', 'condition']);

        $this->assertDatabaseCount('quote_requests', 0);
    }

    public function test_photos_are_optional(): void
    {
        Storage::fake('local');

        // Toplu alımda fiyatı çoğu zaman model/adet listesi belirler; fotoğraf istenmez.
        $this->post('/teklif-al', ['name' => 'Ali', 'phone' => '05321112233', 'kvkk' => '1'])
            ->assertRedirect(route('quote.thanks'));

        $this->assertSame([], QuoteRequest::firstOrFail()->photos);
    }

    public function test_product_details_are_stored(): void
    {
        Storage::fake('local');

        $this->post('/teklif-al', $this->validPayload())->assertRedirect(route('quote.thanks'));

        $quote = QuoteRequest::firstOrFail();
        $this->assertSame('iPhone 13 128 GB', $quote->product_type);
        $this->assertSame('40 adet', $quote->quantity);
        $this->assertSame('ikinci_el', $quote->condition);
        $this->assertSame('İkinci el, çalışır', $quote->condition_label);
    }

    public function test_unknown_condition_is_rejected(): void
    {
        $this->post('/teklif-al', $this->validPayload(['condition' => 'uydurma']))
            ->assertSessionHasErrors('condition');
    }

    public function test_multiple_photos_are_accepted(): void
    {
        Storage::fake('local');

        $this->post('/teklif-al', $this->validPayload([
            'photos' => [
                UploadedFile::fake()->image('a.jpg'),
                UploadedFile::fake()->image('b.png'),
                UploadedFile::fake()->image('c.webp'),
            ],
        ]))->assertRedirect(route('quote.thanks'));

        $this->assertCount(3, QuoteRequest::firstOrFail()->photos);
    }

    public function test_valid_request_is_stored_with_photos_and_notification_mail(): void
    {
        Storage::fake('local');
        Mail::fake();
        Setting::set('notification_email', 'bildirim@example.com');
        Setting::flush();

        $response = $this->post('/teklif-al', $this->validPayload([
            'photos' => [
                UploadedFile::fake()->image('telefonlar.jpg', 800, 600),
                UploadedFile::fake()->image('liste.webp', 800, 600),
            ],
        ]));

        $response->assertRedirect(route('quote.thanks'));

        $quote = QuoteRequest::first();
        $this->assertNotNull($quote);
        $this->assertSame('Test Müşteri', $quote->name);
        $this->assertSame(QuoteRequest::STATUS_NEW, $quote->status);
        $this->assertTrue($quote->kvkk_accepted);
        $this->assertSame('Kadıköy / İstanbul', $quote->location_label);
        $this->assertCount(2, $quote->photos);

        foreach ($quote->photos as $path) {
            Storage::disk('local')->assertExists($path);
        }

        Mail::assertSent(QuoteRequestReceived::class, fn ($mail) => $mail->hasTo('bildirim@example.com'));
    }

    public function test_request_with_only_the_required_fields_is_accepted(): void
    {
        Storage::fake('local');

        // Zorunlu alanlar: ad, telefon ve KVKK onayı.
        $this->post('/teklif-al', [
            'name' => 'Ali',
            'phone' => '+90 532 111 22 33',
            'kvkk' => '1',
        ])->assertRedirect(route('quote.thanks'));

        $this->assertDatabaseCount('quote_requests', 1);
    }

    public function test_disallowed_file_types_and_too_many_photos_are_rejected(): void
    {
        Storage::fake('local');

        $this->post('/teklif-al', $this->validPayload([
            'photos' => [UploadedFile::fake()->create('belge.pdf', 100, 'application/pdf')],
        ]))->assertSessionHasErrors('photos.0');

        $this->post('/teklif-al', $this->validPayload([
            'photos' => array_map(fn ($i) => UploadedFile::fake()->image("f{$i}.jpg"), range(1, 6)),
        ]))->assertSessionHasErrors('photos');

        $this->post('/teklif-al', $this->validPayload([
            'photos' => [UploadedFile::fake()->image('buyuk.jpg')->size(6000)],
        ]))->assertSessionHasErrors('photos.0');

        $this->assertDatabaseCount('quote_requests', 0);
    }

    public function test_honeypot_and_invalid_inputs_are_rejected(): void
    {
        $this->post('/teklif-al', $this->validPayload(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('website');

        $this->post('/teklif-al', $this->validPayload(['phone' => 'abc']))
            ->assertSessionHasErrors('phone');

        $this->post('/teklif-al', $this->validPayload(['preferred_date' => now()->subDay()->toDateString()]))
            ->assertSessionHasErrors('preferred_date');

        $otherDistrict = District::whereHas('province', fn ($q) => $q->where('slug', 'edirne'))->value('id');
        $this->post('/teklif-al', $this->validPayload(['district_id' => $otherDistrict]))
            ->assertSessionHasErrors('district_id');

        $this->assertDatabaseCount('quote_requests', 0);
    }

    public function test_page_url_only_accepts_same_site_http_urls(): void
    {
        $this->post('/teklif-al', $this->validPayload(['page_url' => 'javascript:alert(1)']));
        $this->assertNull(QuoteRequest::latest('id')->first()->page_url);

        $this->post('/teklif-al', $this->validPayload(['page_url' => 'https://evil.example/x']));
        $this->assertNull(QuoteRequest::latest('id')->first()->page_url);

        $this->post('/teklif-al', $this->validPayload(['page_url' => url('/istanbul/kadikoy')]));
        $this->assertSame(url('/istanbul/kadikoy'), QuoteRequest::latest('id')->first()->page_url);
    }
}

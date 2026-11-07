@php
    $selProvince = old('province_id', $selectedProvince ?? $formProvince ?? '');
    $selDistrict = old('district_id', $selectedDistrict ?? $formDistrict ?? '');
    $selService = old('service_id', $selectedService ?? $formService ?? '');
    $selCondition = old('condition', '');
    $formId = $formId ?? 'quoteForm';
    $conditions = \App\Models\QuoteRequest::CONDITIONS;
@endphp
<div class="quote-form-card">
    @if (! empty($formTitle))
        <h3 class="mb-1">{{ $formTitle }}</h3>
    @endif
    @if (! empty($formText))
        <p class="text-muted mb-4">{{ $formText }}</p>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Formda eksik veya hatalı alanlar var:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('quote.store') }}" enctype="multipart/form-data" data-quote-form data-recaptcha id="{{ $formId }}" novalidate>
        @csrf
        <input type="hidden" name="source" value="{{ $source ?? 'form' }}">
        <input type="hidden" name="page_url" value="{{ url()->current() }}">
        @if ($selDistrict)
            {{-- İlçe sayfasından gelen talep: ilçe listesi forma gömülmez, ilçe gizli alanla taşınır. --}}
            <input type="hidden" name="district_id" value="{{ $selDistrict }}">
        @endif
        <div class="d-none" aria-hidden="true"><label>Web sitesi<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label required" for="{{ $formId }}-name">Ad Soyad / Firma</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="{{ $formId }}-name" name="name" value="{{ old('name') }}" required autocomplete="name">
            </div>
            <div class="col-md-6">
                <label class="form-label required" for="{{ $formId }}-phone">Telefon</label>
                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="{{ $formId }}-phone" name="phone" value="{{ old('phone') }}" placeholder="05xx xxx xx xx" required autocomplete="tel">
            </div>
            <div class="col-md-7">
                <label class="form-label" for="{{ $formId }}-product">Ürün / model</label>
                <input type="text" class="form-control @error('product_type') is-invalid @enderror" id="{{ $formId }}-product" name="product_type" value="{{ old('product_type') }}" maxlength="120" placeholder="Örn: iPhone 13 128 GB, Roborock S8, ofis laptopları">
            </div>
            <div class="col-md-5">
                <label class="form-label" for="{{ $formId }}-quantity">Yaklaşık adet</label>
                <input type="text" class="form-control @error('quantity') is-invalid @enderror" id="{{ $formId }}-quantity" name="quantity" value="{{ old('quantity') }}" maxlength="60" placeholder="Örn: 40 adet, 2 palet">
            </div>
            <div class="col-12">
                <span class="form-label d-block">Ürün durumu</span>
                <div class="condition-pills" role="radiogroup" aria-label="Ürün durumu">
                    @foreach ($conditions as $value => $label)
                        <input type="radio" name="condition" value="{{ $value }}" id="{{ $formId }}-cond-{{ $value }}" @checked($selCondition === $value)>
                        <label for="{{ $formId }}-cond-{{ $value }}">{{ $label }}</label>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="{{ $formId }}-province">Ürünlerin bulunduğu il</label>
                <select class="form-select @error('province_id') is-invalid @enderror" id="{{ $formId }}-province" name="province_id">
                    <option value="">İl seçin</option>
                    @foreach ($formProvinces as $province)
                        <option value="{{ $province->id }}" @selected((string) $selProvince === (string) $province->id)>{{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="{{ $formId }}-service">Ürün grubu</label>
                <select class="form-select @error('service_id') is-invalid @enderror" id="{{ $formId }}-service" name="service_id">
                    <option value="">Seçin (isteğe bağlı)</option>
                    @foreach ($formServices as $service)
                        <option value="{{ $service->id }}" @selected((string) $selService === (string) $service->id)>{{ $service->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label" for="{{ $formId }}-message">Not</label>
                <textarea class="form-control @error('message') is-invalid @enderror" id="{{ $formId }}-message" name="message" rows="3" placeholder="Kutu/aksesuar durumu, kilit (iCloud/FRP), fatura, teslim adresi gibi bilgileri yazabilirsiniz.">{{ old('message') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label" for="{{ $formId }}-photos">Fotoğraf veya ürün listesi görseli <span class="text-muted fw-normal">(isteğe bağlı, en fazla 5)</span></label>
                <div class="photo-drop @error('photos') is-invalid @enderror">
                    <i class="fa-solid fa-camera"></i>
                    <strong>Ürünlerin ya da stok listesinin fotoğrafını ekleyin</strong>
                    <div class="form-text">Durum tespitini hızlandırır. JPG, PNG veya WebP · her biri en fazla 5 MB</div>
                    <input type="file" id="{{ $formId }}-photos" name="photos[]" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple>
                </div>
                @error('photos')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <div class="form-text" data-photo-count></div>
                <div class="photo-previews" data-photo-previews></div>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input @error('kvkk') is-invalid @enderror" type="checkbox" name="kvkk" value="1" id="{{ $formId }}-kvkk" @checked(old('kvkk')) required>
                    <label class="form-check-label small" for="{{ $formId }}-kvkk">
                        @if ($kvkkPage)
                            <a href="{{ $kvkkPage->url }}" target="_blank">{{ $kvkkPage->title }}</a>'ni okudum, kişisel verilerimin talebimin değerlendirilmesi amacıyla işlenmesini kabul ediyorum.
                        @else
                            Kişisel verilerimin talebimin değerlendirilmesi amacıyla işlenmesini kabul ediyorum.
                        @endif
                    </label>
                </div>
            </div>
            <div class="col-12">
                @include('partials.recaptcha')
            </div>
            <div class="col-12 d-grid d-md-flex gap-2 pt-1">
                <button type="submit" class="btn btn-navy btn-lg"><i class="fa-solid fa-paper-plane"></i>Teklif İste</button>
                @if (site_phone())
                    <a class="btn btn-call btn-lg" href="{{ phone_href() }}"><i class="fa-solid fa-phone"></i>Hemen Ara</a>
                @endif
            </div>
        </div>
    </form>
</div>

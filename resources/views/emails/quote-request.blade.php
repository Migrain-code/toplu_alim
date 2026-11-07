<!DOCTYPE html>
<html lang="tr">
<head><meta charset="UTF-8"><title>Yeni teklif talebi</title></head>
<body style="font-family: Arial, sans-serif; color:#222; line-height:1.6;">
    <h2 style="margin-bottom:4px;">Yeni teklif talebi</h2>
    <p style="margin-top:0;color:#666;">{{ $quote->created_at->format('d.m.Y H:i') }}</p>
    <table cellpadding="6" style="border-collapse:collapse;">
        <tr><td><strong>Ad Soyad</strong></td><td>{{ $quote->name }}</td></tr>
        <tr><td><strong>Telefon</strong></td><td><a href="{{ phone_href($quote->phone) }}">{{ $quote->phone }}</a></td></tr>
        @if($quote->email)<tr><td><strong>E-posta</strong></td><td>{{ $quote->email }}</td></tr>@endif
        <tr><td><strong>Bölge</strong></td><td>{{ $quote->location_label ?: '-' }}</td></tr>
        <tr><td><strong>Hizmet</strong></td><td>{{ $quote->service?->title ?? '-' }}</td></tr>
        @if($quote->product_type)<tr><td><strong>Ürün türü</strong></td><td>{{ $quote->product_type }}</td></tr>@endif
        @if($quote->quantity)<tr><td><strong>Yaklaşık adet</strong></td><td>{{ $quote->quantity }}</td></tr>@endif
        @if($quote->condition)<tr><td><strong>Ürün durumu</strong></td><td>{{ $quote->condition_label }}</td></tr>@endif
        @if($quote->preferred_date)<tr><td><strong>Tercih edilen tarih</strong></td><td>{{ $quote->preferred_date->format('d.m.Y') }}</td></tr>@endif
        <tr><td><strong>Fotoğraf</strong></td><td>{{ count($quote->photos ?? []) }} adet</td></tr>
        <tr><td valign="top"><strong>Açıklama</strong></td><td>{!! nl2br(e($quote->message ?: '-')) !!}</td></tr>
    </table>
    <p><a href="{{ $adminUrl }}" style="display:inline-block;background:#1d4ed8;color:#fff;padding:10px 18px;text-decoration:none;border-radius:4px;">Talebi panelde aç</a></p>
</body>
</html>

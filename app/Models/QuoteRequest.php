<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequest extends Model
{
    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_NEW => 'Yeni',
        self::STATUS_CONTACTED => 'İletişime Geçildi',
        self::STATUS_SCHEDULED => 'Teklif Verildi',
        self::STATUS_COMPLETED => 'Tamamlandı',
        self::STATUS_CANCELLED => 'İptal',
    ];

    public const STATUS_COLORS = [
        self::STATUS_NEW => 'warning',
        self::STATUS_CONTACTED => 'info',
        self::STATUS_SCHEDULED => 'primary',
        self::STATUS_COMPLETED => 'success',
        self::STATUS_CANCELLED => 'danger',
    ];

    /** Satılmak istenen ürünlerin durumu (formdaki seçenekler). */
    public const CONDITIONS = [
        'sifir' => 'Sıfır / kapalı kutu',
        'teshir_iade' => 'Teşhir veya iade',
        'ikinci_el' => 'İkinci el, çalışır',
        'arizali' => 'Arızalı / hurda',
        'karisik' => 'Karışık',
    ];

    protected $fillable = [
        'name', 'phone', 'email', 'province_id', 'district_id', 'service_id',
        'product_type', 'quantity', 'condition', 'message',
        'photos', 'preferred_date', 'kvkk_accepted', 'status', 'admin_notes', 'source',
        'page_url', 'ip', 'user_agent',
        'assigned_to', 'assigned_by', 'assigned_at', 'assignment_note',
    ];

    protected function casts(): array
    {
        return [
            'photos' => 'array',
            'preferred_date' => 'date',
            'kvkk_accepted' => 'boolean',
            'assigned_at' => 'datetime',
        ];
    }

    public function getConditionLabelAttribute(): ?string
    {
        return $this->condition ? (self::CONDITIONS[$this->condition] ?? $this->condition) : null;
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /** Talebi yürütecek saha personeli. */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** Atamayı yapan müşteri temsilcisi. */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /** Saha personeli yalnız kendi taleplerini görür. */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if ($user && $user->isInstaller()) {
            return $query->where('assigned_to', $user->getKey());
        }

        return $query;
    }

    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_to');
    }

    public function assignTo(User $installer, User $by, ?string $note = null): void
    {
        $this->forceFill([
            'assigned_to' => $installer->getKey(),
            'assigned_by' => $by->getKey(),
            'assigned_at' => now(),
            'assignment_note' => $note,
            // Atama yapıldıysa talep artık "yeni" değildir.
            'status' => $this->status === self::STATUS_NEW ? self::STATUS_CONTACTED : $this->status,
        ])->save();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getLocationLabelAttribute(): string
    {
        return collect([$this->district?->name, $this->province?->name])->filter()->implode(' / ');
    }
}

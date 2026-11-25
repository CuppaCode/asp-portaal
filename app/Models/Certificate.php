<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use App\Models\Driver;
use App\Models\CertificateCategory;
use App\Models\CertificateMeta;

class Certificate extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public $table = 'certificate';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'last_notification_sent_at',
        'renewal_token_expires_at',
    ];

    protected $fillable = [
        'driver_id',
        'category_id',
        'created_at',
        'name',
        'notify_date',
        'expiry_date',
        'not_applicable',
        'issue_date',
        'updated_at',
        'deleted_at',
        'team_id',
        'last_notification_sent_at',
        'renewal_token',
        'renewal_token_expires_at',
        'original_expiry_date',
        'renewed_by_email',
        'renewed_by_user_id',
    ];

    protected $casts = [
        'not_applicable' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class)->withTrashed();
    }

    public function signals()
    {
        return $this->hasMany(CertificateSignal::class);
    }

    /**
     * Raw expiry date. The expiry_date attribute is formatted for display (d-m-Y).
     */
    public function expiryCarbon(): ?Carbon
    {
        $raw = $this->attributes['expiry_date'] ?? null;

        return $raw ? Carbon::parse($raw)->startOfDay() : null;
    }

    public function category()
    {
        return $this->belongsTo(CertificateCategory::class, 'category_id');
    }

    public function renewedBy()
    {
        return $this->belongsTo(User::class, 'renewed_by_user_id');
    }

    public function renewals()
    {
        return $this->hasMany(CertificateRenewal::class);
    }

    public function code95Courses()
    {
        return $this->hasMany(Code95Course::class, 'certificate_id')->orderBy('course_date');
    }

    public function metaFields()
    {
        return $this->hasMany(CertificateMeta::class, 'certificate_id');
    }

    public function getMetaValue(string $fieldKey, $default = null)
    {
        $meta = $this->relationLoaded('metaFields')
            ? $this->metaFields->firstWhere('field_key', $fieldKey)
            : $this->metaFields()->where('field_key', $fieldKey)->first();

        return $meta ? $meta->resolved_value : $default;
    }

    public function upsertMetaValue(
        string $fieldKey,
        string $valueType,
        $value,
        string $visibilityScope = 'internal',
        ?string $maskedDisplayValue = null,
        ?string $source = null
    ): CertificateMeta {
        $payload = [
            'value_type' => $valueType,
            'visibility_scope' => $visibilityScope,
            'masked_display_value' => $maskedDisplayValue,
            'source' => $source,
            'value_text' => null,
            'value_number' => null,
            'value_date' => null,
            'value_json' => null,
        ];

        switch ($valueType) {
            case 'number':
                $payload['value_number'] = $value;
                break;
            case 'date':
                $payload['value_date'] = $value;
                break;
            case 'json':
                $payload['value_json'] = $value;
                break;
            default:
                $payload['value_text'] = $value;
                break;
        }

        return $this->metaFields()->updateOrCreate(
            ['field_key' => $fieldKey],
            $payload
        );
    }

    public function isRenewed()
    {
        return $this->original_expiry_date !== null
            && Carbon::parse($this->expiry_date)->gt(Carbon::parse($this->original_expiry_date));
    }

    public function scopeNotRenewed($query)
    {
        return $query->whereNull('original_expiry_date');
    }

    public function scopeExpiredLongerThan($query, $days)
    {
        return $query->where('expiry_date', '<', Carbon::now()->subDays($days));
    }

    public function getNotifyDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function getExpiryDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setNotifyDateAttribute($value)
    {
        $this->attributes['notify_date'] = $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function getIssueDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setIssueDateAttribute($value)
    {
        $this->attributes['issue_date'] = $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function setExpiryDateAttribute($value)
    {
        $this->attributes['expiry_date'] = $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // Certificates do not need generated image conversions.
    }
}

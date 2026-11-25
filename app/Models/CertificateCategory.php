<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Certificate;

class CertificateCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'certificate_categories';

    protected $fillable = [
        'name',
        'code',
        'duration',
        'notify_days_before',
        'enable_notifications',
        'notification_recipients',
        'reminder_frequency_days',
        'academy',
        'one_off',
        'allow_not_applicable',
        'signal_1_months',
        'signal_2_days',
        'signal_1_only_if_hours_open',
        'sort_order',
    ];

    protected $casts = [
        'enable_notifications' => 'boolean',
        'notification_recipients' => 'array',
        'academy' => 'boolean',
        'one_off' => 'boolean',
        'allow_not_applicable' => 'boolean',
        'signal_1_only_if_hours_open' => 'boolean',
    ];

    public function scopeAcademy($query)
    {
        return $query->where('academy', true)->orderBy('sort_order');
    }

    public function isCode95(): bool
    {
        return $this->code === 'code_95';
    }

    protected static function booted(): void
    {
        static::saving(function (CertificateCategory $category) {
            if (empty($category->code)) {
                $category->code = static::generateUniqueCode($category->name, $category->id);
            }
        });
    }

    public static function generateUniqueCode(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name, '_');
        $base = $base !== '' ? $base : 'category';

        $code = $base;
        $counter = 1;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('code', $code)
            ->exists()) {
            $code = $base . '_' . $counter;
            $counter++;
        }

        return $code;
    }

    public function getModuleKeyAttribute(): string
    {
        return $this->code ?: 'default';
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'category_id');
    }
}

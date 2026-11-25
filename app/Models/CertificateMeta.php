<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateMeta extends Model
{
    use HasFactory;

    protected $table = 'certificate_meta';

    protected $fillable = [
        'certificate_id',
        'field_key',
        'value_type',
        'value_text',
        'value_number',
        'value_date',
        'value_json',
        'visibility_scope',
        'masked_display_value',
        'source',
    ];

    protected $casts = [
        'value_number' => 'decimal:4',
        'value_date' => 'date',
        'value_json' => 'array',
    ];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class, 'certificate_id');
    }

    public function getResolvedValueAttribute()
    {
        return match ($this->value_type) {
            'number' => $this->value_number,
            'date' => $this->value_date,
            'json' => $this->value_json,
            default => $this->value_text,
        };
    }
}
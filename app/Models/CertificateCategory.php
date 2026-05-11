<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Certificate;

class CertificateCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'certificate_categories';

    protected $fillable = [
        'name',
        'duration',
        'notify_days_before',
        'enable_notifications',
        'notification_recipients',
        'reminder_frequency_days',
    ];

    protected $casts = [
        'enable_notifications' => 'boolean',
        'notification_recipients' => 'array',
    ];

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'category_id');
    }
}

<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MailTemplate extends Model implements HasMedia
{
    use SoftDeletes, MultiTenantModelTrait, InteractsWithMedia, Auditable, HasFactory;

    public $table = 'mail_templates';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'subject',
        'body',
        'trigger_type',
        'is_active',
        'is_automatic',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_automatic' => 'boolean',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function mailings()
    {
        return $this->hasMany(Mailing::class, 'mail_template_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTrigger($query, $triggerType)
    {
        return $query->where('trigger_type', $triggerType);
    }

    public function scopeAutomatic($query)
    {
        return $query->where('is_automatic', true);
    }

    public function scopeManual($query)
    {
        return $query->where('is_automatic', false);
    }
}

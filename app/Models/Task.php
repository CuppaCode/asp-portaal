<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\MultiTenantModelTrait;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Task extends Model implements HasMedia
{
    use SoftDeletes, MultiTenantModelTrait, InteractsWithMedia, Auditable, HasFactory;

    public $table = 'tasks';

    protected $dates = [
        'created_at',
        'deadline_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'new'         => 'New',
        'in_progress' => 'In Progress',
        'waiting'     => 'Waiting...',
        'done'        => 'Done',
    ];

    protected $fillable = [
        'task_number',
        'description',
        'user_id',
        'claim_id',
        'created_at',
        'deadline_at',
        'status',
        'updated_at',
        'deleted_at',
        'team_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function claim()
    {
        return $this->belongsTo(Claim::class, 'claim_id');
    }

    public function getDeadlineAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setDeadlineAtAttribute($value)
    {
        $this->attributes['deadline_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

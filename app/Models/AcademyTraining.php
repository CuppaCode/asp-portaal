<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademyTraining extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public const PROVIDER_ASP = 'asp';
    public const PROVIDER_PARTNER = 'partner';

    public const PROVIDER_SELECT = [
        self::PROVIDER_ASP => 'ASP zelf',
        self::PROVIDER_PARTNER => 'Partner',
    ];

    public const CODE95_TYPE_SELECT = [
        'theory' => 'Theorie',
        'practice' => 'Praktijk',
    ];

    public $table = 'academy_trainings';

    protected $fillable = [
        'company_id',
        'team_id',
        'name',
        'cbr_code',
        'training_date',
        'location',
        'provider_type',
        'provider_name',
        'provider_email',
        'code95_hours',
        'code95_type',
        'is_prevention',
        'notes',
        'closed_at',
    ];

    protected $casts = [
        'training_date' => 'date',
        'closed_at' => 'datetime',
        'is_prevention' => 'boolean',
        'code95_hours' => 'integer',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function participants()
    {
        return $this->hasMany(AcademyTrainingParticipant::class, 'training_id');
    }

    public function items()
    {
        return $this->morphMany(AcademyChecklistItem::class, 'subject');
    }

    public function courses()
    {
        return $this->hasMany(Code95Course::class, 'academy_training_id');
    }

    public function scopeOpen($query)
    {
        return $query->whereNull('closed_at');
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    public function hasCode95Module(): bool
    {
        return !empty($this->code95_hours);
    }

    public function isAspProvider(): bool
    {
        return $this->provider_type === self::PROVIDER_ASP;
    }
}

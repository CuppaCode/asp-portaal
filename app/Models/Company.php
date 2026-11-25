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

class Company extends Model implements HasMedia
{
    use SoftDeletes, MultiTenantModelTrait, InteractsWithMedia, Auditable, HasFactory;

    public $table = 'companies';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const COMPANY_TYPE_SELECT = [
        'injury'     => 'Letsel',
        'transport'  => 'Transport',
        'touringcar' => 'Touringcar',
        'salvage'    => 'Berging',
        'recovery'   => 'Hersteller',
        'expertise'  => 'Expertise',
        'insurance'  => 'Verzekeraar',
    ];

    protected $fillable = [
        'name',
        'company_type',
        'street',
        'zipcode',
        'city',
        'country',
        'phone',
        'active',
        'logo',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
        'contact_id',
        'bank_account_number',
        'company_size',
        'truck_count',
        'additional_information',
        'start_fee',
        'claims_fee',
        'additional_costs',
        'academy_enabled',
        'academy_started_at',
        'academy_completed_at',
    ];

    protected $casts = [
        'academy_enabled' => 'boolean',
        'academy_started_at' => 'date',
        'academy_completed_at' => 'date',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

        public function sla()
        {
            return $this->hasOne(SLA::class, 'company_id');
        }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function scopeAcademy($query)
    {
        return $query->where('academy_enabled', true);
    }

    public function contactPerson()
    {
        return $this->belongsTo(Contact::class, 'contact_id')->withTrashed();
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function academyTrainings()
    {
        return $this->hasMany(AcademyTraining::class);
    }

    public function academyItems()
    {
        return $this->morphMany(AcademyChecklistItem::class, 'subject');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function claimTokens()
    {
        return $this->hasMany(CompanyClaimToken::class);
    }

    public function claimFormConfigs()
    {
        return $this->hasMany(CompanyClaimFormConfig::class);
    }

    public function claimFormNotifications()
    {
        return $this->hasMany(CompanyClaimFormNotification::class);
    }

    public function customClaimFields()
    {
        return $this->hasMany(CompanyCustomClaimField::class);
    }
}

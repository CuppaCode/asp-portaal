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

class Claim extends Model implements HasMedia
{
    use SoftDeletes, MultiTenantModelTrait, InteractsWithMedia, Auditable, HasFactory;

    public $table = 'claims';

    public const INJURY_SELECT = [
        'yes'   => 'Ja',
        'no'    => 'Nee',
        'other' => 'Anders...',
    ];

    public const DAMAGED_PART_SELECT = [
        'backpart'  => 'Achterscherm',
        'tire'      => 'Band/Velg',
        'roof'      => 'Dak',
        'dorpel'    => 'Dorpel',
        'interior'  => 'Interieur',
        'motor'     => 'Motor',
        'portdoor'  => 'Portier',
        'window'    => 'Ruit',
        'light'     => 'Verlichting'
    ];

    public const CONTACT_LAWYER_SELECT = [
        'yes' => 'Ja',
        'no'  => 'Nee',
        'n/a' => 'N.V.T',
    ];

    protected $appends = [
        'damage_files',
        'report_files',
        'financial_files',
        'other_files',
    ];

    public const DAMAGED_PART_OPPOSITE_SELECT = [
        'backpart'  => 'Achterscherm',
        'tire'      => 'Band/Velg',
        'roof'      => 'Dak',
        'dorpel'    => 'Dorpel',
        'interior'  => 'Interieur',
        'motor'     => 'Motor',
        'portdoor'  => 'Portier',
        'window'    => 'Ruit',
        'light'     => 'Verlichting'
    ];

    public const OPPOSITE_TYPE_SELECT = [
        'private'  => 'Particulier',
        'business' => 'Zakelijk',
        'unknown'  => 'Onbekend',
    ];

    public const STATUS_SELECT = [
        'new'                       => 'Nieuw',
        'on_hold'                   => 'Claim ingediend',
        'awaiting_docs'             => 'Afwachting documenten',
        'received_docs'             => 'Documenten binnen',
        'expert'                    => 'Expert ingeschakeld',
        'delivered'                 => 'Afspraak aflevering',
        'damagerepair'              => 'Afspraak schadeherstel',
        'liable'                    => 'Aansprakelijk gesteld',
        'invoice_asp'               => 'Factuur ASP',
        'saf_received'              => 'SAF binnen',
        'amount_incident'           => 'Schadebedrag bekend',
        'invoice_incident'          => 'Schadefactuur afwachten',
        'invoice_indicent_payed'    => 'Schadefactuur betaald',
        'payment_recveived'         => 'Uitkering binnen',
        'finished'                  => 'Afgesloten',
    ];

    protected $dates = [
        'date_accident',
        'requested_at',
        'report_received_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const RECOVERABLE_CLAIM_SELECT = [
        'unknown'   => 'Onbekend',
        'yes'       => 'Ja',
        'no'        => 'Nee',
        'partially' => 'Gedeeltelijk',
    ];

    public const DAMAGED_AREA_SELECT = [
        'left_side'   => 'Linkerzijde',
        'right_side'  => 'Rechterzijde',
        'top_side'    => 'Bovenzijde',
        'bottom_side' => 'Onderzijde',
        'front_side'  => 'Voorzijde',
        'back_side'   => 'Achterzijde',
    ];

    public const DAMAGED_AREA_OPPOSITE_SELECT = [
        'left_side'   => 'Linkerzijde',
        'right_side'  => 'Rechterzijde',
        'top_side'    => 'Bovenzijde',
        'bottom_side' => 'Onderzijde',
        'front_side'  => 'Voorzijde',
        'back_side'   => 'Achterzijde',
    ];

    protected $fillable = [
        'company_id',
        'assign_self',
        'subject',
        'claim_number',
        'status',
        'injury',
        'contact_lawyer',
        'date_accident',
        'recoverable_claim',
        'injury_other',
        'injury_office_id',
        'vehicle_id',
        'vehicle_opposite_id',
        'opposite_type',
        'damaged_part',
        'damage_origin',
        'damaged_area',
        'damage_kind',
        'damaged_part_opposite',
        'damage_origin_opposite',
        'damaged_area_opposite',
        'recovery_office_id',
        'damage_costs',
        'recovery_costs',
        'replacement_vehicle_costs',
        'expert_costs',
        'other_costs',
        'deductible_excess_costs',
        'insurance_costs',
        'expertise_office_id',
        'expert_report_is_in',
        'requested_at',
        'report_received_at',
        'created_at',
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

    public function claimNotes()
    {
        return $this->belongsToMany(Note::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getDateAccidentAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDateAccidentAttribute($value)
    {
        $this->attributes['date_accident'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function injury_office()
    {
        return $this->belongsTo(InjuryOffice::class, 'injury_office_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function vehicle_opposite()
    {
        return $this->belongsTo(VehicleOpposite::class, 'vehicle_opposite_id');
    }

    public function recovery_office()
    {
        return $this->belongsTo(RecoveryOffice::class, 'recovery_office_id');
    }

    public function expertise_office()
    {
        return $this->belongsTo(ExpertiseOffice::class, 'expertise_office_id');
    }

    public function getRequestedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setRequestedAtAttribute($value)
    {
        $this->attributes['requested_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getReportReceivedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setReportReceivedAtAttribute($value)
    {
        $this->attributes['report_received_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getDamageFilesAttribute()
    {
        return $this->getMedia('damage_files');
    }

    public function getReportFilesAttribute()
    {
        return $this->getMedia('report_files');
    }

    public function getFinancialFilesAttribute()
    {
        return $this->getMedia('financial_files');
    }

    public function getOtherFilesAttribute()
    {
        return $this->getMedia('other_files');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

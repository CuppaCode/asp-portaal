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
    use SoftDeletes, InteractsWithMedia, Auditable, HasFactory, MultiTenantModelTrait;

    public $table = 'claims';

    public const INJURY_SELECT = [
        'yes'   => 'Ja',
        'no'    => 'Nee',
        'other' => 'Anders...',
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

    public const DAMAGED_PART_SELECT = [
        'backpart'  => 'Achterscherm', //Fallback?
        'trunk'     => 'Achterklep',
        'tire'      => 'Band/Velg',
        'bumper'    => 'Bumper',
        'carter'    => 'Carter',
        'roof'      => 'Dak',
        'doormolding'   => 'Deurrubber',
        'supportarm' => 'Draagarm',
        'dorpel'    => 'Dorpel',
        'interior'  => 'Interieur', //Fallback?
        'motor'     => 'Motor',
        'portdoor'  => 'Portier',
        'window'    => 'Ruit',
        'carhood'   => 'Motorkap',
        'grill'     => 'Grill',
        'steeringwheel' => 'Stuurinrichting',
        'mirror'    => 'Spiegel',
        'screen' => 'Scherm',
        'exhaust'   => 'Uitlaat',
        'light'     => 'Verlichting', //Fallback?
        'wheel'     => 'Wielophanding',
        'clutch' => 'Koppeling',
        'valve_box' => 'Klepkast',
        'license_plate' => 'Kentekenplaat',
        'battery' => 'Accu',
        'bump_strip' => 'Stootlijst/sierlijst',
        'tow_bar' => 'Trekhaak',
        'leveling_leg' => 'Stelpoot (caravan/camper)',
        'drawbar' => 'Dissel',
        'headlight' => 'Koplamp',
        'taillight' => 'Achterlicht',
        'raw' => 'RAW',
        'steering_wheel' => 'Stuur',
        'dashboard' => 'Dashboard',
        'upholstery' => 'Stoel/bank/bekleding',
        'door_panel' => 'Deurpaneel',
        'gear_lever' => 'Schakelpook/Aut. handle'
    ];

    public const DAMAGED_PART_OPPOSITE_SELECT = [
        'backpart'  => 'Achterscherm', //Fallback?
        'trunk'     => 'Achterklep',
        'tire'      => 'Band/Velg',
        'bumper'    => 'Bumper',
        'carter'    => 'Carter',
        'roof'      => 'Dak',
        'doormolding'   => 'Deurrubber',
        'supportarm' => 'Draagarm',
        'dorpel'    => 'Dorpel',
        'interior'  => 'Interieur', //Fallback?
        'motor'     => 'Motor',
        'portdoor'  => 'Portier',
        'window'    => 'Ruit',
        'carhood'   => 'Motorkap',
        'grill'     => 'Grill',
        'steeringwheel' => 'Stuurinrichting',
        'mirror'    => 'Spiegel',
        'screen' => 'Scherm',
        'exhaust'   => 'Uitlaat',
        'light'     => 'Verlichting', //Fallback?
        'wheel'     => 'Wielophanding',
        'clutch' => 'Koppeling',
        'valve_box' => 'Klepkast',
        'license_plate' => 'Kentekenplaat',
        'battery' => 'Accu',
        'bump_strip' => 'Stootlijst/sierlijst',
        'tow_bar' => 'Trekhaak',
        'leveling_leg' => 'Stelpoot (caravan/camper)',
        'drawbar' => 'Dissel',
        'headlight' => 'Koplamp',
        'taillight' => 'Achterlicht',
        'raw' => 'RAW',
        'steering_wheel' => 'Stuur',
        'dashboard' => 'Dashboard',
        'upholstery' => 'Stoel/bank/bekleding',
        'door_panel' => 'Deurpaneel',
        'gear_lever' => 'Schakelpook/Aut. handle'
    ];

    public const OPPOSITE_TYPE_SELECT = [
        'private'  => 'Particulier',
        'business' => 'Zakelijk',
        'lease_car' => 'Leaseauto',
        'unknown'  => 'Onbekend',
        'obstacle' => 'Obstakel', //Fallback?
    ];

    public const STATUS_SELECT = [
        'new'                       => 'Nieuw',
        'in_progress'               => 'In behandeling',
        'claim_denied'              => 'Claim afgewezen',        
        'damage_estimate_requested' => 'Schadebedrag opgevraagd',
        'damage_form_requested'     => 'Schadeformulier opgevraagd', // new
        'requested_info'            => 'Aanvullende informatie opgevraagd',
        'opposite_is_responsible'   => 'WP aansprakelijk gesteld', // new
        'responsible_by_opposite'   => 'Aansprakelijk gesteld door WP', // new
        'requested_expert'          => 'Expert aangevraagd',
        'awaiting_approval'         => 'Goedkeuring aangevraagd', 
        'with_recovery'             => 'Bij hersteller', // new
        'awaiting_invoice'          => 'Factuur naar administratie',
        'reopened'                  => 'Dossier heropend', // new
        'finished'                  => 'Dossier Gesloten',

        'damage_estimate_received'  => 'Schadebedrag ontvangen', //Fallback?
        'sended_responsible'        => 'Aansprakelijkheid verstuurd',
        'received_responsible'      => 'Aansprakelijkheid ontvangen',
        'awaiting_report'           => 'Rapport afwachten',
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
        'inside'      => 'Binnenzijde' //new
    ];

    public const DAMAGED_AREA_OPPOSITE_SELECT = [
        'left_side'   => 'Linkerzijde',
        'right_side'  => 'Rechterzijde',
        'top_side'    => 'Bovenzijde',
        'bottom_side' => 'Onderzijde',
        'front_side'  => 'Voorzijde',
        'back_side'   => 'Achterzijde',
        'inside'      => 'Binnenzijde' //new
    ];

    public const DAMAGE_KIND = [
        'traffic'   => 'Verkeer',
        'transport' => 'Transport',
    ];

    public const DAMAGE_ORIGIN = [
        'no_priority'                   => 'Geen voorrang',
        'parking'                       => 'Parkeren',
        'backtoopposite'                => 'Achterop TP',
        'oppositetoback'                => 'TP achterop',
        'backed_up' => 'Achteruitgereden',
        'enter_leave_traffic' => 'In- of uitvoegen',
        'change_lane' => 'Veranderen van rijstrook',
        'run_redlight' => 'Door rood verkeerslicht',
        'insufficient_right_keeping' => 'Onvoldoende rechtshouden',
        'swerve' => 'Uitwijken',
        'parking_damage_still' => 'Parkeerschade (stond stil)',
        'parking_damage_smash' => 'Parkeerschade (reed tegen WP)',
        'pushed_in_traffic' => 'Doorgedrukt in file',
        'water_damage' => 'Hagel/storm/water',
        'part_loose' => 'Onderdeel los/open',
        'electrical_fault' => 'Electrische storing',
        'breakdown_IM' => 'Pechverplaatsing IM',
        'wrong_parker' => 'Foutparkeerder',
        'seizure' => 'In beslagname',
        'insufficient_fuse' => 'Onvoldoende zekering',
        'broken_cable' => 'Afgebroken kabel/spanband/strop',
        'lost_cargo' => 'Verloren lading/onderdeel',
        'spoon_damage' => 'Lepelschade',
        'upper_deck_damage' => 'Bovendek schade',
        'open_door' => 'Openen portier',
        'flat_tire' => 'Lekke band',
        'storage_from_ditch' => 'Berging uit sloot/greppel',
        'slip'                          => 'Slippen',
        'special_maneuver'              => 'Bijzondere manoeuvre',
        'obstacle'                      => 'Tegen opstakel',
        'loadings'                      => 'Laden',
        'unloading'                     => 'Lossen',
        'animal_collision'              => 'Aanrijding dier',
        'stone_chips'                   => 'Steenslag',
        'fire'                          => 'Brand/kortsluiting',
        'cyclist_pedestrian_collision'  => 'Aanrijding fietser/voetganger',
        'dodge'                         => 'Uitwijken',
        'transport'                     => 'Tranport'
    ];

    public const DAMAGE_ORIGIN_OPPOSITE = [
        'no_priority'                   => 'Geen voorrang',
        'parking'                       => 'Parkeren',
        'backtoopposite'                => 'Achterop TP',
        'oppositetoback'                => 'TP achterop',
        'backed_up' => 'Achteruitgereden',
        'enter_leave_traffic' => 'In- of uitvoegen',
        'change_lane' => 'Veranderen van rijstrook',
        'run_redlight' => 'Door rood verkeerslicht',
        'insufficient_right_keeping' => 'Onvoldoende rechtshouden',
        'swerve' => 'Uitwijken',
        'parking_damage_still' => 'Parkeerschade (stond stil)',
        'parking_damage_smash' => 'Parkeerschade (reed tegen WP)',
        'pushed_in_traffic' => 'Doorgedrukt in file',
        'water_damage' => 'Hagel/storm/water',
        'part_loose' => 'Onderdeel los/open',
        'electrical_fault' => 'Electrische storing',
        'breakdown_IM' => 'Pechverplaatsing IM',
        'wrong_parker' => 'Foutparkeerder',
        'seizure' => 'In beslagname',
        'insufficient_fuse' => 'Onvoldoende zekering',
        'broken_cable' => 'Afgebroken kabel/spanband/strop',
        'lost_cargo' => 'Verloren lading/onderdeel',
        'spoon_damage' => 'Lepelschade',
        'upper_deck_damage' => 'Bovendek schade',
        'open_door' => 'Openen portier',
        'flat_tire' => 'Lekke band',
        'storage_from_ditch' => 'Berging uit sloot/greppel',
        'slip'                          => 'Slippen',
        'special_maneuver'              => 'Bijzondere manoeuvre',
        'obstacle'                      => 'Tegen opstakel',
        'loadings'                      => 'Laden',
        'unloading'                     => 'Lossen',
        'animal_collision'              => 'Aanrijding dier',
        'stone_chips'                   => 'Steenslag',
        'fire'                          => 'Brand/kortsluiting',
        'cyclist_pedestrian_collision'  => 'Aanrijding fietser/voetganger',
        'dodge'                         => 'Uitwijken',
        'transport'                     => 'Tranport'
    ];

    public const DECLINE_REASON_SELECT = [
        'no_response'   => 'Geen reactie',
        'no_liability'  => 'Geen aansprakelijkheid',
        'faulty'        => 'Foutieve melding',
        'duplicate'     => 'Dubbele melding',
        'no_agreement'  => 'Geen akkoord opdrachtgever'
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
        'driver_vehicle',
        'driver_vehicle_opposite',
        'opposite_type',
        'obstacle',
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
        'invoice_settlement_asp',
        'opposite_claim_no',
        'invoice_comment',
        'assignee_id',
        'invoice_amount',
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

    public function notes()
    {
        return $this->belongsToMany(Note::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
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
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('panel.date_format')) : null;
    }

    public function setRequestedAtAttribute($value)
    {
        $this->attributes['requested_at'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getReportReceivedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('panel.date_format')) : null;
    }

    public function setReportReceivedAtAttribute($value)
    {
        $this->attributes['report_received_at'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
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

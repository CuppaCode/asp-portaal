<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SLA extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'SLA';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'startdate',
        'enddate',
    ];

    protected $fillable = [
        'company_id',
        'startdate',
        'enddate',
        'label',
        'max_amount',
        'amount_users',
        'reports',
        'analytics_options',
        'other',
    ];

    public const REPORT_SELECT = [
        'year'   => 'Per jaar',
        'halfyear'    => 'Per halfjaar',
        'quarterly' => 'Per kwartaal',
    ];

    public const LABEL_SELECT = [
        'asp' => 'AutoSchadePlan',
        'company' => 'Opdrachtgever',
    ];

    public const ANALYTICS_SELECT = [
        'damage_cost'           => 'Schadebedrag per maand, kwartaal, halfjaar, jaar',
        'saving_cost'           => 'Besparing per maand, kwartaal, half jaar, jaar',
        'damage_kind'           => 'Schadebedrag per soort',
        'damage_activity'       => 'Schadebedrag per activiteit (transportschade)',
        'damage_driver'         => 'Schade per medewerker aantal/bedrag',
        'damage_vehicle'        => 'Schade per voertuig aantal/bedrag',
        'qty_claims'            => 'Aantal claims/toegewezen/afgeweze',
        'recoverable'           => 'Aantal verhaalbare schade/ niet verhaalbare schade',
        'other'                 => 'Anders..',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['startdate'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['enddate'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
}

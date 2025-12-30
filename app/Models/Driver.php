<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes, MultiTenantModelTrait, Auditable, HasFactory;

    public $table = 'drivers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
        'company_id',
        'contact_id'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function driverVehicles()
    {
        return $this->belongsToMany(Vehicle::class);
    }

    public function driverVehicleOpposites()
    {
        return $this->belongsToMany(VehicleOpposite::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function getDriverFullNameAttribute()
    {
        if(!empty($this->contact) && !empty($this->company)) {
            return "{$this->contact->first_name} {$this->contact->last_name} | {$this->company->name}";
        } else {
            return "Niet gevonden";
        }
    }

    public function getDriverNameAttribute()
    {
        if(!empty($this->contact)) {
            return "{$this->contact->first_name} {$this->contact->last_name}";
        } else {
            return "Niet gevonden";
        }
    }
}


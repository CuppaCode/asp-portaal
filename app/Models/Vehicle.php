<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'vehicles';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'company_id',
        'name',
        'plates',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function vehicleDrivers()
    {
        return $this->hasMany(Driver::class, 'vehicle_id', 'id');
    }

    public function vehicleClaims()
    {
        return $this->hasMany(Claim::class, 'vehicle_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

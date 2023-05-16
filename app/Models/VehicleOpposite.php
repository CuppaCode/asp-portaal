<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleOpposite extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'vehicle_opposites';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
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

    public function vehicleOppositeDrivers()
    {
        return $this->hasMany(Driver::class, 'vehicle_opposite_id', 'id');
    }

    public function vehicleOppositeClaims()
    {
        return $this->hasMany(Claim::class, 'vehicle_opposite_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

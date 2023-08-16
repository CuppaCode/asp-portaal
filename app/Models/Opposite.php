<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opposite extends Model
{
    use SoftDeletes, MultiTenantModelTrait, Auditable, HasFactory;

    public $table = 'opposites';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'street',
        'zipcode',
        'city',
        'country',
        'email',
        'phone',
        'created_at',
        'updated_at',
        'deleted_at',
        'claim_id',
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class, 'claim_id');
    }
}

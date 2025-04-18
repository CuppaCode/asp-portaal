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
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
        'contact_id',
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

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}

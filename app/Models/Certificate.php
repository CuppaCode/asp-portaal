<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Models\Driver;

class Certificate extends Model
{
    use HasFactory;

    public $table = 'certificate';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'driver_id',
        'created_at',
        'name',
        'notify_date',
        'expiry_date',
        'updated_at',
        'deleted_at',
        'team_id',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function setNotifyDateAttribute($value)
    {
        $this->attributes['notify_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function setExpiryDateAttribute($value)
    {
        $this->attributes['expiry_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
}

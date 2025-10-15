<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}

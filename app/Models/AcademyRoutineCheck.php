<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademyRoutineCheck extends Model
{
    use HasFactory;

    public $table = 'academy_routine_checks';

    protected $fillable = [
        'check_date',
        'check_key',
        'done_by_user_id',
        'done_at',
    ];

    protected $casts = [
        'check_date' => 'date',
        'done_at' => 'datetime',
    ];

    public function doneBy()
    {
        return $this->belongsTo(User::class, 'done_by_user_id')->withTrashed();
    }
}

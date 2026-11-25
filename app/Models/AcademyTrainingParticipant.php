<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademyTrainingParticipant extends Model
{
    use HasFactory;

    public $table = 'academy_training_participants';

    protected $fillable = [
        'training_id',
        'driver_id',
        'attended',
        'cbr_confirmed',
        'code95_course_id',
    ];

    protected $casts = [
        'attended' => 'boolean',
        'cbr_confirmed' => 'boolean',
    ];

    public function training()
    {
        return $this->belongsTo(AcademyTraining::class, 'training_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class)->withTrashed();
    }

    public function course()
    {
        return $this->belongsTo(Code95Course::class, 'code95_course_id');
    }
}

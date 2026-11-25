<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code95Course extends Model
{
    use HasFactory;

    protected $table = 'code95_courses';

    protected $fillable = [
        'certificate_id',
        'course_date',
        'name',
        'code',
        'type',
        'hours',
        'cbr_registered',
        'valid_until',
        'academy_training_id',
        'trainer',
        'team_id',
    ];

    protected $casts = [
        'course_date' => 'date',
        'valid_until' => 'date',
        'hours' => 'integer',
        'cbr_registered' => 'boolean',
    ];

    public function academyTraining()
    {
        return $this->belongsTo(AcademyTraining::class, 'academy_training_id');
    }

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}

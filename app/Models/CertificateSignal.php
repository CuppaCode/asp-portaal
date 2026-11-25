<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateSignal extends Model
{
    use HasFactory;

    public $table = 'certificate_signals';

    protected $fillable = [
        'certificate_id',
        'level',
        'expiry_date',
        'mailing_id',
        'sent_by_user_id',
        'sent_at',
    ];

    protected $casts = [
        'level' => 'integer',
        'expiry_date' => 'date',
        'sent_at' => 'datetime',
    ];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    public function mailing()
    {
        return $this->belongsTo(Mailing::class);
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by_user_id')->withTrashed();
    }
}

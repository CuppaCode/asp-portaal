<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'old_expiry_date',
        'new_expiry_date',
        'renewed_by_user_id',
        'renewed_by_email',
        'renewal_method',
        'notes',
    ];

    protected $casts = [
        'old_expiry_date' => 'date',
        'new_expiry_date' => 'date',
    ];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    public function renewedByUser()
    {
        return $this->belongsTo(User::class, 'renewed_by_user_id');
    }
}

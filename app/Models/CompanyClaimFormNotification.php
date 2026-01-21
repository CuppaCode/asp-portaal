<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyClaimFormNotification extends Model
{
    public $table = 'company_claim_form_notifications';

    protected $fillable = [
        'company_id',
        'email',
        'name',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

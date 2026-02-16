<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyCustomClaimField extends Model
{
    protected $fillable = [
        'company_id',
        'field_type',
        'field_name',
        'field_label',
        'options',
        'is_enabled',
        'is_required',
        'include_in_notification',
        'conditional_logic',
        'display_order',
        'field_width',
        'field_group',
    ];

    protected $casts = [
        'options' => 'array',
        'conditional_logic' => 'array',
        'is_enabled' => 'boolean',
        'is_required' => 'boolean',
        'include_in_notification' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

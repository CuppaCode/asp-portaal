<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $table = 'audit_logs';

    protected $fillable = [
        'description',
        'subject_id',
        'subject_type',
        'user_id',
        'properties',
        'host',
    ];

    protected $casts = [
        'properties' => 'collection',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function getClaimAttribute() 
    {
        $claim = Claim::where('id', $this->subject_id)->get();

        return $claim;
    }

    public function getClaimAssigneeAttribute() 
    {
        $claim = Claim::where('id', $this->subject_id)->get();
        
        $user = Contact::where('user_id', $claim[0]['assignee_id'])->get();

        return $user;
    }

    public function getCompanyAttribute() 
    {
        $company = Company::where('id', $this->Claim[0]['company_id'])->get();

        return $company;
    }
}



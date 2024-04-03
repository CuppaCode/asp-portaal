<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SLA extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'SLA';

    public const REPORT_SELECT = [
        'year'   => 'Per jaar',
        'halfyear'    => 'Per halfjaar',
        'quarterly' => 'Per kwartaal',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

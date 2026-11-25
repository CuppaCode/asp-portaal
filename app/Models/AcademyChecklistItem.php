<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademyChecklistItem extends Model
{
    use Auditable, HasFactory;

    public $table = 'academy_checklist_items';

    protected $fillable = [
        'subject_type',
        'subject_id',
        'step',
        'item_key',
        'period',
        'due_date',
        'done_at',
        'done_by_user_id',
        'not_applicable',
        'not_applicable_reason',
        'mailing_id',
        'note',
    ];

    protected $casts = [
        'due_date' => 'date',
        'done_at' => 'datetime',
        'not_applicable' => 'boolean',
        'step' => 'integer',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function doneBy()
    {
        return $this->belongsTo(User::class, 'done_by_user_id')->withTrashed();
    }

    public function mailing()
    {
        return $this->belongsTo(Mailing::class);
    }

    /**
     * Done or not needed: the item no longer blocks the next step.
     */
    public function isResolved(): bool
    {
        return $this->done_at !== null || $this->not_applicable;
    }

    public function isOpen(): bool
    {
        return !$this->isResolved();
    }

    /**
     * Definition from config/academy.php.
     */
    public function definition(): ?array
    {
        $subject = match ($this->subject_type) {
            Company::class => 'company',
            Certificate::class => 'certificate',
            default => 'training',
        };

        return config("academy.items.{$subject}.{$this->item_key}");
    }
}

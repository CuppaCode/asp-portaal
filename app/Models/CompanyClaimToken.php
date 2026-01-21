<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyClaimToken extends Model
{
    public $table = 'company_claim_tokens';

    protected $fillable = [
        'company_id',
        'token',
        'label',
        'is_active',
        'submission_count',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'submission_count' => 'integer',
        'last_used_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Generate a unique token
     */
    public static function generateToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('token', $token)->exists());

        return $token;
    }

    /**
     * Increment usage counter and update last used timestamp
     */
    public function incrementUsage(): void
    {
        $this->increment('submission_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get the full URL for this token
     */
    public function getUrlAttribute(): string
    {
        return url('/claim-form/' . $this->token);
    }
}

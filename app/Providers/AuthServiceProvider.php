<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gate for approving draft claims
        Gate::define('approve_draft_claim', function ($user, $claim) {
            // Admin and Agent can approve any draft
            if ($user->is_admin || $user->roles()->where('title', 'Agent')->exists()) {
                return true;
            }
            
            // Regular users can only approve drafts from their own team
            // Load company relationship if not already loaded
            if (!$claim->relationLoaded('company')) {
                $claim->load('company');
            }
            
            return $claim->company && $user->team_id === $claim->company->team_id;
        });
    }
}

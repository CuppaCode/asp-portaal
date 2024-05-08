<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Claim;
use App\Models\Company;
use App\Models\Contact;

trait MultiTenantModelTrait
{
    public static function bootMultiTenantModelTrait()
    {
        if (! app()->runningInConsole() && auth()->check()) {
            $canAssignCompany = auth()->user()->can('assign_company');
            static::creating(function ($model) use ($canAssignCompany) {
                // Prevent admin from setting his own id - admin entries are global.
                // If required, remove the surrounding IF condition and admins will act as users
                if (! $canAssignCompany) {
                    $model->team_id = auth()->user()->team_id;
                } elseif ($canAssignCompany) {
                    if($model->table == 'claims') {
                        $company = Company::where('id', $model->company_id)->get('team_id')->first();
                        $model->team_id = $company->team_id;
                    }
                    elseif ($model->table == 'tasks'){
                        $user = Contact::where('user_id', $model->user_id)->get('team_id')->first();
                        if (isset($user->team_id)) {
                            $model->team_id = $user->team_id;
                        } else {
                            $model->team_id = auth()->user()->team_id;
                        }
                    }
                }
            });
            if (! $canAssignCompany) {
                static::addGlobalScope('team_id', function (Builder $builder) {
                    $field = sprintf('%s.%s', $builder->getQuery()->from, 'team_id');

                    $builder->where($field, auth()->user()->team_id)->orWhereNull($field);
                });
            }
        }
    }
}

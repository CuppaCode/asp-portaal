<?php

namespace App\Models;

use App\Notifications\VerifyUserNotification;
use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, Auditable, HasFactory, HasApiTokens;

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function canAssignCompany()
    {
        return $this->can('assign_company');
    }

    public function isAdminOrAgent()
    {
        
        $roles = $this->roles()->get();

        if( $roles->contains(1) ){
            
            return true;

        }

        if ( $roles->contains(3) ){

            return true;

        }

        return false;

    }

    public function getIsSuperAdminAttribute()
    {
        $superAdminEmails = [
            'jeffrey@cuppacode.nl'
        ];

    // IPV4
        $superAdminIPs = [
            '188.91.67.52', //Erik home
            '82.168.180.202', // Jeffrey home
            '127.0.0.1' //locally
        ];

        $IP = null;

        // if user from the share internet   
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {   
            $IP = $_SERVER['HTTP_CLIENT_IP'];   
        }   
        //if user is from the proxy   
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   
            $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];   
        }   
        //if user is from the remote address   
        else{   
            $IP = $_SERVER['REMOTE_ADDR'];   
        }   

        if (!$this->isAdmin) return false;

        if (!in_array($this->email, $superAdminEmails)) return false;

        if (!in_array($IP, $superAdminIPs)) return false;

        return true;

    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::created(function (self $user) {
            $registrationRole = config('panel.registration_default_role');
            if (! $user->roles()->get()->contains($registrationRole)) {
                $user->roles()->attach($registrationRole);
            }
            
        });
    }

    public function userNotes()
    {
        return $this->hasMany(Note::class, 'user_id', 'id');
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function contact()
    {
        return $this->hasOne(Contact::class);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Contact;
use App\Models\Team;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

         /**
          * Create a new user instance after a valid registration.
          *
          * @param  array  $data
          * @return \App\User
          */
         protected function create(array $data)
         {
             $user = User::create([
                 'name'     => $data['name'],
                 'email'    => $data['email'],
                 'password' => Hash::make($data['password']),
                 'team_id'  => request()->input('team', null)
             ]);

            if (! request()->has('team')) {
                $team = Team::create([
                    'owner_id' => $user->id,
                    'name'     => $data['email'],
                ]);

                $user->update(['team_id' => $team->id]);
            }

            if (request()->has('contact')) {
            
                $contact = Contact::find(request()->input('contact'));

                if(!isset($contact->user_id)) {
                    
                    $contact->user_id = $user->id;
                    $contact->save();
                    
                }
            
            }

            if (request()->has('team')) {
                
                $team = Team::find(request()->input('team'));

                if(!isset($team->owner_id)) {

                    $team->update(['owner_id' => $user->id]);

                }

            }

             

             return $user;
         }

    public function showRegistrationForm()
    {
        if (request()->has('signature') && ! request()->hasValidSignature()) {
            return redirect()->route('register');
        }

        return view('auth.register');
    }
}

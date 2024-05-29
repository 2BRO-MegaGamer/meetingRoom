<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\CheckUnique;
use App\Http\Controllers\Controller;
use App\Http\Controllers\generate_UUID;
use App\Http\Controllers\getcilentIPController;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

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
    protected $redirectTo = '/home';

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
        $validate = Validator::make($data, [
            'firstName' => ['required', 'string', 'max:64'],
            'lastName' => ['required', 'string', 'max:64'],
            'userName' => ['required', 'string',  'max:64','unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['required', 'string', 'min:10', 'max:11', 'unique:users'],
            'gender' => 'required',
            'password' => ['required', 'string', 'min:8', 'confirmed',Rules\Password::defaults()],
            'password_confirmation' => 'required',
        ]);
        return $validate;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $ip = (new getcilentIPController)->getClientIps();
        $uuid_12 = (new generate_UUID)->my_uniqe_uuid_generate(12);

        if ((new CheckUnique)->check_id($uuid_12) != "true") {
            $i = 0;
            while ($i < 20) {
                $uuid_12 = (new generate_UUID)->my_uniqe_uuid_generate(12);
                if ((new CheckUnique)->check_id($uuid_12) == "true") {
                    $i = 20;
                }else{
                    $i++;
                }
            }
        }
        return User::create([
            'id' => $uuid_12,
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'userName' => $data['userName'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'gender' => $data['gender'],
            'password' => Hash::make($data['password']),
            'last_ip' => $ip,
            'status' => "Offline",
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __destruct()
    {
        (new UserStatusController)->change_my_statuse();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function get_secure_information($id) {
        $information = User::where("id",$id)->get(['email','phone_number']);
        $phone_number = $information[0]->phone_number;
        $start_phoneNumber = $this->get_string_star(strlen($phone_number)-2);
        $final_result_phone =$start_phoneNumber . $phone_number[strlen($phone_number)-2] . $phone_number[strlen($phone_number)-1];
        $email = $information[0]->email;
        $full_email = explode("@",$email);
        $first_part = $full_email[0];
        $first_star =$this->get_string_star(strlen($first_part)-1);
        $first_final = $first_part[0] . $first_star ."@";
        $second_part = explode(".",$full_email[1])[0];
        $second_star =$this->get_string_star(strlen($second_part)-1);
        $second_final = $second_part[0] . $second_star .".";
        $last_part = explode(".",$full_email[1])[1];
        $last_star =$this->get_string_star(strlen($last_part)-1);
        $last_final = $last_part[0] . $last_star;
        $final_email = $first_final . $second_final . $last_final;
        return([$final_result_phone,$final_email]);
    }
    private function get_string_star($len)  {
        $result = '';
        for ($i=0; $i < $len; $i++) { 
            $result = $result ."*";
        }
        return $result;
    }
}

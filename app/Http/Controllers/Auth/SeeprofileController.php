<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeeprofileController extends Controller
{

    public function seeprofile(){

        return view('auth.profile');
    }
    public function UserName_bio_img_change(Request $request)
    {

    }
}

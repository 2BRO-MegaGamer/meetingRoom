<?php

namespace App\Http\Controllers;

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
    public function change_audio(){
        // $files = Storage::files("public/z/");
        // for ($i=0; $i < count($files); $i++) { 
        //     $file_array = Storage::get($files[$i]);
        //     $old_name = explode(".",$files[$i])[0];

        //     Storage::move($files[$i], ($old_name.".wav"));
        // }
        // dd($files , $file_array);
        dd("test");
    }
}

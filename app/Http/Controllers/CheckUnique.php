<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class CheckUnique extends Controller
{
    public function check_if_unique(Request $request) {
        $array_result = ["userName"=>"false","email"=>"false","phone_number"=>"false"];

        $validator_userName = Validator::make($request->all(), [
            'userName' => 'unique:users'
        ]);

        $validator_email = Validator::make($request->all(), [
            'email' => 'unique:users'
        ]);
        $phone_number['phone_number'] = substr($request->phone_number,1);
        $validator_phoneNumber = Validator::make($phone_number, [
            'phone_number' => 'unique:users'
        ]);


        if ($validator_userName->passes()) {
            $array_result['userName'] = "true";
        }
        if ($validator_email->passes()) {
            $array_result['email'] = "true";
        }
        if ($validator_phoneNumber->passes()) {
            $array_result['phone_number'] = "true";
        }
        return $array_result;
    }
    public function check_id($id) {
        $id_validate = ['id'=>$id];
        $validator = Validator::make($id_validate, [
            'id' => 'unique:users'
        ]);
        if ($validator->passes()) {
            return "true";
        }else{
            return "false";
        }
    }
}

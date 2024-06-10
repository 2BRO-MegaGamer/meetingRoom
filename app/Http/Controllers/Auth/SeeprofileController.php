<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use App\Models\Rooms;
use Illuminate\Http\Request;

class SeeprofileController extends Controller
{
    public function seeprofile(){
        $user_information = (new HomeController)->get_secure_information(auth()->user()->id);
        $rooms = auth()->user()->rooms;
        $room_detail=[];
        if ($rooms != null) {
            $rooms_array = explode(",",$rooms);
            for ($i=0; $i < count($rooms_array); $i++) {
                $detail = Rooms::where("id",$rooms_array[$i])->get(['creator_id','room_name','room_uuid','type','status','created_at']);
                $room_detail[$rooms_array[$i]] = $detail[0];
            }
        }
        return view('auth.profile')->with(["room_detail"=>$room_detail,"user_information"=>$user_information]);
    }
    public function change_profile_detail(Request $request) {
        
    }
    public function change_rooms_detail(Request $request) {
        
    }


    // public function remove_from_user_tabel($room_uuid) {
    //     $room_info = Rooms::where('room_uuid',$room_uuid)->get(['MOD_member']);
    //     $string_id = null;
    //     if ($room_info[0]->MOD_member != null) {
    //         $get_ids = explode(",",$room_info[0]->MOD_member);
    //         for ($i=0; $i < count($get_ids); $i++) { 
    //             if ($get_ids[$i] == $id) {
    //                 unset($get_ids[$i]);
    //             }
    //         }
    //         foreach ($get_ids as $ids) {
    //             if ($string_id == "") {
    //                 $string_id = $ids;
    //             }else{
    //                 $string_id = $string_id . "," . $ids;
    //             }
    //         }
    //     }
    //     Rooms::where('room_uuid',$room_uuid)->update([
    //         'MOD_member'=> $string_id
    //     ]);
    // }
    // public function delete_room($room_uuid) {
    //     $room_info = Rooms::where('room_uuid',$room_uuid)->get(['MOD_member']);
    //     $string_id = null;
    //     if ($room_info[0]->MOD_member != null) {
    //         $get_ids = explode(",",$room_info[0]->MOD_member);
    //         for ($i=0; $i < count($get_ids); $i++) { 
    //             if ($get_ids[$i] == $id) {
    //                 unset($get_ids[$i]);
    //             }
    //         }
    //         foreach ($get_ids as $ids) {
    //             if ($string_id == "") {
    //                 $string_id = $ids;
    //             }else{
    //                 $string_id = $string_id . "," . $ids;
    //             }
    //         }
    //     }
    //     Rooms::where('room_uuid',$room_uuid)->update([
    //         'MOD_member'=> $string_id
    //     ]);
    // }
}

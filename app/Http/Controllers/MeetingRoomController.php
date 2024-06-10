<?php

namespace App\Http\Controllers;

use App\Models\Rooms;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class MeetingRoomController extends Controller
{
    public function create_page(){
        $message = session()->get("message");
        $uuid = Str::uuid();
        if ($message != null) {
            return view('meetingRoom.create',["roomID" => $uuid,"message"=>$message[1]]);
        }
        return view('meetingRoom.create',["roomID" => $uuid]);
    }
    public function create_room(Request $request){
        $no_rooms = $this->have_Rooms(auth()->id());
        if (count($no_rooms) < 3) {
            $new_rooms = new Rooms;
            $new_rooms->creator_id = auth()->id();
            $new_rooms->room_name = $request->room_name;
            $new_rooms->room_uuid = $request->room_uuid;
            $new_rooms->type = $request->type_Room;
            $new_rooms->save();
            $this->add_to_user_tabel($new_rooms->id);
            return redirect('/mR/joinTo/'.$request->room_uuid)->with(['message'=>[false,'Click Join Room, if u dont want to change your name',$new_rooms->room_uuid]]);
        }else{
            return redirect('/mR/create')->with(['message'=>[false,'You cannot make more than 3 rooms']]);
        }
    }
    private function add_to_user_tabel($room_id) {
        $all_rooms = User::where("id",auth()->user()->id)->get('rooms')[0]->rooms;
        if ($all_rooms == null) {
            User::where("id",auth()->user()->id)->update([
                "rooms"=>$room_id
            ]);
        }else{
            $all_rooms_array = explode(",",$all_rooms);
            if (array_search($room_id,$all_rooms_array) == "") {
                $all_rooms = $all_rooms .",". $room_id;
                User::where("id",auth()->user()->id)->update([
                    "rooms"=>$all_rooms
                ]);
            }
        }
    }
    public function get_permission($room_UUID,$user_id) {
        $get_rooms = Rooms::where('room_uuid',$room_UUID)->get();
        $info_user_inRoom = $this->check_user_status_in_room($get_rooms[0],$user_id);
        $Permission = '';
        
        foreach ($info_user_inRoom as $perm => $Bool_ids) {
            if ($Bool_ids === true) {
                $Permission = $perm;
                break;
            }
        }
        if ($Permission == '') {
            $Permission = 'MEMBER';
        }
        return $Permission;
    }
    public function genarate_room(Request $request){
        $my_custom_name = $request->my_custom_name;
        $roomID = $request->room_uuid;
        $get_rooms = Rooms::where('room_uuid',$roomID)->get();
        if (count($get_rooms) == 0) {
            return redirect('/mR/joinTo/'.$roomID)->with(['message'=>[false,'There is no room with this id',$roomID]]);
        }else{
            $info_user_inRoom = $this->check_user_status_in_room($get_rooms[0],auth()->id());
            $member_check = $this->is_member_in_list($get_rooms[0],"Members",auth()->id());
            $dublicate_detect = ($member_check == true)?$member_check:false;
            $room_type = $get_rooms[0]->type;
            $host_details = ($this->get_host_information($roomID));
            $Permission = $this->get_permission($roomID,auth()->id());
            if ($room_type != 'public') {
                $message = '';
                if ($info_user_inRoom['accept_m'] != true && $info_user_inRoom['HOST'] != true && $info_user_inRoom['MOD'] != true) {
                    if ($info_user_inRoom['wait_to_accept'] === true) {
                        $message = 'Your membership request has been sent,pls wait';
                    }else{
                        $this->make_user_visible_in_wait_list($roomID);
                        $message = 'You are not a member of this group Your request has been sent to the administrators';
                    }
                    return redirect('/mR/joinTo/'.$roomID)->with(['message'=>[false,$message,$roomID]]);
                }
            }
            $this->make_user_visible_in_Member_list($get_rooms[0]);
            return view('meetingRoom.Room',[
                'my_custom_name'=>$request->my_custom_name,
                'roomUUID'=>$roomID,'roomID'=>$get_rooms[0]->id,
                'Permission'=>$Permission,
                'duplicate'=>$dublicate_detect,
                "HOST_userName"=> $host_details[0],
                "HOST_id"=> $host_details[1],
            ]);
        }
    }
    public function get_host_information($roomUUID){
        $room_creator_id =  Rooms::where('room_uuid',$roomUUID)->get('creator_id')[0]->creator_id;
        $host_information = User::where('id', $room_creator_id)->get('UserName')[0];
        return [$host_information->UserName,$room_creator_id];
    }
    public function make_user_visible_in_wait_list($room_id){
        $room_info = Rooms::where('room_uuid',$room_id)->get();
        if (!($room_info[0]->wait_to_accept == null)) {
            $string_ids = $room_info[0]->wait_to_accept . "," . auth()->id();
        }else{
            $string_ids = (string) auth()->id();
        }
        Rooms::where('room_uuid',$room_id)->update([
            'wait_to_accept'=> $string_ids
        ]);
    }
    public function make_user_visible_in_Member_list($room_info){
        $member_check = $this->is_member_in_list($room_info,"Members",auth()->id());
        $need_update = true;
        $string_ids='';
        if (!($member_check === true)) {
            if (is_string($member_check)) {
                $string_ids = $member_check . "," . auth()->id();
            }else{
                $string_ids = (string) auth()->id();
            }
        }else{
            $need_update = false;
        }
        if ($need_update) {
            Rooms::where('room_uuid',$room_info->room_uuid)->update([
                'Members'=> $string_ids
            ]);
        }
    }
    public function joinTo_page(string $roomID){
        $message = session()->get("message");
        if ($message != null) {
            if ($message[0] === false) {
                return view('meetingRoom.join',['roomID'=>$message[2],'message'=> $message[1]]);
            }else{
                return view('meetingRoom.join',['roomID'=>$roomID]);
            }
        }else{
            return view('meetingRoom.join',['roomID'=>$roomID]);
        }
    }
    public function check_user_status_in_room($roomID,$user_id){
        if (is_string($roomID)) {
            $get_rooms = Rooms::where('room_uuid',$roomID)->get();
            $roomID = $get_rooms[0];
        }
        $host_check = $this->is_member_host($roomID,$user_id);
        $moderator_check = $this->is_member_in_list($roomID,array_keys($roomID->toArray())[5],$user_id);
        $accept_m_check = $this->is_member_in_list($roomID,array_keys($roomID->toArray())[7],$user_id);
        $wait_to_accept_check = $this->is_member_in_list($roomID,array_keys($roomID->toArray())[6],$user_id);
        $all_data = ["HOST"=>$host_check,"MOD"=>$moderator_check,"accept_m"=>$accept_m_check,"wait_to_accept"=>$wait_to_accept_check];
        return $all_data;
    }
    public function is_member_host($roomInfo,$user_id){
        if (($roomInfo->creator_id) == $user_id) {
            return true;
        }else{
            return false;
        }
    }
    public function is_member_in_list($room_INFO,$column,$user_id){
        $bool_am_i = false;
        if ($room_INFO->$column === null) {
            return null;
        }else{
            $list_members = explode(",",$room_INFO->$column);
            for ($i=0; $i < count($list_members); $i++) { 
                if ($list_members[$i] == $user_id) {
                    $bool_am_i = true;
                }
            }
            if (!$bool_am_i) {
                // array_push($list_members, (string) auth()->id());
                $string_ids = '';
                for ($i=0; $i < count($list_members); $i++) { 
                    if ($string_ids == '') {
                        $string_ids = $list_members[$i];
                    }else{
                        $string_ids = $string_ids . ",". $list_members[$i];
                    }
                }
                return $string_ids;
            }else{
                return true;
            }
        }
        
    }
    public function have_Rooms($my_id){
        $have_rooms = Rooms::where("creator_id",$my_id)->get();
        return $have_rooms;
    }
    public function member_disconnect($room_uuid,$id){
        $room_info = Rooms::where('room_uuid',$room_uuid)->get(['Members']);
        $string_id = '';
        if ($room_info[0]->Members != null) {
            $get_ids = explode(",",$room_info[0]->Members);
            for ($i=0; $i < count($get_ids); $i++) { 
                if ($get_ids[$i] == $id) {
                    unset($get_ids[$i]);
                }
            }
            foreach ($get_ids as $ids) {
                if ($string_id == "") {
                    $string_id = $ids;
                }else{
                    $string_id = $string_id . "," . $ids;
                }
            }
            $string_id = ($string_id== "")?null:$string_id;
        }else{
            $string_id = null;
        }
        Rooms::where('room_uuid',$room_uuid)->update([
            'Members'=> $string_id
        ]);
        return $string_id;
    }
    public function remove_from_mod_member($room_uuid,$id) {
        $room_info = Rooms::where('room_uuid',$room_uuid)->get(['MOD_member']);
        $string_id = null;
        if ($room_info[0]->MOD_member != null) {
            $get_ids = explode(",",$room_info[0]->MOD_member);
            for ($i=0; $i < count($get_ids); $i++) { 
                if ($get_ids[$i] == $id) {
                    unset($get_ids[$i]);
                }
            }
            foreach ($get_ids as $ids) {
                if ($string_id == "") {
                    $string_id = $ids;
                }else{
                    $string_id = $string_id . "," . $ids;
                }
            }
        }
        Rooms::where('room_uuid',$room_uuid)->update([
            'MOD_member'=> $string_id
        ]);
    }
    public function remove_from_banned_member($room_uuid,$id) {
        $room_info = Rooms::where('room_uuid',$room_uuid)->get(['banned_m']);
        $string_id = '';
        if ($room_info[0]->banned_m != null) {
            $get_ids = explode(",",$room_info[0]->banned_m);
            for ($i=0; $i < count($get_ids); $i++) { 
                if ($get_ids[$i] == $id) {
                    unset($get_ids[$i]);
                }
            }
            foreach ($get_ids as $ids) {
                if ($string_id == "") {
                    $string_id = $ids;
                }else{
                    $string_id = $string_id . "," . $ids;
                }
            }
        }else{
            $string_id = null;
        }
        Rooms::where('room_uuid',$room_uuid)->update([
            'banned_m'=> $string_id
        ]);
    }
    public function remove_from_kicked_member($room_uuid,$id) {
        $room_info = Rooms::where('room_uuid',$room_uuid)->get(['removed_m']);
        $string_id = '';
        if ($room_info[0]->removed_m != null) {
            $get_ids = explode(",",$room_info[0]->removed_m);
            for ($i=0; $i < count($get_ids); $i++) { 
                if ($get_ids[$i] == $id) {
                    unset($get_ids[$i]);
                }
            }
            foreach ($get_ids as $ids) {
                if ($string_id == "") {
                    $string_id = $ids;
                }else{
                    $string_id = $string_id . "," . $ids;
                }
            }
        }else{
            $string_id = null;
        }
        Rooms::where('room_uuid',$room_uuid)->update([
            'removed_m'=> $string_id
        ]);
    }
    public function get_members_peer_id(Request $request){
        $room_info_req = $request->room_info;
        $ROOM_ID = $room_info_req[1];
        $room_info = Rooms::where('id',$ROOM_ID)->get('Members');
        $room_string_ids = $room_info[0]->Members;
        $room_array_ids = explode(",",$room_string_ids);
        $peer_ids=[];
        for ($i=0; $i < count($room_array_ids); $i++) { 
            array_push($peer_ids,($room_array_ids[$i]."_".$ROOM_ID));
        }
        return($peer_ids);

    }
}












































// if ($roomID[0]->wait_to_accept == null) {
//     Rooms::where('room_uuid',$request->room_uuid)->update([
//         'wait_to_accept' => ''. auth()->id()
//     ]);
// }else{
//     $wait_member = explode(",",$roomID[0]->wait_to_accept);
//     array_push($wait_member, (string) auth()->id());
//     $string_ids = '';
//     for ($i=0; $i < count($wait_member); $i++) { 
//         if ($wait_member[$i] != auth()->id()) {
//             if ($string_ids == '') {
//                 $string_ids = $wait_member[$i];
//             }else{
//                 $string_ids = $string_ids . ",". $wait_member[$i];
//             }
//         }
//     }
//     if ($string_ids != '') {
//         Rooms::where('room_uuid',$request->room_uuid)->update([
//             'wait_to_accept' => $string_ids
//         ]);
//     }
// }
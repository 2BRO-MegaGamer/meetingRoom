<?php
namespace App\Http\Controllers\auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MeetingRoomController;
use App\Models\Rooms;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class manageRoomController extends Controller
{
    public function show_rooms(){
        $all_rooms = (new MeetingRoomController)->have_Rooms(auth()->user()->id);
        return view("auth.RoomManage.select_room")->with(['room_info'=>$all_rooms]);
    }
    public function show_ban_list(string $room_id) {
        $banned_m = Rooms::where('id',$room_id)->get('banned_m')[0]->banned_m;
        $each_info = [];
        if ($banned_m != null) {
            $banned_m_array = explode(",",$banned_m);
            for ($i=0; $i < count($banned_m_array); $i++) { 
                $user_info = User::where("id",$banned_m_array[$i])->get(['firstName','lastName','userName','gender','status'])[0];
                $each_info[$banned_m_array[$i]] = $user_info;
            }
        }
        return view("auth.RoomManage.banlist_form")->with(["banned_m"=>$each_info,"room_id"=>$room_id]);
    }
    public function show_allowedMember_list(string $room_id) {
        $accept_m = Rooms::where('id',$room_id)->get('accept_m')[0]->accept_m;
        $each_info = [];
        if ($accept_m != null) {
            $accept_m_array = explode(",",$accept_m);
            for ($i=0; $i < count($accept_m_array); $i++) { 
                $user_info = User::where("id",$accept_m_array[$i])->get(['firstName','lastName','userName','gender','status'])[0];
                $each_info[$accept_m_array[$i]] = $user_info;
            }
        }
        return view("auth.RoomManage.allowedMember")->with(["accept_m"=>$each_info,"room_id"=>$room_id]);
    }
    public function show_manageMember_list(string $room_id) {
        $Members = Rooms::where('id',$room_id)->get('Members')[0]->Members;
        $each_info = [];
        if ($Members != null) {
            $Members_array = explode(",",$Members);
            for ($i=0; $i < count($Members_array); $i++) { 
                $user_info = User::where("id",$Members_array[$i])->get(['firstName','lastName','userName','gender','status'])[0];
                $each_info[$Members_array[$i]] = $user_info;
            }
        }
        return view("auth.RoomManage.member_form")->with(["Members"=>$each_info,"room_id"=>$room_id]);
    }
    public function show_mod_list(string $room_id) {
        $MOD_member = Rooms::where('id',$room_id)->get('MOD_member')[0]->MOD_member;
        $each_info = [];
        if ($MOD_member != null) {
            $MOD_member_array = explode(",",$MOD_member);
            for ($i=0; $i < count($MOD_member_array); $i++) { 
                $user_info = User::where("id",$MOD_member_array[$i])->get(['firstName','lastName','userName','gender','status'])[0];
                $each_info[$MOD_member_array[$i]] = $user_info;
            }
        }
        return view("auth.RoomManage.modlist_form")->with(["MOD_member"=>$each_info,"room_id"=>$room_id]);
    }
    public function remove_ban_mod(Request $request) {
        switch ($request->ban_mod) {
            case 'banlist':
                $ban_or_mod = "banned_m";
                break;
            case 'modlist':
                $ban_or_mod = "MOD_member";
                break;
            case 'allowedlist':
                $ban_or_mod = "accept_m";
                break;
            case 'memberlist':
                $ban_or_mod = "Members";
                break;
        }
        $ban_mod_member = Rooms::where('id',$request->room_info)->get($ban_or_mod)[0]->$ban_or_mod;
        if ($ban_mod_member != null) {
            $ban_mod_member = explode(",",$ban_mod_member);
            $search_array = array_search($request->id,$ban_mod_member);
            unset($ban_mod_member[$search_array]);
            $result ="";
            foreach ($ban_mod_member as $key => $value) {
                if ($result == "") {
                    $result = $value;
                }else{
                    $result = $result . ",". $value;
                }
            }
        }
        $result = ($result=="")?null:$result;
        Rooms::where("id",$request->room_info)->update([
            $ban_or_mod => $result
        ]);
        return "done";
    }
    public function remvoe_room(Request $request) {
        $room_id = $request->room_info;
        $confirm_text = $request->confirm_text;
        if ($confirm_text == ("remove room with id " .$room_id)) {
            Rooms::where("id",$room_id)->delete();
            $user_rooms_ids = User::where("id",auth()->user()->id)->get("rooms")[0]->rooms;
            if ($user_rooms_ids != null) {
                $user_rooms_ids = explode(",",$user_rooms_ids);
                $search_array = array_search($room_id,$user_rooms_ids);
                unset($user_rooms_ids[$search_array]);
                $result ="";
                foreach ($user_rooms_ids as $key => $value) {
                    if ($result == "") {
                        $result = $value;
                    }else{
                        $result = $result . ",". $value;
                    }
                }
            }
            $result = ($result=="")?null:$result;
            User::where("id",auth()->user()->id)->update(['rooms'=>$result]);
            return redirect("/manageRoom")->withErrors(["Room deleted"=>"Room deleted successfully"]);
        }else{
            return redirect("/manageRoom")->withErrors(["not Confirmed"=>"pls type text right"]);
        }
    }
    public function accept_denny_refresh_member(Request $request){

        $room_info = Rooms::where("id",$request->room_info)->get(['wait_to_accept','deny_m','accept_m']);
        $wait_to_accept = $room_info[0]->wait_to_accept;
        if ($request->accept_reject == "refresh") {
            return $wait_to_accept;
        }
        $wait_to_accept_ids = [];
        if ($wait_to_accept != null) {
            $wait_to_accept_ids = explode(",",$room_info[0]->wait_to_accept);
            $array_search = array_search($request->id,$wait_to_accept_ids);
            $selected_id = $wait_to_accept_ids[$array_search];
            unset($wait_to_accept_ids[$array_search]);
            $result ="";
            foreach ($wait_to_accept_ids as $key => $value) {
                if ($result == "") {
                    $result = $value;
                }else{
                    $result = $result . ",". $value;
                }
            }
            $result = ($result=="")?null:$result;
            Rooms::where("id",$request->room_info)->update([
                'wait_to_accept'=>$result
            ]);
            if ($request->accept_reject == "accept") {
                if ($room_info[0]->accept_m != null) {
                    $db_text = "UPDATE rooms SET accept_m = CONCAT(accept_m,',' ,'". ($selected_id)."') WHERE id = '".$request->room_info."';";
                    DB::select($db_text);
                }else{
                    Rooms::where("id",$request->room_info)->update([
                        'accept_m'=>$selected_id
                    ]);
                }
            }
            if($request->accept_reject == "reject"){
                if ($room_info[0]->deny_m != null) {
                    $db_text = "UPDATE rooms SET deny_m = CONCAT(deny_m,',' ,'". ($selected_id)."') WHERE id = '".$request->room_info."';";
                    DB::select($db_text);
                }else{
                    Rooms::where("id",$request->room_info)->update([
                        'deny_m'=>$selected_id
                    ]);
                }
            }
        }
        return $result;
    }
    public function warning_member(Request $request) {
        $room_info = Rooms::where("id",$request->room_info)->get('warn_m');
        $date = date("Y-m-d H:i:s");
        $text_log = '"'.$date.'":{"target_name":"'.auth()->user()->userName.'", "target_id":"'.$request->id.'","text":"'.$request->warning_text.'"}';
        if ($room_info[0]->warn_m != null) {
            $db_text = "UPDATE rooms SET warn_m = CONCAT(warn_m,',' ,'". ($text_log)."') WHERE id = '".$request->room_info."';";
            DB::select($db_text);
        }else{
            Rooms::where("id",$request->room_info)->update([
                'warn_m'=>$text_log
            ]);
        }
    }
    public function ban_member(Request $request) {

        $room_db = Rooms::where("id",$request->room_info)->get(['banned_m','room_uuid']);
        $all_banned_member_string = $room_db[0]->banned_m;
        $room_uuid = $room_db[0]->room_uuid;
        if ($all_banned_member_string == null) {
            Rooms::where("id",$request->room_info)->update([
                "banned_m"=>$request->id
            ]);
        }else{
            $all_banned_member_array = explode(",",$all_banned_member_string);
            if (array_search($request->id,$all_banned_member_array) == "") {
                $all_banned_member_string = $all_banned_member_string .",". $request->id;
                Rooms::where("id",$request->room_info)->update([
                    "banned_m"=>$all_banned_member_string
                ]);
            }
        }
        (new MeetingRoomController)->member_disconnect($room_uuid,$request->id);

    }
}

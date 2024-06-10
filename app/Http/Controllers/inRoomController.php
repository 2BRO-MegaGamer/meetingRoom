<?php

namespace App\Http\Controllers;

use App\Models\Rooms;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use stdClass;
class inRoomController extends Controller
{
    public function members_cannot_make_connection_to_member(Request $request){
        if ($request->connected == 'false') {
            $members = (Rooms::where('room_uuid',$request->room_uuid)->get())[0]->Members;
            $members = explode(",",$members);
            $m_id_string = '';
            for ($i=0; $i < count($members); $i++) {
                if ($members[$i] == $request->member_id) {
                    array_splice($members,$i,1);
                }
            }
            for ($i=0; $i < count($members) ; $i++) {
                if ($m_id_string == '') {
                    $m_id_string = $members[$i];
                }else{
                    $m_id_string = $m_id_string . "," . $members[$i];
                }
            }
            Rooms::where('room_uuid',$request->room_uuid)->update([
                'Members'=>$m_id_string
            ]);
        }
    }
    public function HOST_MOD_cando(Request $request) {
        $user_permission = (new MeetingRoomController)->get_permission($request->room_info[0],$request->id);
        $my_permission = (new MeetingRoomController)->get_permission($request->room_info[0],auth()->user()->id);
        if (($my_permission == "MOD" && $user_permission == "HOST")||($my_permission == "MOD" && $user_permission == "MOD")) {
            return false;
        }
        // return [$user_permission , $my_permission,$request->action];
        switch ($request->action) {
            case "kick":
                $all_removed_member_string = Rooms::where("id",$request->room_info[1])->get('removed_m')[0]->removed_m;
                if ($all_removed_member_string == null) {
                    Rooms::where("id",$request->room_info[1])->update([
                        "removed_m"=>$request->id
                    ]);
                }else{
                    $all_removed_member_array = explode(",",$all_removed_member_string);
                    if (array_search($request->id,$all_removed_member_array) == "") {
                        $all_removed_member_string = $all_removed_member_string .",". $request->id;
                        Rooms::where("id",$request->room_info[1])->update([
                            "removed_m"=>$all_removed_member_string
                        ]);
                    }
                }
                (new MeetingRoomController)->member_disconnect($request->room_info[0],$request->id);
                break;
            case "ban":
                $all_banned_member_string = Rooms::where("id",$request->room_info[1])->get('banned_m')[0]->banned_m;
                if ($all_banned_member_string == null) {
                    Rooms::where("id",$request->room_info[1])->update([
                        "banned_m"=>$request->id
                    ]);
                }else{
                    $all_banned_member_array = explode(",",$all_banned_member_string);
                    if (array_search($request->id,$all_banned_member_array) == "") {
                        $all_banned_member_string = $all_banned_member_string .",". $request->id;
                        Rooms::where("id",$request->room_info[1])->update([
                            "banned_m"=>$all_banned_member_string
                        ]);
                    }
                }
                (new MeetingRoomController)->member_disconnect($request->room_info[0],$request->id);
                break;
            case "promote":
                $all_promote_member_string = Rooms::where("id",$request->room_info[1])->get('MOD_member')[0]->MOD_member;
                if ($all_promote_member_string == null) {
                    Rooms::where("id",$request->room_info[1])->update([
                        "MOD_member"=>$request->id
                    ]);
                }else{
                    $all_promote_member_array = explode(",",$all_promote_member_string);
                    if (array_search($request->id,$all_promote_member_array) == "") {
                        $all_promote_member_string = $all_promote_member_string .",". $request->id;
                        Rooms::where("id",$request->room_info[1])->update([
                            "MOD_member"=>$all_promote_member_string
                        ]);
                    }
                }
                (new MeetingRoomController)->member_disconnect($request->room_info[0],$request->id);
                break;
            case "demote":
                (new MeetingRoomController)->remove_from_mod_member($request->room_info[0],$request->id);
                (new MeetingRoomController)->member_disconnect($request->room_info[0],$request->id);
                break;
        }
        return($request);
    }

    public function load_old_announcement(Request $request){
        $roomID = $request->room_info[0];
        $permission = (new MeetingRoomController)->get_permission($roomID,auth()->user()->id);
        $announcment_chat = "{". Rooms::where("room_uuid",$roomID)->get('announcement')[0]->announcement ."}";
        $json_announcement = json_decode($announcment_chat);
        if ($permission == "HOST" || $permission == "MOD") {
            return $announcment_chat;
        }else{
            $return_object = new stdClass();
            foreach ($json_announcement as $key => $value) {
                if ($value->mention == "@members") {
                    $return_object->$key = $value;
                }
            }
            return json_encode($return_object);
        }
    }
    public function announcement_log(Request $request){
        $date = date("Y-m-d H:i:s");
        $id = (new generate_UUID)->my_uniqe_uuid_generate(15);
        $user_id = auth()->user()->id;
        $room_uuid = $request->room_info[0];
        $text = $request->text;
        $mention = $request->log_for_peer["mention"];
        $text_log = '"'.$date.'":{"id":"'.$id.'","sender_name":"'.auth()->user()->userName.'", "sender_id":"'.$user_id.'","text":"'.$text.'","mention":"'.$mention.'","room_uuid":"'.$room_uuid.'"}';
        $is_null_sql = "SELECT id FROM rooms WHERE announcement IS NULL AND room_uuid='".$room_uuid."';";
        $is_null_value = DB::select($is_null_sql);
        if (count($is_null_value) == 0) {
            $db_text = "UPDATE rooms SET announcement = CONCAT(announcement,',' ,'". ($text_log)."') WHERE room_uuid = '".$room_uuid."';";
            DB::select($db_text);
        }else if(count($is_null_value) == 1){
            Rooms::where("room_uuid",$room_uuid)->update([
                "announcement"=>($text_log)
            ]);
        }
        return $id;
    }
    public function load_old_message(Request $request){
        $room_uuid = $request->room_info[0];
        $all_message = Rooms::where("room_uuid",$room_uuid)->get('chat_messages')[0]->chat_messages;
        $chat_message = '';
        if ($all_message != null) {
            $chat_message = "{". $all_message ."}";
        }else{
            $chat_message = "false";
        }
        return $chat_message;
    }
    public function upload_formdata(Request $request) {
        $file = $request->file("file");
        $fileName = $file->hashName();
        $fileformat = explode('.',$fileName)[1];
        $fileType = $request->file_type;
        $room_uuid = $request->room_info[0];
        $files = Storage::files('/public/uploads/'.$room_uuid);
        // VALIDATOR FILES
        $file_int_name = count($files) .".".$fileformat;
        $file->storeAs(('public/uploads/'.$room_uuid), $file_int_name);
        $id= explode('.',$file_int_name)[0];
        $this->send_file_text_log(true,[$file_int_name,$fileType],$room_uuid,$id);
        return $id;
    }
    public function upload_textmessage(Request $request) {
        $text = $request->text;
        $room_uuid = $request->room_info[0];
        $id = (new generate_UUID)->my_uniqe_uuid_generate(15);
        $this->send_file_text_log(false,$text,$room_uuid,$id);
        return $id;
    }
    private function send_file_text_log($text_file,$detail,$room_uuid,$id) {
        $text = '';
        $user_id = auth()->user()->id;
        $date = date("Y-m-d H:i:s");
        $text_file_string = '';
        $type="";
        if ($text_file) {
            $text = $detail[0];
            $text_file_string = "file";
            $type = $detail[1];
        }else{
            $text = $detail;
            $text_file_string = "text";
            $type="text";
        }
        $is_null_sql = "SELECT id FROM rooms WHERE chat_messages IS NULL AND room_uuid='".$room_uuid."';";
        $is_null_value = DB::select($is_null_sql);
        $text_log = '"'.$date.'":{"id":"'.$id.'","sender_name":"'.auth()->user()->userName.'", "sender_id":"'.$user_id.'","'.$text_file_string.'":"'.$text.'","format":"'.$text_file_string.'","type":"'.$type.'","room_uuid":"'.$room_uuid.'"}';
        if (count($is_null_value) == 0) {
            $db_text = "UPDATE rooms SET chat_messages = CONCAT(chat_messages,',' ,'". ($text_log)."') WHERE room_uuid = '".$room_uuid."';";
            DB::select($db_text);
        }else if(count($is_null_value) == 1){
            Rooms::where("room_uuid",$room_uuid)->update([
                "chat_messages"=>($text_log)
            ]);
        }
    }
    public function get_mods_info(Request $request) {
        $mods = Rooms::where("room_uuid",$request->room_info[0])->get('MOD_member')[0]->MOD_member;
        if ($mods != NULL) {
            return (explode(",",$mods));
        }else{
            return "false";
        }
    }
    public function send_announcement(Request $request){
        return $request;
        
    }
    public function get_username_from_id(Request $request){
        $username = User::where('id',$request->id)->get('userName')[0]->userName;
        $permission_inroom = (new MeetingRoomController)->get_permission($request->room_info[0],$request->id);
        return [$username,$permission_inroom];
    }
}

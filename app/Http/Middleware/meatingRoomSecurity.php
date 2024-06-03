<?php

namespace App\Http\Middleware;

use App\Http\Controllers\UserStatusController;
use App\Models\Rooms;
use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\MeetingRoomController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

use function Laravel\Prompts\alert;

class meatingRoomSecurity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function __destruct()
    {
        (new UserStatusController)->change_my_statuse();
    }
    public function handle(Request $request, Closure $next): Response
    {
        $room_uuid="";
        if (count($request->all()) == 0) {
            $pattern = "/Room\/[a-z A-Z 0-9 -]*/m";
            preg_match_all($pattern, $request->url(), $matches, PREG_SET_ORDER);
            $room_uuid = explode("/",$matches[0][0])[1];
        }else{
            if (($request->room_uuid) != "") {
                $room_uuid = $request->room_uuid;
            }else{
                $room_uuid = $request->room_info[0];
            }
        }
        if (count(Rooms::where("room_uuid",$room_uuid)->get('id')) == 0) {
            if ($request->ajax() == 1 || $request->ajax() == true) {
                return redirect("/false")->with(["message"=>"room_db_info"]);
            }
            return redirect("/")->withErrors(["Not find"=>"We could not find the room"]);
        }
        $all_banned_member_string = Rooms::where("room_uuid",$room_uuid)->get('banned_m')[0]->banned_m;
        if ($all_banned_member_string != null) {
            Log::alert($all_banned_member_string == null);
            $all_banned_member_array = explode(",",$all_banned_member_string);
            if (array_search(auth()->user()->id,$all_banned_member_array) !="") {
                if ($request->ajax() == 1 || $request->ajax() == true) {
                    return redirect("/false")->with(["message"=>"room_db_info"]);
                }
                return redirect("/")->withErrors(["banned"=>"You just get banned from this room"]);
            }
        }

        $all_removed_member_string = Rooms::where("room_uuid",$room_uuid)->get('removed_m')[0]->removed_m;
        if ($all_removed_member_string != null) {
            $all_removed_member_array = explode(",",$all_removed_member_string);
            if (array_search(auth()->user()->id,$all_removed_member_array) != "") {
                if ($request->ajax() == 1 || $request->ajax() == true) {
                    return redirect("/false")->with(["message"=>"room_db_info"]);
                }
                return redirect("/")->withErrors(["kicked"=>"You just get kicked from this room"]);
            }
        }
        if ($request->room_info != null) {
            $return_value = redirect("/");
            $room_db_info = Rooms::where("room_uuid",$room_uuid)->get();
            if (count($room_db_info) > 0) {
                if ((new MeetingRoomController)->is_member_in_list($room_db_info[0],"Members",auth()->id()) === true) {
                    return $next($request);
                }else{
                    if ($request->ajax() == 1 || $request->ajax() == true) {
                        $return_value = redirect("/false")->with(["message"=>"is_member_in_list"]);
                    }
                    return $return_value;
                }
            }else{
                if ($request->ajax()== 1 || $request->ajax() == true) {
                    $return_value = redirect("/false")->with(["message"=>"room_db_info"]);
                }
                return $return_value;
            }
        }else{
            return $next($request);
        }
    }
}

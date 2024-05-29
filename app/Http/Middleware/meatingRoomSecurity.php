<?php

namespace App\Http\Middleware;

use App\Http\Controllers\UserStatusController;
use App\Models\Rooms;
use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\MeetingRoomController;
use Illuminate\Support\Facades\Log;
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

        //CHANGE RETURN IF USING AJAX
        $return_value = redirect("/");
        $room_info = $request->room_info;
        $my_info = $request->my_info;
        if (is_string($request->room_info)) {
            $request["room_info"] = $room_info = explode(",",$room_info);
        }
        if (is_string($request->my_info)) {
            $request["my_info"] = $my_info = explode(",",$my_info);
        }

        $ROOM_ID = $room_info[1];
        $room_db_info = Rooms::where("id",(int) $ROOM_ID)->get();
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

    }
}

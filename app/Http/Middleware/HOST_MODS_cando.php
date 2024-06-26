<?php

namespace App\Http\Middleware;

use App\Http\Controllers\MeetingRoomController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HOST_MODS_cando
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $all_rooms = (new MeetingRoomController)->have_Rooms(auth()->user()->id);
        $return_value = redirect("/");
        if ($request->ajax()== 1 || $request->ajax() == true) {
            $return_value = redirect("/false")->with(["message"=>"Request Denied"]);
        }
        if ($request->my_info[1]==auth()->user()->id) {
            for ($i=0; $i < count($all_rooms); $i++) {
                if ($all_rooms[$i]->creator_id == auth()->user()->id && $request->room_info[1] == $all_rooms[$i]->id) {
                    $my_permission = (new MeetingRoomController)->get_permission($request->room_info[0],auth()->user()->id);
                    if ($my_permission == "HOST") {
                        return $next($request);
                    }
                }else{
                    $all_mods = explode(",",$all_rooms[$i]->MOD_member);
                    if (array_search(auth()->user()->id,$all_mods) != "") {
                        return $next($request);
                    }
                }
            }
        }

        return $return_value;
    }
}

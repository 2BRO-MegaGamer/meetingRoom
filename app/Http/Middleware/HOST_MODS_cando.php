<?php

namespace App\Http\Middleware;

use App\Http\Controllers\MeetingRoomController;
use Closure;
use Illuminate\Http\Request;
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
        if ($request->my_info[1]==auth()->user()->id) {
            for ($i=0; $i < count($all_rooms); $i++) {
                if ($all_rooms[$i]->creator_id == auth()->user()->id) {
                    return $next($request);
                }else{
                    $all_mods = explode(",",$all_rooms[$i]->MOD_member);
                    if (array_search(auth()->user()->id,$all_mods) != "") {
                        return $next($request);
                    }
                }
            }
        }
        if ($request->ajax()== 1 || $request->ajax() == true) {
            $return_value = redirect("/false")->with(["message"=>"room_db_info"]);
        }
        return $return_value;
    }
}

<?php

namespace App\Http\Middleware;

use App\Http\Controllers\MeetingRoomController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class manageRoomSecurity
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
        for ($i=0; $i < count($all_rooms); $i++) {
            if ($all_rooms[$i]->creator_id == auth()->user()->id && $request->room_info == $all_rooms[$i]->id) {
                return $next($request);
            }
        }
        return $return_value;
    }
}

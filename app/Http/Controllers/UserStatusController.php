<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\DB;

class UserStatusController extends Controller
{
    public function change_my_statuse(){
        if (auth()->check()) {
            DB::table('users')->where('id',auth()->user()->id)->update([
                'status' => 'Online'
            ]);
            $event_scheduler = DB::select('SELECT @@event_scheduler');
            foreach ($event_scheduler[0] as $scheduler => $ON_OFF) {
                if ($ON_OFF == 'OFF') {
                    DB::select('SET GLOBAL event_scheduler = ON;');
                }
            }
            $all_events = DB::select('SHOW EVENTS');
            $event_exist = false;
            if (count($all_events) != 0) {
                foreach ($all_events as $event) {
                    if ($event->Name == 'user_offline_'. auth()->user()->id ) {
                        $event_exist = true;
                    }
                }
            }
            if ($event_exist) {
                $this->drop_event(auth()->user()->id);
            }
            $this->create_event(auth()->user()->id);
        }
    }
    private function create_event($my_id){
        $sql_add_event = 'CREATE EVENT user_offline_'.$my_id.'
        ON SCHEDULE EVERY 1 MINUTE
        STARTS CURRENT_TIMESTAMP
		ENDS CURRENT_TIMESTAMP + INTERVAL 1 MINUTE
        DO
        UPDATE users SET status = "Offline" WHERE id="'.$my_id.'" AND status="Online";
        ';
        DB::select($sql_add_event);
    }
    private function drop_event($my_id){
        $drop_event = 'DROP EVENT user_offline_'. $my_id;
        DB::select($drop_event);
    }
}

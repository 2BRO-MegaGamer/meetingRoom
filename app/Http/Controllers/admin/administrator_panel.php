<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard\minecraft_Dashboard;
use App\Http\Controllers\generate_UUID;
use App\Models\Administrator_log;
use App\Models\Discount_code;
use App\Models\User;
use App\Models\UserServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Storage;

class administrator_panel extends Controller
{
    public function validate_all(Request $request) {
        $request->validate([
            'from_date'=>['required'],
            'to_date'=>['required'],
            'set_all_TF'=>['required']
        ]);
        $all_row = DB::select('SELECT * FROM discount_code WHERE created_at BETWEEN "'.$request->to_date . '" AND "'. $request->from_date.'"');
        if (count($all_row) != 0) {
            for ($i=0; $i < count($all_row); $i++) { 
                $id = $all_row[$i]->id;
                Discount_code::where("id",$id)->update([
                    'validation' => $request->set_all_TF
                ]);
            }
        }
        return redirect("/discount");
    }
    public function remove_all(Request $request) {
        $request->validate([
            'from_date'=>['required'],
            'to_date'=>['required'],
        ]);
        $all_row = DB::select('SELECT * FROM discount_code WHERE created_at BETWEEN "'.$request->to_date . '" AND "'. $request->from_date.'"');
        if (count($all_row) != 0) {
            for ($i=0; $i < count($all_row); $i++) { 
                $id = $all_row[$i]->id;
                Discount_code::where("id",$id)->delete();
            }
        }
        return redirect("/discount");
        
    }
    public function discount_generator(){
        $permission_insite = User::where('id',auth()->user()->id)->get('in_site_permission')[0];
        if ($permission_insite->in_site_permission == "owner") {
            return view('admin.discount_code_generator');
        }else{
            return redirect('/');
        }
    }
    public function remove_unvalid_code(Request $request) {
        $request->validate([
            'code'=>['required'],
            'todo'=>['required']
        ]);
        if ($request->todo == "remove") {
            Discount_code::where("id",$request->code)->delete();
        }else if ($request->todo == "unvalid") {
            $old_data = Discount_code::where("id",$request->code)->get('validation')[0]->validation;
            Discount_code::where("id",$request->code)->update([
                'validation' => ($old_data== "true" ? "false":"true")
            ]);
        }
    }
    public function create_discount_code(Request $request){
        $permission_insite = User::where('id',auth()->user()->id)->get('in_site_permission')[0];
        if ($permission_insite->in_site_permission == "owner") {
            $request->validate([
                'title'=>['required'],
                'count'=>['required'],
                'percentage'=>['required'],
                'expiration_date'=>['required'],
                'usable_amount'=>['required'],
                'low_limit'=>['required'],
                'higth_limit'=>['required'],
            ]);
            if ($request->percentage > 60) {
                $request->percentage = 60;
            }
            for ($i=0; $i < $request->count; $i++) { 
                $uuid = (new generate_UUID)->my_uniqe_uuid_generate(20);
                $discount_tabel = new Discount_code;
                $discount_tabel->id = $uuid;
                $discount_tabel->title = $request->title;
                $discount_tabel->creator = auth()->user()->id;
                $discount_tabel->percentage = $request->percentage;
                $discount_tabel->expiration_date = $request->expiration_date;
                $discount_tabel->usable_amount = $request->usable_amount;
                $discount_tabel->low_limit = $request->low_limit;
                $discount_tabel->higth_limit = $request->higth_limit;
                $discount_tabel->validation = "true";
                $discount_tabel->save();
            }
        }else{
            return redirect('/');
        }
        return redirect('/discount');
    }
    public function view_panel(){
        $permission_insite = User::where('id',auth()->user()->id)->get('in_site_permission')[0];
        if ($permission_insite->in_site_permission == "administrator" || $permission_insite->in_site_permission == "owner") {
            $games = Storage::disk('local')->get('/json/games.json');
            $games = json_decode($games, true);
            return view('admin.admin_dashboard', [
                'games' => $games
            ]);
        }else{
            return redirect('/');
        }
    }
    public function get_info(Request $request){
        $server_id = $request->server_id;
        $game_name =str_replace(" ", "-", $request->game);
        $game_name = strtoupper(str_replace("-Edition", "", $game_name));
        $server_info = UserServer::where([['id',$server_id],['game',$game_name]])->get();
        if (count($server_info) == 0) {
            $this->save_to_log(auth()->user()->id,$request->server_id,'سروری با این اطلاعات وجود ندارد');
            return redirect('/administrator')->withErrors([
                'not_found'=>'سروری با این اطلاعات وجود ندارد'
            ]);
        }else{
            $this->save_to_log(auth()->user()->id,$request->server_id,'ادمین با موفقیت اطلاعات را دریافت کرد');
            return redirect('/administrator')->with([
                'server'=>$server_info[0]
            ]);
        }
    }
    public function view_dashboard($serverID)
    {
        if ($serverID == null) {
            $this->save_to_log(auth()->user()->id,$serverID,'سرور پیدا نشد');
            return redirect('/administrator')->withErrors([
                'null'=>'سرور پیدا نشد'
            ]);
        } else {
            $this->save_to_log(auth()->user()->id,$serverID,'ادمین با موفقیت وارد داشبورد شد');
            $sv_exists = (new minecraft_Dashboard)->available_servers($serverID);
            $versions = Storage::disk('local')->get('/json/versions.json');
            $versions = json_decode($versions, true);
            return view('dashboard_details/dashboard')->with([
                'serverSelected_id' => $serverID,
                'serverSelected_name' => $sv_exists[1],
                'serverSelected_game' => $sv_exists[2],
                'serverSelected_permition' => "administrator",
                'versions'=>$versions
            ]);
        }
    }
    private function save_to_log($admin_id,$target_server_id,$details) {
        $admin_name = (User::where('id',$admin_id)->get('userName')[0])->userName;
        $target_id = UserServer::where('id',$target_server_id)->get('creator_id');
        if (count($target_id) != 0) {
            $target_id=$target_id[0]->creator_id;
            $target_name = (User::where('id',$target_id)->get('userName')[0])->userName;
        }else{
            $target_id = null;
            $target_name = null;
        }
        $Administrator_log = new Administrator_log;
        $Administrator_log->admin_name= $admin_name;
        $Administrator_log->admin_id = $admin_id;
        $Administrator_log->target_name = $target_name;
        $Administrator_log->target_id = $target_id;
        $Administrator_log->target_server_id = $target_server_id;
        $Administrator_log->details = $details;
        $Administrator_log->save();
    }


}

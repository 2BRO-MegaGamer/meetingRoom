<?php

namespace App\Http\Controllers;

use App\Models\PhoneVerification;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;

class PhoneVerificationController extends Controller
{
    public function show_verification_form() {
        $is_requested = PhoneVerification::where('user_id',auth()->user()->id)->get('user_id');
        $All_requested = PhoneVerification::get('user_id');
        if (count($All_requested) >= 5) {
            $this->remove_if_expired($All_requested);
        }
        if (count($is_requested) === 0) {
            return view('auth.verify_phoneNumber')->with(['message'=>'no_code']);
        }else{
            $is_expired = $this->check_expired(auth()->user()->id);
            if (!$is_expired) {
                $attempt_count = $this->check_attempt();
                if ($attempt_count) {
                    return view('auth.verify_phoneNumber')->with(['message'=>'outOf_time']);
                }else{
                    return view('auth.verify_phoneNumber')->with(['message'=>'outOf_attempt']);
                }
            }else{
                return view('auth.verify_phoneNumber')->with(['message'=>'outOf_time']);
            }
        }
    }
    public function send_text_message(Request $request) {
        if (($request->code_1)!= null) {
            $code_get = $request->code_1 .$request->code_2 .$request->code_3 .$request->code_4 . $request->code_5 . $request->code_6;
            $code_db = PhoneVerification::where('user_id',auth()->user()->id)->get();
            if (count($code_db) == 0) {
                return view('auth.verify_phoneNumber')->with(['message'=>'no_code']);
            }
            if ($code_get != $code_db[0]->code) {
                if ($this->check_attempt()) {
                    return view('auth.verify_phoneNumber')->with(['message'=>'code_duplicate']);
                }else{
                    return view('auth.verify_phoneNumber')->with(['message'=>'outOf_attempt']);
                }
            }else{
                $is_expired = $this->check_expired(auth()->user()->id);
                if ($is_expired) {
                    $code_row = PhoneVerification::where('user_id',auth()->user()->id)->get('code');
                    if(count($code_row) == 0){
                        return view('auth.verify_phoneNumber')->with(['message'=>'no_code']);
                    }else{
                        return view('auth.verify_phoneNumber')->with(['message'=>'outOf_time']);
                    }
                }else{
                    PhoneVerification::where('user_id',auth()->user()->id)->delete();
                    User::where('id',auth()->user()->id)->update([
                        'phone_number_verified_at'=>date('Y:m:d H:i:s',time())
                    ]);
                    return redirect('/home')->with(['message'=>'phone_verified']);
                }
            }
        }else{
            $six_random_number = random_int(100000, 999999);
            $phone_verification = new PhoneVerification;
            $is_requested = PhoneVerification::where('user_id',auth()->user()->id)->get('user_id');
            if (count($is_requested) === 0) {
                $phone_verification->user_id = auth()->user()->id;
                $phone_verification->code = $six_random_number;
                $phone_verification->attempt = 0;
                $phone_verification->save();
                return view('auth.verify_phoneNumber')->with(['message'=>'code_send']);
            }else{
                return view('auth.verify_phoneNumber')->with(['message'=>'code_duplicate']);
            }
        }
    }
    public function check_attempt(){
        $attempt_count = PhoneVerification::where('user_id',auth()->user()->id)->get('attempt');
        if ($attempt_count[0]->attempt == 2) {
            return false;
        }else if($attempt_count[0]->attempt < 2){
            PhoneVerification::where('user_id',auth()->user()->id)->update(['attempt'=>$attempt_count[0]->attempt + 1]);
            return true;
        }
    }
    public function check_expired($user_id) {
        $time_expired = PhoneVerification::where('user_id',$user_id)->get('created_at');
        if(count($time_expired)!= 0){
            $y_m_d_h_i_s = $time_expired[0]->created_at;
            $diff_time =(new DateTime($y_m_d_h_i_s))->diff(new DateTime('now'));
            if($diff_time->y == 0 && $diff_time->m == 0 && $diff_time->d == 0 && $diff_time->h == 0 && $diff_time->i <= 5){
                return false;
            }else{
                PhoneVerification::where('user_id',auth()->user()->id)->delete();
                return true;
            }
        }else{
            return null;
        }
    }
    public function remove_if_expired($All_requeste){
        for ($i=0; $i < count($All_requeste); $i++) {
            $is_expired = $this->check_expired($All_requeste[$i]->user_id);
            if ($is_expired) {
                PhoneVerification::where('user_id',$All_requeste[$i]->user_id)->delete();
            }
        }
    }
}

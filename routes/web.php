<?php

use App\Http\Controllers\admin\administrator_panel;
use App\Http\Controllers\auth\manageRoomController;
use App\Http\Controllers\Auth\SeeprofileController;
use App\Http\Controllers\CheckUnique;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\inRoomController;
use App\Http\Controllers\MeetingRoomController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\python\PythonController;
use App\Http\Middleware\HOST_MODS_cando;
use App\Http\Middleware\manageRoomSecurity;
use App\Http\Middleware\meatingRoomSecurity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Profiler\Profile;
Route::get('/', [HomeController::class, 'index']);
Route::get('/home',[HomeController::class, 'index']);
Route::post('/register/check_if_unique', [CheckUnique::class, 'check_if_unique']);
Route::prefix('administrator')->group(function (){
    Route::get('/',[administrator_panel::class, 'view_panel']);
    Route::post('/get_info',[administrator_panel::class, 'get_info']);
    Route::get('/{serverID}', [administrator_panel::class, 'view_dashboard']);
});
Route::middleware('auth')->group(function () {
    Route::get('/python',[PythonController::class , 'select_job']);
    Route::prefix('python/voice_to_text')->group(function () {
        Route::get('/',[PythonController::class , 'show_voice_to_text_form']);
        Route::post('/',[PythonController::class , 'voice_to_text_create_python_file']);
        Route::post('/run_python_file',[PythonController::class , 'voice_to_text_run_python_file']);
        Route::post('/get_result',[PythonController::class , 'voice_to_text_result_txt']);
    });
    Route::prefix('python/convert_file')->group(function () {
        Route::get('/',[PythonController::class , 'show_convert_file_form']);
        Route::post('/upload_file',[PythonController::class , 'convert_file_upload']);
        Route::post('/',[PythonController::class , 'convert_file_create_python_file']);
        Route::post('/run_python_file',[PythonController::class , 'convert_file_run_python_file']);
        Route::get('file/{path}',[PythonController::class , 'convert_file_download']);
    });
    Route::prefix('python/reduce_noise')->group(function () {
        Route::get('/',[PythonController::class , 'show_reduce_noise_form']);
        Route::post('/',[PythonController::class , 'reduce_noise_create_python_file']);
        Route::post('/run_python_file',[PythonController::class , 'reduce_noise_run_python_file']);
        Route::get('file/{path}',[PythonController::class , 'reduce_noise_download']);
    });
    Route::prefix('python/volume_changer')->group(function () {
        Route::get('/',[PythonController::class , 'show_volume_changer_form']);
        Route::post('/',[PythonController::class , 'volume_changer_create_python_file']);
        Route::post('/run_python_file',[PythonController::class , 'volume_changer_run_python_file']);
        Route::get('file/{path}',[PythonController::class , 'volume_changer_download']);
    });

    Route::get('/phone/verify',[PhoneVerificationController::class , 'show_verification_form']);
    Route::post('/phone/verify',[PhoneVerificationController::class , 'send_text_message']);
    Route::get('/seeprofile',[SeeprofileController::class , 'seeprofile']);
    ////////////////manageRoom/////////////////
    Route::get('/manageRoom',[manageRoomController::class , 'show_rooms']);
    Route::get('/manageRoom/banlist/{room_id}',[manageRoomController::class , 'show_ban_list']);
    Route::get('/manageRoom/modlist/{room_id}',[manageRoomController::class , 'show_mod_list']);
    Route::get('/manageRoom/allowedMemmber/{room_id}',[manageRoomController::class , 'show_allowedMember_list']);
    Route::get('/manageRoom/manageMember/{room_id}',[manageRoomController::class , 'show_manageMember_list']);
    Route::middleware(manageRoomSecurity::class)->post('/manageRoom/remvoe_room',[manageRoomController::class , 'remvoe_room']);
    Route::middleware(manageRoomSecurity::class)->post('/manageRoom/remove_ban_mod',[manageRoomController::class , 'remove_ban_mod']);
    Route::middleware(manageRoomSecurity::class)->post('/manageRoom/warning_member',[manageRoomController::class , 'warning_member']);
    Route::middleware(manageRoomSecurity::class)->post('/manageRoom/ban_member',[manageRoomController::class , 'ban_member']);
    Route::middleware(manageRoomSecurity::class)->post('/manageRoom/accept_denny_refresh_member',[manageRoomController::class , 'accept_denny_refresh_member']);
    ////////////////manageRoom/////////////////
    Route::post('/get_member_profile_and_detail',[Profile::class , 'get_members_profile_info_with_peer_id']);
    ////////////////MeetingRoom/////////////////
    Route::prefix('/mR')->group(function () {
        Route::get('/create',[MeetingRoomController::class,'create_page']);
        Route::post('/create_room',[MeetingRoomController::class,'create_room']);
        Route::middleware(meatingRoomSecurity::class)->get('/Room/{RoomID}',[MeetingRoomController::class,'genarate_room']);
        Route::get('/joinTo/{RoomID}',[MeetingRoomController::class,'joinTo_page']);
        Route::middleware(meatingRoomSecurity::class)->group(function () {
        /////////////////////////inROOOM/////////////////////////
            Route::post('/get_members_peer_id',[MeetingRoomController::class,'get_members_peer_id']);
            Route::post('/member_disconnect',[MeetingRoomController::class,'member_disconnect']);
            Route::post('/members_cannot_make_connection_to_member',[inRoomController::class,'members_cannot_make_connection_to_member']);
            Route::post('/get_username_from_id',[inRoomController::class,'get_username_from_id']);
            Route::post('/upload_formdata',[inRoomController::class,'upload_formdata']);
            Route::post('/upload_textmessage',[inRoomController::class,'upload_textmessage']);
            Route::post('/load_old_message',[inRoomController::class,'load_old_message']);
            Route::post('/get_mods_info',[inRoomController::class,'get_mods_info']);
            Route::post('/announcement_log',[inRoomController::class,'announcement_log']);
            Route::post('/load_old_announcement',[inRoomController::class,'load_old_announcement']);
            Route::middleware(HOST_MODS_cando::class)->group(function () {
                Route::post('/HOST_MOD_cando',[inRoomController::class,'HOST_MOD_cando']);
            });
        /////////////////////////inROOOM/////////////////////////
        });
    });
    Route::get('/false',function () {
        return ("message " .session()->get('message'));
    });
    ////////////////MeetingRoom/////////////////
});
Auth::routes(["verify"=>true]);
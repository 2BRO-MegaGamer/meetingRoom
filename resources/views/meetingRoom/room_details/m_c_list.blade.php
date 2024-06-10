<?php
use App\Models\User;

$wait_to_accept = $room_info[0]->wait_to_accept;
$wait_to_accept_id_array =[];
$wait_to_accept_userName_array=[];

if ($wait_to_accept != null) {
    $wait_to_accept_id_array = explode(",",$wait_to_accept);
    for ($i=0; $i < count($wait_to_accept_id_array); $i++) { 
        $user_name = User::where('id',$wait_to_accept_id_array[$i])->get('userName')[0]->userName;
        $wait_to_accept_userName_array[$wait_to_accept_id_array[$i]] = $user_name;
    }
}

$deny_m = $room_info[0]->deny_m;
$deny_m_id_array =[];
$deny_m_userName_array=[];
if ($deny_m != null) {
    $deny_m_id_array = explode(",",$deny_m);
    for ($i=0; $i < count($deny_m_id_array); $i++) { 
        $user_name = User::where('id',$deny_m_id_array[$i])->get('userName')[0]->userName;
        $deny_m_userName_array[$deny_m_id_array[$i]] = $user_name;
    }
}

?>

<div class="w-100 position-absolute bottom-0 d-none border border-1 border-light" id="members_div" style="height: 95%">
    <div class="w-100 bg-dark">
        <button class="btn btn-dark w-100 fs-2" sidebar_detail="members" id="sidebar_showbtn"><i class="bi bi-arrow-down"></i></button>
    </div>
    <div id="members_info_scroll_style" class="list-group overflow-auto rounded-0 w-100 h-100"  style="max-height: 100%;">
        <div id="hosts_info"></div>
        <div id="mods_info"></div>
        <div id="members_info"></div>
    </div>
</div>
<div class="w-100 bg-dark position-absolute bottom-0 d-none border border-1 border-light" id="chatmessage_div" style="height: 95%">
    <div class="w-100 bg-dark">
        <button class="btn btn-dark w-100 fs-2" sidebar_detail="chatmessage" id="sidebar_showbtn"><i class="bi bi-arrow-down"></i></button>
    </div>
    <div id="chat_message_scroll_style" class="overflow-auto" style="max-height: 75%;">
        <div class="d-block" id="message_save_in_room">
        </div>
    </div>
    <div class="position-absolute bottom-0 w-100" style="height: 20%;">
        <div class="h-100">
            <form method="post" class="position-relative" id="C_M_M_form" enctype="multipart/form-data">
                @csrf
                <div class="form-floating " style="height: 68%">
                    <textarea id="textarea_for_message_in_room" class="form-control h-100 rounded-0 " style="resize: none;"></textarea>
                    <label for="textarea_for_message_in_room">Message</label>
                </div>
                <div style="height: 32%">
                    <div class="m-auto h-100 row">
                        <div class="col p-0 m-0" style="max-width: fit-content">
                            <button class="btn btn-outline-light w-100 h-100 rounded-0 border-0" id="voice_chat_message"><i class="bi bi-mic-fill"></i></button>
                        </div>
                        <div class="col p-0 m-0" style="max-width: fit-content">
                            <button class="btn btn-outline-light w-100 h-100 rounded-0 border-0" id="send_file_btn"><i class="bi bi-file-earmark-arrow-up fs-4"></i></button>
                            <input type="file" name="upload_file" class="d-none" accept="image/*,audio/*,video/*,.pdf,.rar,.zip" id="send_file_input">
                        </div>
                        <div class="col p-0 m-0">
                            <button class="btn btn-info w-100 h-100 rounded-0 text-center m-auto fs-2" id="send_message_text"><div class="m-auto" style="rotate:45deg;max-width:fit-content;"><i class="bi bi-send" ></i></div></button>
                        </div>
                    </div>
                </div>
                <div class="w-100 h-100 position-absolute bg-dark z-3 bottom-0 start-0 d-none" style="opacity: 0;" id="voice_recorder_div">
                    <div class="w-100">
                        <button class="btn btn-dark text-light w-100" id="revers_voice_recorder"><i class="bi bi-arrow-down"></i></button>
                    </div>
                    <div class="w-100 position-relative h-75 d-flex justify-content-center align-items-center">
                        <div class="row w-100  m-auto">
                            <div class="col p-0 m-0" style="max-width: fit-content">
                                <button class="btn w-100 h-100 btn-outline-light rounded-3" id="start_holding_recording_mic"><i class="bi bi-mic-fill"></i></button>
                            </div>
                            <div class="col row w-100 m-auto">
                                <div class="col text-center m-auto fs-4 d-none" style="max-width: fit-content" id="play_pause_recorded_mic">
                                    <audio class="w-0 h-0 d-none opacity-0 " style="visibility:hidden;" id="audio" ></audio>
                                    <button class="btn btn-outline-light" id="play_pause" is_played="false" ><i class="bi bi-play-fill"></i></button>
                                </div>
                                <div class="col text-center m-auto fs-4" id="timer_recorder_mic">
                                    <strong id="min">00</strong>:<strong id="sec">00</strong> 
                                </div>
                            </div>
                            <div class="col p-0 m-0" style="max-width: fit-content" ><button class="btn btn-outline-light text-center m-auto fs-2" id="send_recorded_audio"><div class="m-auto" style="rotate:45deg;max-width:fit-content;"><i class="bi bi-send" ></i></div></button></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="w-100 position-absolute bottom-0 d-none border border-1 border-light" id="announcement_div" style="height: 95%;max-width:100%">
    <div class="w-100 h-100 position-relative">
        <div class="w-100 bg-dark" style="max-height: 5%;">
            <button class="btn btn-dark w-100 fs-2" sidebar_detail="announcement" id="sidebar_showbtn"><i class="bi bi-arrow-down"></i></button>
        </div>
        <div class="overflow-y-auto w-100 h-100" style="max-height: 90%;min-height:90%;" id="announcement_saver">
        </div>
        @if ($am_i_host == "true")
            <div class="w-100 h-100" style="max-height: 5%;">
                <button class="w-100 h-100 btn btn-outline-light rounded-0" id="open_announcement_modal"  data-bs-toggle="modal" data-bs-target="#send_announcement">
                    Send Announcement
                </button>
                <div class="modal fade" id="send_announcement" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="send_announcement_Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content bg-dark text-light">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="send_announcement_Label">HOST send Announcement</h1>
                                <button type="button" id="close_HOST_announcement_modal_btn" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="text_announcement" class="form-label">Text Announcement :</label>
                                    <textarea class="form-control" autocomplete="off" id="text_announcement" cols="30" rows="10"></textarea>
                                </div>
                                <select class="form-select" id="announcement_visibility_select_div">
                                    <option value="" selected>Visible for .menu</option>
                                    <option value="all">@All</option>
                                    <option value="mods">@Mods</option>
                                </select>
                                <small>visible for :</small>
                                <div class="w-100 row my-3" id="visible_for_div">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-primary w-100 " id="HOST_send_announcement_btn">Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<div class="w-100 position-absolute bottom-0 d-none border border-1 border-light" id="private_div" style="height: 95%">
    <div class="w-100 bg-dark">
        <button class="btn btn-dark w-100 fs-2" sidebar_detail="private" id="sidebar_showbtn"><i class="bi bi-arrow-down"></i></button>
    </div>
    <div class="w-100">
        <div class="w-100"><button class="btn btn-outline-light w-100 rounded-0" id="refresh_wait_accept_list"><i class="bi bi-arrow-clockwise"></i></button></div>
        <div class="w-100 h-100">
            <div class="h-100 p-0 m-0">
                <div class="w-100 h-100 mt-3 p-0 m-0">
                    <div class="text-center">
                        <p>Wait to accept request</p>
                    </div>
                    <div class="rounded-0 border border-1 border-info" style="max-height: 85%;min-height:85%;height:85%;" >
                        <div class="overflow-y-auto row row-cols-1 p-0 m-0 " style="max-height: 100%" id="wait_to_accept_list">
                            @for ($i = 0; $i < count($wait_to_accept_id_array); $i++)
                            <div class="col mt-3 card bg-dark text-light border-light p-0 m-0" id="{{ $wait_to_accept_id_array[$i] }}_wait_accept">
                                <div class="card-header d-flex flex-column text-center">
                                    {{ $wait_to_accept_userName_array[$wait_to_accept_id_array[$i]] }}
                                    <small class="text-secondary">{{ $wait_to_accept_id_array[$i] }}</small>
                                </div>
                                <div class="card-body p-0 m-0">
                                    <div class="btn-group w-100 h-100 p-0 m-0 rounded-0" role="group">
                                        <button type="button" class="btn btn-danger"><i class="bi bi-x-lg"></i></button>
                                        <button type="button" class="btn btn-success"><i class="bi bi-check-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



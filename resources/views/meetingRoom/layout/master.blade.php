<!DOCTYPE html id="html">
<html lang="en">
    @php
        use App\Models\Rooms;
        $server_path = 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/mR";
        $user_hash_id = auth()->user()->UserName."_".auth()->user()->hashtag."_".auth()->id();
        $user_token = password_hash($user_hash_id,PASSWORD_DEFAULT);
        $members_get = (Rooms::where('room_uuid',$roomUUID)->get())[0]->Members;
        $members_get = explode(',',$members_get);
        $members_connected="";
        for ($i=0; $i < count($members_get); $i++) { 
            if ($members_get[$i] != auth()->id()) {
                if ($members_connected === "") {
                    $members_connected = $members_get[$i];
                }else {
                    $members_connected = $members_connected .",". $members_get[$i];
                }
            }
        }
        $am_i_host;
        if ($Permission === "HOST") {
            $am_i_host = "true";
        }else {
            $am_i_host = "false";
        }
    @endphp
<head>
    @vite(['resources/sass/app.scss'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script>
        const ROOM_UUID = "{{$roomUUID}}";
        const ROOM_ID = "{{$roomID}}";
        const ROOM_PERMISSION = "{{$Permission}}";
        const USER_NAME = "{{auth()->user()->userName}}";
        const USER_ID = "{{auth()->id()}}";
        const USER_TOKEN = "{{$user_token}}";
        const IN_ROOM_NAME = "{{$my_custom_name}}";
        const duplicate_detect = "{{$duplicate}}";
        const MEMBERS_CONNECTED_DB = "{{$members_connected}}";
        const AM_I_HOST = "{{$am_i_host}}";
        const HOST_NAME = "{{$HOST_userName}}";
        const HOST_ID = "{{$HOST_id}}";
        const PEER_INFO={NEW_PEER:{},MEMBERS:[],CONNECTION:{},MEDIA_STREAM:[{video:false,audio:false,camera:false,empty:true},{video:undefined,audio:undefined,camera:undefined,empty:undefined}],CALL:{}}
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <title>{{$roomID}}</title>
    <style id="dynamic_animation">
        *::-webkit-scrollbar-track
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            background-color: #F5F5F5;
        }
        *::-webkit-scrollbar
        {
            width: 6px;
            background-color: #F5F5F5;
        }
        *::-webkit-scrollbar-thumb
        {
            background-color: #000000;
        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        @keyframes sidebar_RAL_animation{
            0%{
                bottom:0%;
            }
            100%{
                bottom:93.5%;
            }
        }
        @keyframes opacity_change{
            0%{
                opacity: 0;
            }
            100%{
                opacity: 1;
            }
        }
    </style>
    <audio class="d-none opacity-0 w-0 h-0" id="notification_audio" src="{{asset('audio/notification.mp3')}}"></audio>
</head>
<body class=" bg-dark" id="body">
    <div class="vw-100 vh-100 bg-dark " style="overflow:hidden">
        <div class="position-absolute w-100 h-100 " id="loadScreen">
            @include('meetingRoom.room_details.loadingScreen')
        </div>
        <div class="vw-100 vh-100 bg-dark d-none" id="confirm_connect">
            <div class="w-100 h-100 d-flex justify-content-center align-items-center">
                <div class="card w-50 text-light" style="background: none;">
                    <div class="card-header text-center h3">
                        Do you want to join this room?
                    </div>
                    <div class="card-footer ">
                        <div class="m-auto row">
                            <div class="w-25">
                                <button class="btn btn-danger" id="dont_connect">close</button>
                            </div>
                            <div class="w-75 text-end">
                                <button class="btn w-50 btn-success" id="open_connection">connect</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 h-100 p-0 m-0 d-none" id="meatingRoom">
            <div class="w-100 h-100 text-light row w-100 m-auto z-3">
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-2  d-none d-xxl-block d-xl-block d-lg-block p-0 m-0">
                    @include('meetingRoom.room_details.webcam_mic')
                </div>
                <div class="col p-0 m-0">
                    @include('meetingRoom.room_details.host_whiteboard_video')
                </div>
                <div class="col-xxl-1 col-xl-1 col-md-1 col-sm-1 col-xs-2 col-2 p-0 m-0" id="sidebarmenu_div">
                    <div class="w-100 h-100 ">
                        @include('meetingRoom.room_details.sidebar')
                    </div>
                </div>
                {{-- col-xxl-3 col-xl-3 col-md-4 col-sm-6 col-xs-6 col-7 --}}
            </div>
        </div>
    </div>
    <div>
        <button type="button" id="btn_for_user_connect_to_room" class="mw-0 mh-0 d-none" data-bs-toggle="modal" data-bs-target="#user_connect_to_room"></button>
        <div class="modal fade" id="user_connect_to_room" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="user_connect_to_room_Label" aria-hidden="true">
            <div class="modal-dialog text-light ">
                <div class="modal-content bg-dark">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="user_connect_to_room_Label">Do you want to join this room?</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <small>By clicking on the connect button, you accept the <a href="#">rules</a> of this site</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="modal_btn_close_user_want_to_make_connection" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="modal_btn_connect_user_want_to_make_connection" onclick="user_want_to_make_connection(false)">connect</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="toasts_div"></div>
    @vite(['resources/js/meating_room.js','resources/js/bootstrap.js','resources/js/notification_Toasts.js'])

</body>
{{-- <script src="//cdn.jsdelivr.net/npm/eruda"></script>
<script>eruda.init();</script> --}}

</html>
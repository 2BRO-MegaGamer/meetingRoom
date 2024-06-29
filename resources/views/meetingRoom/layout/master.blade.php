<!DOCTYPE html id="html">
<html lang="en">
    @php
        use App\Models\Rooms;
        $user_hash_id = auth()->user()->UserName."_".auth()->id();
        $user_token = password_hash($user_hash_id,PASSWORD_DEFAULT);
        $room_info = Rooms::where('room_uuid',$roomUUID)->get();
        $am_i_host;
        if ($Permission === "HOST") {
            $am_i_host = "true";
        }else {
            $am_i_host = "false";
        }
    @endphp
<head>
    @vite(['resources/sass/app.scss','resources/css/meetingroom.css'])
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
        const AM_I_HOST = "{{$am_i_host}}";
        const HOST_NAME = "{{$HOST_userName}}";
        const HOST_ID = "{{$HOST_id}}";
        const PEER_INFO={NEW_PEER:{},MEMBERS:[],CONNECTION:{},MEDIA_STREAM:[{video:false,audio:false,camera:false,empty:true},{video:undefined,audio:undefined,camera:undefined,empty:undefined}],CALL:{}}
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <title>{{$roomID}}</title>
    <style id="dynamic_animation">

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
                <div class="card text-light" style="background: none;">
                    <div class="card-header text-center h3">
                        Do you want to join this room?
                    </div>
                    <div class="card-footer">
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
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-2 col-sm-5 position-relative d-none d-xxl-block d-xl-block d-lg-block p-0 m-0" >
                    @include('meetingRoom.room_details.webcam_mic')
                </div>
                <div class="col p-0 m-0">
                    @include('meetingRoom.room_details.host_whiteboard_video')
                </div>
                <div class="col-xxl-1 col-xl-1 col-md-1 col-sm-1 bg-dark col-xs-2 col-2 p-0 m-0 z-3" id="sidebarmenu_div">
                    <div class="w-100 h-100 ">
                        @include('meetingRoom.room_details.sidebar')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="toasts_div"></div>
    @vite(['resources/js/meating_room.js','resources/js/bootstrap.js','resources/js/notification_Toasts.js'])
    <style>
        .modal-backdrop {
            z-index: 0;
        }
    </style>
</body>
<?php
$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile")); 

if($isMob && $am_i_host == "true"){ 
    echo '<script src="//cdn.jsdelivr.net/npm/eruda"></script><script>eruda.init();</script>'; 
}
?>


</html>
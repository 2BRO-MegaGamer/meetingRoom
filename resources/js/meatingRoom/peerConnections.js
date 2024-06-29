export function create_connection_room() {
    var peer = new Peer(USER_ID+"_"+ROOM_ID,{host:"192.168.1.103", port: 9000, path: '/',debug:1});
    peer.on('open',(id)=>{
        PEER_INFO["NEW_PEER"] =  peer;
        peer.on('call', async function(call) {
            var call_peer_id = call.peer;
            var metadata_call = (call.metadata)[call_peer_id];
            console.log( call_peer_id + " call FROM :" ,call , " || " + metadata_call);
            if (PEER_INFO.MEDIA_STREAM[1]['empty'] == undefined) {
                await make_empty_media_stream(true);
            }
            var empty_media = PEER_INFO.MEDIA_STREAM[1]["empty"];
            var option = {metadata:{}};
            option['metadata'][USER_ID+"_"+ROOM_ID] = PEER_INFO["MEDIA_STREAM"][0];
            call.answer(empty_media,option);
            update_call_status(call,metadata_call,call_peer_id);
        });
        peer.on('connection', function(conn) {
            var peer_id = conn.peer;
            var u_id = peer_id.split("_")[0];
            if (PEER_INFO['CONNECTION'][peer_id] == undefined) {
                PEER_INFO['CONNECTION'][peer_id] = [];
            }
            PEER_INFO['CONNECTION'][peer_id][0] = conn;
            PEER_INFO['CONNECTION'][peer_id][1] = true;
            check_conn_connection(conn,"receive");
            if (document.getElementById(u_id+"_member_list_div") == null) {
                add_member_to_list(conn.peer);
            }
            console.log("connect, someone or me? ",conn);
        });
        get_members_ids(id);
    })
    permission_todo();
}
function add_my_media_to_user (call_peer_id) {
    var all_media_name = Object.keys(PEER_INFO.MEDIA_STREAM[0]);
    var peer = PEER_INFO.NEW_PEER;
    all_media_name.forEach((media)=>{
        if (PEER_INFO.MEDIA_STREAM[0][media] == true && media != "empty") {
            var option = {metadata:{}};
            option['metadata'][USER_ID+"_"+ROOM_ID] = media;
            var mediaStream = PEER_INFO.MEDIA_STREAM[1][media];
            var call = peer.call(call_peer_id,mediaStream,option);
            call.on("stream",(stream)=>{
                console.log("connect another media stream");
            })
        }
    })
    console.log("call_peer_id ", call_peer_id);
}
function update_call_status (call,metadata_call,call_peer_id) {
    var call_id = call_peer_id.split("_")[0];
    if (call_id == HOST_ID) {
        call_id = "HOST";
    }
    if (PEER_INFO["CALL"][call_peer_id] == undefined) {
        PEER_INFO["CALL"][call_peer_id]={};
    }
    if (PEER_INFO["CALL"][call_peer_id]["media_stream"] == undefined) {
        PEER_INFO["CALL"][call_peer_id]["media_stream"]=[]
        PEER_INFO["CALL"][call_peer_id]["media_stream"][0] = {};
        PEER_INFO["CALL"][call_peer_id]["media_stream"][1] = {};
    }
    console.log(PEER_INFO["CALL"][call_peer_id] , " || " ,call,metadata_call,call_peer_id , "update_call_status");
    if (metadata_call== "empty") {

        $("#"+call_id+"_internet_status").removeClass("text-danger");
        $("#"+call_id+"_internet_status").addClass("text-success");
        PEER_INFO["CALL"][call_peer_id]["status"]=true;
        PEER_INFO["CALL"][call_peer_id]["media_stream"][0]={empty:true};
        add_my_media_to_user(call_peer_id);
    }else{
        call.on('stream', function(stream) {
            PEER_INFO["CALL"][call_peer_id]["media_stream"][0][metadata_call]= true;
            PEER_INFO["CALL"][call_peer_id]["media_stream"][1][metadata_call]= stream;
            user_WMSR_set(call_peer_id)
            console.log("STREAM HAVE VALUE .answer" , call_peer_id, " || ",call , " | | ",PEER_INFO);
        });
        console.log("another connection :" ,call);
    }
    if (PEER_INFO["CALL"][call_peer_id]["call"] == undefined) {
        PEER_INFO["CALL"][call_peer_id]["call"]={video:undefined,audio:undefined,webcam:undefined,empty:undefined};
    }
    PEER_INFO["CALL"][call_peer_id]["call"][metadata_call]=call;
}
document.querySelectorAll("[use_WMSR]").forEach((btn)=>{
    btn.addEventListener("click",()=>{
        if (!btn.disabled) {
            btn.disabled = true;
            change_WMSR(btn.getAttribute("use_WMSR"),btn);
        }
    });
})
async function change_WMSR(WMSR,btn) {
    if ( WMSR == "raisehand") {
        var status = (btn.getAttribute("status")=="true")?true:false;
        (status)?btn.setAttribute("status","false"):btn.setAttribute("status","true");
        var all_users = Object.keys(PEER_INFO.CONNECTION);
        if (all_users.length != 0) {
            all_users.forEach(user_peerid=>{
                var conn = PEER_INFO.CONNECTION[user_peerid][0];
                if (PEER_INFO.CONNECTION[user_peerid][1]) {
                    conn.send({"raisehand":{"status":!status}});
                }
            });
        }
        change_WMSR_overview(WMSR,btn);
    }else{
        var status;
        var media_stream;
        if (PEER_INFO.MEDIA_STREAM[1][WMSR] != undefined ) {
            if (PEER_INFO.MEDIA_STREAM[0][WMSR]) {
                PEER_INFO.MEDIA_STREAM[1][WMSR].getTracks().forEach(function(track) {
                    track.stop();
                });
                status = false;
                PEER_INFO.MEDIA_STREAM[0][WMSR] = false;
                PEER_INFO.MEDIA_STREAM[1][WMSR] = undefined;
            }
        }else{
            status = true;
            switch (WMSR) {
                case "webcam":
                    media_stream = await get_webcam_stream();
                    break;
                case "audio":
                    media_stream = await get_voicechat_stream();
                    break;
                case "video":
                    media_stream = await get_display_stream();
                    break;
            }
            var all_media_name = Object.keys(PEER_INFO.MEDIA_STREAM[0]);
            var is_media_active =false;
            all_media_name.forEach((media)=>{
                if (PEER_INFO.MEDIA_STREAM[0][media] == true && media != "empty") {
                    is_media_active = true;
                }
            })
            if (is_media_active == false) {
                PEER_INFO.MEDIA_STREAM[0]["empty"] = true;
            }else{
                PEER_INFO.MEDIA_STREAM[0]["empty"] = false;
            }
            var all_users = Object.keys(PEER_INFO.CONNECTION);
            var peer = PEER_INFO.NEW_PEER;
            user_WMSR_set(USER_ID+"_"+ROOM_ID);
            if (all_users.length != 0) {
                all_users.forEach(user_peerid=>{
                    var option = {metadata:{}};
                    option['metadata'][USER_ID+"_"+ROOM_ID] = WMSR;
                    var call = peer.call(user_peerid,media_stream,option);
                    if (PEER_INFO["CALL"][user_peerid] != undefined) {
                        PEER_INFO["CALL"][user_peerid]["call"][WMSR] = call;
                    }
                });
            }
        }
        if (status != undefined) {
            var all_users = Object.keys(PEER_INFO.CONNECTION);
            if (all_users.length != 0) {
                all_users.forEach(user_peerid=>{
                    var conn = PEER_INFO.CONNECTION[user_peerid][0];
                    if (PEER_INFO.CONNECTION[user_peerid][1]) {
                        conn.send({"change_media":[(PEER_INFO.MEDIA_STREAM)[0],WMSR,status]});
                    }
                });
            }
        }
        change_WMSR_overview(WMSR,btn);
    }


}
function change_WMSR_overview(WMSR,btn) {

    var my_media_info = PEER_INFO.MEDIA_STREAM[0];

    switch (WMSR) {
        case "webcam":
            setTimeout(()=>{
                if (my_media_info[WMSR]) {
                    btn.innerHTML = '<i class="bi bi-camera-video"></i>';
                }else{
                    btn.innerHTML = '<i class="bi bi-camera-video-off"></i>';
                }
            },1000)
            break;
        case "audio":
            setTimeout(()=>{
                if (my_media_info[WMSR]) {
                    btn.innerHTML = '<i class="bi bi-mic"></i>';
                }else{
                    btn.innerHTML = '<i class="bi bi-mic-mute"></i>';
                }
            },1000);
            break;
        case "video":
            setTimeout(()=>{
                if (my_media_info[WMSR]) {
                    btn.innerHTML = '<i class="bi bi-display-fill"></i>';
                }else{
                    btn.innerHTML = '<i class="bi bi-display"></i>';
                }
            },1000)
            break;
        case "raisehand":
            setTimeout(()=>{
                var status = (btn.getAttribute("status")=="true")?true:false;
                if (status) {
                    btn.innerHTML = '<i class="bi bi-hand-index-fill"></i>';
                }else{
                    btn.innerHTML = '<i class="bi bi-hand-index"></i>';
                }
            },1000)
            break;
    }
    $(btn).addClass("animate__animated animate__faster animate__flipOutX")
    setTimeout(()=>{
        $(btn).addClass("opacity-0");
        $(btn).removeClass("animate__animated animate__faster animate__flipOutX");
        $(btn).addClass("animate__animated animate__faster animate__flipInX");
        $(btn).removeClass("opacity-0");
        setTimeout(()=>{
            $(btn).removeClass("animate__animated animate__faster animate__flipInX");
        },500)
    },1000);
    setTimeout(()=>{
        btn.disabled = false;
    },1500)
}

function create_connection_everyone(id) {
    var peer = PEER_INFO.NEW_PEER;
    for (let i = 0; i < PEER_INFO['MEMBERS'].length; i++) {
        if (PEER_INFO['MEMBERS'][i].split("_")[0] != USER_ID) {
            var option={metadata:{}};
            option["metadata"][USER_ID] = {"userName":USER_NAME,"user_id":USER_ID,"in_room_name":IN_ROOM_NAME,"is_host":AM_I_HOST};
            var conn = peer.connect(PEER_INFO['MEMBERS'][i],option);
            if (PEER_INFO['CONNECTION'][PEER_INFO['MEMBERS'][i]] == undefined) {
                PEER_INFO['CONNECTION'][PEER_INFO['MEMBERS'][i]] = [];
            }
            PEER_INFO['CONNECTION'][PEER_INFO['MEMBERS'][i]][0] = conn;
            PEER_INFO['CONNECTION'][PEER_INFO['MEMBERS'][i]][1] = false;
            check_conn_connection(conn);
        }
    }
}



function check_conn_connection(conn,send_receive) {
    var peer_id = conn.peer;
    if (send_receive == "receive") {
        conn.on("open",()=>{
            conn.send({"media_status" : PEER_INFO['MEDIA_STREAM'][0]});
            // conn.send("salam " + peer_id + " !"+" From:"+USER_INFO.USER_NAME);
        })
    }
    conn.on("open",()=>{
        var u_peer_id = conn.peer;
        var u_id = u_peer_id.split("_")[0];
        conn.on("data",(data)=>{
            switch (Object.keys(data)[0]) {
                case "change_media":
                    PEER_INFO.CALL[u_peer_id]["media_stream"][0][data["change_media"][1]] = data["change_media"][2];
                    user_WMSR_set(u_peer_id);
                    break;
                case "media_status":
                    if (PEER_INFO["CALL"][u_peer_id]== undefined) {
                        PEER_INFO["CALL"][u_peer_id]={};
                    }
                    if (PEER_INFO["CALL"][u_peer_id]["media_stream"] == undefined) {
                        PEER_INFO["CALL"][u_peer_id]["media_stream"] = [];
                    }
                    PEER_INFO["CALL"][u_peer_id]["media_stream"][0]=data["media_status"];
                    break;
                case "media_receive":
                    var file_OR_text = (data["media_receive"].format == "text") ? false:true;
                    var user_id = data["media_receive"].sender_id;
                    var userName = data["media_receive"].sender_name;
                    var first_div = file_text_front_div(file_OR_text,data["media_receive"],userName,user_id);
                    first_div.id = data["media_receive"].id;
                    $(first_div).removeClass("placeholder");
                    break;
                case "HOST_announcement":
                    var log_for_peer = data["HOST_announcement"];
                    var id = log_for_peer.id;
                    announcement_front_div(log_for_peer,true);
                    break;
                case "raisehand":
                    var status = data["raisehand"].status;
                    var raisehand_row_statos = document.getElementById(u_id + "_row_raisehand_status");
                    if (status) {
                        $(raisehand_row_statos).removeClass("d-none");
                    }else{
                        $(raisehand_row_statos).addClass("d-none");
                    }
                    break;
                case "kicked_banned":
                    if (u_id == HOST_ID) {
                        location.replace("/");
                    }
                    break;
                case "promote_demote":
                    if (u_id == HOST_ID) {
                        location.reload();
                    }
                    break;
            }
        });
        PEER_INFO['CONNECTION'][u_peer_id][1] = true;
        create_call_to_user(peer_id,undefined,undefined);
    })
}
function user_WMSR_set(u_peer_id) {
    var user_id = u_peer_id.split("_")[0];
    var user_media_status;
    if (user_id == USER_ID) {
        user_media_status = PEER_INFO.MEDIA_STREAM;
    }else{
        user_media_status = PEER_INFO.CALL[u_peer_id].media_stream;
    }
    var all_media_status = Object.keys(user_media_status[0]);
    all_media_status.forEach((media)=>{
        if (user_media_status[0][media]) {
            if ((document.getElementById(u_peer_id+"_video_tag") == null || document.getElementById(u_peer_id+"_mic_tag")== null || document.getElementById(u_peer_id+"_webcam_tag")== null) && user_id != USER_ID) {
                create_overview_for_call(u_peer_id);
                setTimeout(()=>{
                    user_WMSR_set(u_peer_id);
                },1000)
            }else{
                var all_media = Object.keys(user_media_status[1]);
                for (let i = 0; i < all_media.length; i++) {
                    switch (all_media[i]) {
                        case "video":
                            if (user_media_status[1][all_media[i]]) {
                                document.getElementById(((user_id == HOST_ID)?"host":u_peer_id)+"_video_tag").srcObject = user_media_status[1]['video'];
                                document.getElementById(((user_id == HOST_ID)?"host":u_peer_id)+"_video_tag").play();
                                if (user_id == HOST_ID) {
                                    $("#host_video_tag").removeClass("placeholder");
                                    document.getElementById("host_video_tag").setAttribute("controls","");
                                }else{
                                    if (user_media_status[1]['webcam']) {
                                        if (document.getElementById(u_peer_id+"_webcam_tag").srcObject == undefined) {
                                            document.getElementById(u_peer_id+"_webcam_tag").srcObject = user_media_status[1]['webcam'];
                                        }
                                        $("#"+u_peer_id+"_switch_wv_prev").removeClass('d-none');
                                        $("#"+u_peer_id+"_switch_wv_next").removeClass('d-none');
                                    }
                                    var carousel_webcam_video = document.getElementById(u_peer_id+"_carousel_webcam_video");
                                    $(carousel_webcam_video.children[0]).addClass('active');
                                    $(carousel_webcam_video.children[1]).removeClass('active');
                                    $(("#"+u_peer_id+"_video_div")).removeClass("d-none");
                                }
                            }else{
                                document.getElementById(((user_id == HOST_ID)?"host":u_peer_id)+"_video_tag").srcObject = undefined;
                                if (user_media_status[1]['webcam']) {
                                    if (document.getElementById(u_peer_id+"_webcam_tag").srcObject == undefined) {
                                        document.getElementById(u_peer_id+"_webcam_tag").srcObject = user_media_status[1]['webcam'];
                                    }
                                    var carousel_webcam_video = document.getElementById(u_peer_id+"_carousel_webcam_video");
                                    $(carousel_webcam_video.children[0]).removeClass('active');
                                    $(carousel_webcam_video.children[1]).addClass('active');
                                    $("#"+u_peer_id+"_switch_wv_prev").addClass('d-none');
                                    $("#"+u_peer_id+"_switch_wv_next").addClass('d-none');
                                }else{
                                    $(("#"+u_peer_id+"_video_div")).addClass("d-none");
                                }
                            }
                            break;
                        case "audio":
                            if (user_media_status[1][all_media[i]]) {
                                document.getElementById(u_peer_id+"_mic_tag").srcObject = user_media_status[1]['audio'];
                                document.getElementById(u_peer_id+"_mic_tag").play();
                                $("#"+u_peer_id+"_mic_webcam_div").addClass("border-success");
                            }else{
                                if (document.getElementById(u_peer_id+"_mic_tag") != null) {
                                    document.getElementById(u_peer_id+"_mic_tag").srcObject = undefined;
                                    $("#"+u_peer_id+"_mic_webcam_div").removeClass("border-success");
                                }
                            }
                            break;
                        case "webcam":
                            if (user_media_status[1][all_media[i]]) {
                                document.getElementById(u_peer_id+"_webcam_tag").srcObject = user_media_status[1]['webcam'];
                                document.getElementById(u_peer_id+"_webcam_tag").play();
                                if (user_media_status[1]['video']) {
                                    if (document.getElementById(((user_id == HOST_ID)?"host":u_peer_id)+"_video_tag").srcObject == undefined) {
                                        document.getElementById(((user_id == HOST_ID)?"host":u_peer_id)+"_video_tag").srcObject = user_media_status[1]['video'];
                                    }
                                    $("#"+u_peer_id+"_switch_wv_prev").removeClass('d-none');
                                    $("#"+u_peer_id+"_switch_wv_next").removeClass('d-none');
                                }
                                var carousel_webcam_video = document.getElementById(u_peer_id+"_carousel_webcam_video");
                                $(carousel_webcam_video.children[0]).addClass('active');
                                $(carousel_webcam_video.children[1]).removeClass('active');
                                $(("#"+u_peer_id+"_video_div")).removeClass("d-none");
                            }else{
                                if (document.getElementById(u_peer_id+"_webcam_tag")!= null) {
                                    document.getElementById(u_peer_id+"_webcam_tag").srcObject = undefined;
                                }
                                if (user_media_status[1]['video']) {
                                    if (document.getElementById(((user_id == HOST_ID)?"host":u_peer_id)+"_video_tag").srcObject == undefined) {
                                        document.getElementById(((user_id == HOST_ID)?"host":u_peer_id)+"_video_tag").srcObject = user_media_status[1]['video'];
                                    }
                                    var carousel_webcam_video = document.getElementById(u_peer_id+"_carousel_webcam_video");
                                    $(carousel_webcam_video.children[0]).removeClass('active');
                                    $(carousel_webcam_video.children[1]).addClass('active');
                                    $("#"+u_peer_id+"_switch_wv_prev").addClass('d-none');
                                    $("#"+u_peer_id+"_switch_wv_next").addClass('d-none');
                                }else{
                                    $(("#"+u_peer_id+"_video_div")).addClass("d-none");
                                }
                            }
                            break;
                        case "empty":
                            break;
                    }
                }
            }
        }
    })
}



async function get_display_stream() {
    return new Promise(async (resolve, reject) => {
        let mediaStream = null;
        try {
            mediaStream = await navigator.mediaDevices.getDisplayMedia({
                video: {
                    cursor: "always"
                },
                audio: true
            });
            mediaStream["oninactive"] = (()=>{
                document.querySelector("[use_wmsr=video]").click();
            })
            PEER_INFO["MEDIA_STREAM"][0]["video"]=true;
            PEER_INFO["MEDIA_STREAM"][1]["video"]=mediaStream;
            resolve(mediaStream)
            return mediaStream;
        } catch (ex) {
            resolve(false)
        }
    })


}
async function get_voicechat_stream() {
    return new Promise(async (resolve, reject) => {
        var getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
        await getUserMedia({audio: true}, function(stream) {
            PEER_INFO["MEDIA_STREAM"][0]["audio"]=true;
            PEER_INFO["MEDIA_STREAM"][1]["audio"]=stream;
            resolve(stream)
            return stream;
        }, function(err) {
            resolve(false)
            return false;
        });
    })
}

async function get_webcam_stream() {
    return new Promise(async (resolve, reject) => {
        var getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
        await getUserMedia({video: true,audio:true}, function(stream) {
            PEER_INFO["MEDIA_STREAM"][0]["webcam"]=true;
            PEER_INFO["MEDIA_STREAM"][1]["webcam"]=stream;
            resolve(stream)
            return stream;
        }, function(err) {
            resolve(false)
            return false;
        });
    })
}
function make_empty_media_stream(media_only) {
    const createEmptyAudioTrack = () => {
        const ctx = new AudioContext();
        const oscillator = ctx.createOscillator();
        const dst = oscillator.connect(ctx.createMediaStreamDestination());
        oscillator.start();
        const track = dst.stream.getAudioTracks()[0];
        return Object.assign(track, { enabled: false });
    };
    const createEmptyVideoTrack = ({ width, height }) => {
        const canvas = Object.assign(document.createElement('canvas'), { width, height });
        canvas.getContext('2d').fillRect(0, 0, width, height);
        const stream = canvas.captureStream();
        const track = stream.getVideoTracks()[0];
        return Object.assign(track, { enabled: false });
    };
    const audioTrack = createEmptyAudioTrack();
    const videoTrack = createEmptyVideoTrack({ width:640, height:480 });

    return new Promise(async (resolve, reject) => {
        var empty_audio = new MediaStream([audioTrack])
        if (media_only != true) {
            PEER_INFO["MEDIA_STREAM"][0]["empty"]=true;
        }
        PEER_INFO["MEDIA_STREAM"][1]["empty"]=empty_audio;
        resolve(empty_audio)
        return empty_audio;
    })
}
async function create_call_to_user(u_peer_id,overview_created,call_again) {
    if (overview_created != true) {
        create_overview_for_call(u_peer_id);
    }else{
        if (document.getElementById(u_peer_id+"_mic_webcam_div") != null) {
            var peer = PEER_INFO.NEW_PEER;
            if (PEER_INFO.MEDIA_STREAM[1]['empty'] == undefined) {
                await make_empty_media_stream(true);
            }
            var empty_media = PEER_INFO.MEDIA_STREAM[1]["empty"];
            var call_id = u_peer_id.split("_")[0];
            if (call_id == HOST_ID) {
                call_id = "HOST";
            }
            var option = {metadata:{}};
            option['metadata'][USER_ID+"_"+ROOM_ID] = "empty";
            var call = peer.call(u_peer_id,empty_media,option);
            if (PEER_INFO["CALL"][u_peer_id] == undefined) {
                PEER_INFO["CALL"][u_peer_id]={}
            }
            PEER_INFO["CALL"][u_peer_id]["status"]=true;
            $("#"+call_id+"_internet_status").removeClass("text-danger");
            $("#"+call_id+"_internet_status").addClass("text-success");
            if (PEER_INFO["CALL"][u_peer_id]["call"] == undefined) {
                PEER_INFO["CALL"][u_peer_id]["call"]={video:undefined,audio:undefined,webcam:undefined,empty:undefined};
            }
            PEER_INFO["CALL"][u_peer_id]["call"]["empty"] = call;
            PEER_INFO["CALL"][u_peer_id]["media_stream"]=[];
            PEER_INFO["CALL"][u_peer_id]["media_stream"][0]={video:false,audio:false,webcam:false,empty:false};
            PEER_INFO["CALL"][u_peer_id]["media_stream"][1]={video:undefined,audio:undefined,webcam:undefined,empty:undefined};
            call.on("close",()=>{
                var u_id = (call.peer).split("_")[0];
                if (document.getElementById(u_id+"_internet_status") != null) {
                    $("#"+u_id+"_internet_status").removeClass("text-success");
                    $("#"+u_id+"_internet_status").addClass("text-danger");
                    PEER_INFO["CALL"][u_peer_id]["call"]={video:undefined,audio:undefined,webcam:undefined,empty:undefined};
                }
                if (document.getElementById(call.peer+"_mic_webcam_div") != null) {
                    document.getElementById(call.peer+"_mic_webcam_div").remove();
                }
            })
        }
    }
}
function get_username_with_id(u_peer_id) {
    var id = u_peer_id.split("_")[0];
    $.ajax({
        type: "POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/mR/get_username_from_id',
        data: {
            _token : $('meta[name="csrf-token"]').attr('content'),
            my_info:[USER_NAME,USER_ID,USER_TOKEN],
            room_info:[ROOM_UUID,ROOM_ID,ROOM_PERMISSION],
            id:id,
        },
        success: function(data) {
            create_overview_for_call(u_peer_id,data[0]);
        }
    });
}
export function create_btn_permission(id) {
    var all_btns = [];
    var first_div = document.createElement("div");
    $(first_div).addClass("w-100 h-100 btn-group p-0 m-0");
    if (ROOM_PERMISSION == "MOD" || ROOM_PERMISSION == "HOST") {
        var btn_kick = document.createElement("button");
        btn_kick.innerHTML = '<i class="bi bi-box-arrow-right"></i>';
        var btn_ban = document.createElement("button");
        btn_ban.innerHTML = '<i class="bi bi-exclamation-octagon"></i>';
        $(btn_kick).addClass("btn btn-warning");
        $(btn_ban).addClass("btn btn-danger");
        all_btns.push([btn_kick,"kick"]);
        all_btns.push([btn_ban,"ban"]);
    }
    if (ROOM_PERMISSION == "HOST") {
        var btn_promote = document.createElement("button");
        btn_promote.innerHTML = '<i class="bi bi-person-fill-up"></i>';
        var btn_demote = document.createElement("button");
        btn_demote.innerHTML = '<i class="bi bi-person-fill-down"></i>';
        $(btn_promote).addClass("btn btn-dark");
        $(btn_demote).addClass("btn btn-dark");
        all_btns.push([btn_promote,"promote"]);
        all_btns.push([btn_demote,"demote"]);
    }
    for (let i = 0; i < all_btns.length; i++) {
        if (i == 0 || i == (all_btns.length - 1)) {
            $(all_btns[i][0]).addClass("rounded-top-0");
        }
        all_btns[i][0].addEventListener("click",()=>{
            $.ajax({
                type: "POST",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:'/mR/HOST_MOD_cando',
                data: {
                    _token : $('meta[name="csrf-token"]').attr('content'),
                    my_info:[USER_NAME,USER_ID,USER_TOKEN],
                    room_info:[ROOM_UUID,ROOM_ID,ROOM_PERMISSION],
                    id:id,
                    action:all_btns[i][1]
                },
                success: function(data) {
                    var H_messaeg = "";
                    if (all_btns[i][1] == "kick" || all_btns[i][1] == "ban") {
                        H_messaeg = "kicked_banned";
                    }else{
                        H_messaeg = "promote_demote";
                    }
                    var conn = PEER_INFO.CONNECTION[(id+"_"+ROOM_ID)];
                    if (conn != undefined && conn[1] == true) {
                        conn = conn[0];
                        var message ={};
                        message[H_messaeg] = [USER_NAME,USER_ID,USER_TOKEN];
                        conn.send(message);
                    }
                    switch (all_btns[i][1]) {
                        case "kick":
                            show_notification("success",(("kick").replaceAll("_"," ")),"User has been kicked",true);
                            break;
                        case "ban":
                            show_notification("success",(("ban").replaceAll("_"," ")),"User has been banned",true);
                            break;
                        case "promote":
                            show_notification("success",(("promote").replaceAll("_"," ")),"User has been promoted",true);
                            break;
                        case "demote":
                            show_notification("success",(("demote").replaceAll("_"," ")),"User has been demote",true);
                            break;
                    }
                    var btn = document.getElementById(id+"_detail_Modal");
                    if (btn != null) {
                        btn = btn.querySelector("button");
                        if (btn != null) {
                            btn.click();
                        }
                    }
                }
            });
        });
        first_div.append(all_btns[i][0]);
    }
    return first_div;
}
function create_overview_for_call(u_peer_id,answer) {
    var u_id = u_peer_id.split("_")[0];
    if (document.getElementById(u_peer_id+"_mic_webcam_div") == null) {
        if (answer != undefined) {
            var base_html = `
            <div class="col m-0 p-1" id="${u_peer_id}_mic_webcam_div" >
                <div class="w-100 rounded  border border-2">
                    <div class="w-100 d-none" id="${u_peer_id}_video_div">
                        <div id="${u_peer_id}_carousel_webcam_video" class="carousel slide">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <video id="${u_peer_id}_video_tag" class="w-100" ></video>
                                </div>
                                <div class="carousel-item">
                                    <video id="${u_peer_id}_webcam_tag" id="" class="w-100" ></video>
                                </div>
                            </div>
                            <button class="carousel-control-prev d-none" id="${u_peer_id}_switch_wv_prev" type="button" data-bs-target="#${u_peer_id}_carousel_webcam_video" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next d-none" id="${u_peer_id}_switch_wv_next" type="button" data-bs-target="#${u_peer_id}_carousel_webcam_video" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            </div>
                    </div>
                    <div class="row p-0 m-0 rounded position-relative" >
                        <div class="position-absolute w-75 h-100 d-none" id="`+u_id+`_row_raisehand_status">
                            <div class="w-100 h-100 position-relative">
                                <div class="position-absolute translate-middle start-100 top-50">
                                    <i class="bi bi-hand-index fs-4 text-success"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="fw-bold">
                                `+answer+`
                            </div>
                            <small>
                                `+u_id+`
                            </small>
                        </div>
                        <button id="`+u_id+`_detail_Modal_btn" class=" btn btn-light rounded-end-1 col-3 fs-2" type="button" data-bs-toggle="modal" data-bs-target="#`+u_id+`_detail_Modal">
                            <i class="bi bi-arrow-bar-right"></i>
                        </button>
                        <div class="modal fade" id="`+u_id+`_detail_Modal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog ">
                                <div class="modal-content bg-dark">
                                    <div class="modal-header">
                                        <div class="row w-100 p-0 m-0 " >
                                            <div class="col">
                                                <div class="fw-bold">
                                                    `+answer+`
                                                </div>
                                            </div>
                                            <div class="col">
                                                #`+u_id+`
                                            </div>
                                            <div class="col-2 text-center">
                                                <button type="button" class="btn-close text-light fw-bold" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x-lg"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-body w-100 p-0 m-0" id="`+u_id+`_btn_permission"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="opacity-0 w-0 h-0 position-absolute strat-0 top-0">
                        <audio src="" id="${u_peer_id}_mic_tag"></audio>
                    </div>
                </div>
            </div>
            `;
            document.getElementById("webcam_or_voice_1").innerHTML += base_html;
            if (document.getElementById(u_id+"_btn_permission").innerHTML == "" && u_id != USER_ID) {
                var modal_option = create_btn_permission(u_id);
                document.getElementById(u_id+"_btn_permission").append(modal_option);
            }
            create_call_to_user(u_peer_id,true,undefined);
        }else{
            get_username_with_id(u_peer_id);
        }
    }
}
function get_members_ids(id) {
    var peer = PEER_INFO.NEW_PEER;
    if (peer.connection == undefined) {
        $.ajax({
            type: "POST",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:'/mR/get_members_peer_id',
            data: {
                _token : $('meta[name="csrf-token"]').attr('content'),
                my_info:[USER_NAME,USER_ID,USER_TOKEN],
                room_info:[ROOM_UUID,ROOM_ID,ROOM_PERMISSION],
                id:id
            },
            dataType: "json",
            success: function(data) {
                PEER_INFO["MEMBERS"] = data;
                create_connection_everyone(id);
            }
        });
    }
}
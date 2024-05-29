
export function create_connection_room() {
    // var peer = new Peer(USER_INFO.USER_ID+"_"+ROOM_INFO.ROOM_ID,{port: 9000, path: '/',debug:3});
    // var peer = new Peer(USER_ID+"_"+ROOM_ID,{host:"192.168.1.106", port: 9000, path: '/',debug:1});
    // var peer = new Peer(USER_ID+"_"+ROOM_ID,{host:"192.168.1.104",port: 9000, path: "/myapp",debug:1});
    var peer = new Peer(USER_ID+"_"+ROOM_ID,{host:"192.168.1.103", port: 9000, path: '/',debug:1});

    console.log(Peer);
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
            // conn.on("open",()=>{
            //     if (PEER_INFO['CONNECTION'][peer_id] == undefined) {
            //         PEER_INFO['CONNECTION'] = {};
            //         PEER_INFO['CONNECTION'][peer_id]=[conn,true];
            //     }
            // })
            if (PEER_INFO['CONNECTION'][peer_id] == undefined) {
                PEER_INFO['CONNECTION'][peer_id] = [];
            }
            PEER_INFO['CONNECTION'][peer_id][0] = conn;
            PEER_INFO['CONNECTION'][peer_id][1] = true;
            check_conn_connection(conn,"receive");
            add_member_to_list(conn.peer)
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
        // call.on('stream', function(stream) {
            // if ((PEER_INFO.CALL)[call_peer_id]["media_status"].empty) {
            //     (PEER_INFO.CALL)[call_peer_id]["media_stream"] = undefined;
            // }else{
            //     (PEER_INFO.CALL)[call_peer_id]["media_stream"] = stream;
            // }
            // PEER_INFO["CALL"][call_peer_id]["status"]=true;
            // PEER_INFO["CALL"][call_peer_id]["call"]=call;
            // user_WMSR_set(call_peer_id);
            // console.log("STREAM FROM .answer" , call_peer_id, " || ",call , " | | ",PEER_INFO);
        // });
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
    var status;
    var media_stream;
    console.log("  |  change_WMSR  | " , PEER_INFO.MEDIA_STREAM[1][WMSR]);
    if (PEER_INFO.MEDIA_STREAM[1][WMSR] != undefined) {
        if (WMSR != "raisehand") {
            if (PEER_INFO.MEDIA_STREAM[0][WMSR]) {
                PEER_INFO.MEDIA_STREAM[1][WMSR].getTracks().forEach(function(track) {
                    track.stop();
                });
                status = false;
                PEER_INFO.MEDIA_STREAM[0][WMSR] = false;
                PEER_INFO.MEDIA_STREAM[1][WMSR] = undefined;
            }
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
            case "raisehand":
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
    change_WMSR_overview(WMSR,btn)
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
            if (true) {

            }else{

            }
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
                // case "reconnect_call":
                //     var call = (PEER_INFO.CALL)[u_peer_id]["call"];
                //     call.close();
                //     (PEER_INFO.CALL)[u_peer_id]["call"] = undefined;
                //     (PEER_INFO.CALL)[u_peer_id]["media_stream"] = [{video:undefined,audio:undefined,webcam:undefined,empty:undefined},'reconnect'];
                //     create_call_to_user(u_peer_id,true,true);
                //     console.log("call again to : " ,u_peer_id,call);
                //     break;
            }
            console.log('Received', data,Object.keys(data));
        });
        PEER_INFO['CONNECTION'][u_peer_id][1] = true;

        create_call_to_user(peer_id,undefined,undefined);
    })
}

function user_WMSR_set(u_peer_id) {
    // {video:undefined,audio:undefined,webcam:undefined,empty:undefined}
    // _switch_wv_prev
    var user_id = u_peer_id.split("_")[0];
    var user_media_status;

    if (user_id == USER_ID) {
        user_media_status = PEER_INFO.MEDIA_STREAM;
    }else{
        user_media_status = PEER_INFO.CALL[u_peer_id].media_stream;
    }
    var all_media_status = Object.keys(user_media_status[0]);
    console.log("all_media_status" ,all_media_status);

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
    console.log(user_media_status);




    // if ((PEER_INFO.CALL)[u_peer_id]['media_status'][1].empty) {
    //     (document.getElementById(u_peer_id+"_video_tag") != null)? (document.getElementById(u_peer_id+"_video_tag").srcObject =  undefined):document.getElementById(u_peer_id+"_video_tag");
    //     (document.getElementById(u_peer_id+"_mic_tag") != null)? (document.getElementById(u_peer_id+"_mic_tag").srcObject =  undefined):document.getElementById(u_peer_id+"_mic_tag");
    //     $(("#"+u_peer_id+"_video_div")).addClass("d-none");
    // }else{
    //     if (document.getElementById(u_peer_id+"_mic_tag") != null && document.getElementById(u_peer_id+"_video_tag") != null) {
    //         if ((PEER_INFO.CALL)[u_peer_id]['media_status'].video || (PEER_INFO.CALL)[u_peer_id]['media_status'].webcam) {
                // document.getElementById(u_peer_id+"_mic_tag").srcObject = undefined;
                // ////VIDEO ONLY SWITCH WEBCAM | | VIDEO
                // document.getElementById(u_peer_id+"_video_tag").srcObject = user_media_status[0]['video'];
                // document.getElementById(u_peer_id+"_video_tag").play();
                // $(("#"+u_peer_id+"_video_div")).removeClass("d-none");
    //         }else if((PEER_INFO.CALL)[u_peer_id]['media_status'].audio){
    //             document.getElementById(u_peer_id+"_video_tag").srcObject = undefined;
                // document.getElementById(u_peer_id+"_mic_tag").srcObject = user_media_status[0]['audio'];
                // document.getElementById(u_peer_id+"_mic_tag").play();
    //             $(("#"+u_peer_id+"_video_div")).addClass("d-none");
    //         }
    //         console.log("user_WMSR_set " , u_peer_id , document.getElementById(u_peer_id+"_mic_tag") , (PEER_INFO.CALL)[u_peer_id][0]['media_stream']);
    //     }else{
    //         setTimeout(()=>{
    //             user_WMSR_set(u_peer_id);
    //         },5000)
    //     }
    // }
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
                console.log("disconnected stream");
            })
            mediaStream["onactive"]=(()=>{
                console.log("stream start");

            })
            PEER_INFO["MEDIA_STREAM"][0]["video"]=true;
            PEER_INFO["MEDIA_STREAM"][1]["video"]=mediaStream;
            resolve(mediaStream)
            return mediaStream;
        } catch (ex) {
            resolve(false)
            console.log("Error occurred", ex);
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
            console.log('Failed to get local stream' ,err);
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
            console.log('Failed to get local stream' ,err);
            return false;
        });
    })
}


function make_empty_media_stream(media_only) {
    console.log("created empty");
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

    // else{
    //     PEER_INFO["MEDIA_STREAM"]=[{video:false,audio:false,webcam:false,empty:true},stream];

    //     local_stream[1] = new MediaStream([audioTrack]);
    //     return new MediaStream([audioTrack, videoTrack]);
    // }
}
async function create_call_to_user(u_peer_id,overview_created,call_again) {
    if (overview_created != true) {
        create_overview_for_call(u_peer_id);
    }else{
        if (document.getElementById(u_peer_id+"_mic_webcam_div") != null) {
            var peer = PEER_INFO.NEW_PEER;
            console.log("empty media : " , PEER_INFO.MEDIA_STREAM['empty']);
            // var all_media = Object.keys(PEER_INFO.MEDIA_STREAM[1]);
            // all_media.forEach(mS => {
            //     if (PEER_INFO.MEDIA_STREAM[1][mS] != undefined) {
            //         empty_media[0] = false;
            //         empty_media[1] = PEER_INFO.MEDIA_STREAM[1][mS];
            //     }
            // });
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
            // call.on('stream',(stream)=>{
            //     var metadata =  call.metadata[call.peer];
            //     PEER_INFO["CALL"][u_peer_id]["status"]=true;
            //     PEER_INFO["CALL"][u_peer_id]["media_stream"][1][metadata]=true;
            //     PEER_INFO["CALL"][u_peer_id]["media_stream"][0][metadata]=stream;
            //     // user_WMSR_set(u_peer_id);
            // })
            call.on("close",()=>{
                console.log("CALL closed by :",call.peer);
            })
            console.log("create_call_to_user",u_peer_id,overview_created,call_again);
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

function create_overview_for_call(u_peer_id,answer) {
    var u_id = u_peer_id.split("_")[0];

    if (document.getElementById(u_peer_id+"_mic_webcam_div") == null) {
        if (answer != undefined) {
            var base_html = `
            <div class="w-100 rounded border border-2  my-1" id="${u_peer_id}_mic_webcam_div">
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
                <div class="row p-0 m-0">
                    <div class="col">
                        <div class="fw-bold">
                            `+answer+`
                        </div>
                        <small>
                            `+u_id+`
                        </small>
                    </div>
                    <button id="`+u_id+`_detail_Modal_btn" class=" btn btn-light rounded-end-1 col-2 p-0 m-0 fs-2" type="button" data-bs-toggle="modal" data-bs-target="#`+u_id+`_detail_Modal">
                        <i class="bi bi-arrow-bar-right"></i>
                    </button>
                    <div class="modal fade" id="`+u_id+`_detail_Modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog ">
                            <div class="modal-content bg-dark">
                                <div class="modal-header">
                                    <div class="row w-100 p-0 m-0">
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="opacity-0 w-0 h-0 position-absolute strat-0 top-0">
                    <audio src="" id="${u_peer_id}_mic_tag"></audio>
                </div>
            </div>
            `;
            document.getElementById("webcam_or_voice_in_use").innerHTML += base_html;

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
    }else{
        console.log(peer.connection);
    }
}
function permission_todo() {

}


// const conn =peer.connect("");
// conn.on('open', function() {
//     conn.on('data', function(data) {
//         console.log('Received', data);
//     });
//     conn.send('Hello!');
// });
// var y = 109;
// setInterval(()=>{
//     if (y < 315) {
//         var text  = "/fill -73 "+y+" 95 -66 "+y+" 103 farmland";
//         navigator.clipboard.writeText(text);
//         y+=5;
//     }
// },1000)

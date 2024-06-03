window.addEventListener("DOMContentLoaded",()=>{
    $(document).ready(()=>{
        add_event_sidebar_menu();
        load_old_messages();
        load_old_announcement();
        setTimeout(()=>{
            page_load();
        },1000)
    });
});
function dynamic_loadScreen() {
    var resize_loadscreen = document.getElementById("resize_loadscreen");
    var monitor_size = [window.outerWidth,window.outerHeight,(window.outerWidth < window.outerHeight) ? 1:0];
    resize_loadscreen.style.width = (monitor_size[monitor_size[2]]/4) + "px";
    resize_loadscreen.style.height = (monitor_size[monitor_size[2]]/4)+ "px";
}
dynamic_loadScreen();
function C_M_M_functions() {
    var C_M_M_form = document.getElementById("C_M_M_form");
    var send_file_btn = document.getElementById("send_file_btn");
    var send_file_input = document.getElementById("send_file_input");
    var send_message_text = document.getElementById("send_message_text");
    var voice_chat_message = document.getElementById("voice_chat_message");
    var revers_voice_recorder = document.getElementById("revers_voice_recorder");
    var start_holding_recording_mic = document.getElementById("start_holding_recording_mic");
    var can_use_mic = false;
    C_M_M_form.addEventListener("submit",(e)=>{
        e.preventDefault();
    });
    send_file_btn.addEventListener("click",()=>{
        send_file_input.click();
    });
    send_message_text.addEventListener("click",()=>{
        send_text_message();
    });
    send_file_input.addEventListener("change",()=>{
        send_file(send_file_input);
    });
    voice_chat_message.addEventListener("click",()=>{
        // console.log("mouse_down");
        // send_audi_message();
        change_front_record_audio(true);
    });
    revers_voice_recorder.addEventListener("click",()=>{
        change_front_record_audio(false)
    });
    start_holding_recording_mic.addEventListener("mousedown",()=>{
        can_use_mic = true;
        setTimeout(()=>{
            if (can_use_mic) {
                $("#play_pause_recorded_mic").addClass("d-none");
                document.querySelector("#timer_recorder_mic #sec").innerText = "00";
                document.querySelector("#timer_recorder_mic #min").innerText = "00";
                send_audi_message();
            }
        },200);
    });
    start_holding_recording_mic.addEventListener("mouseup",()=>{
        can_use_mic = false;
    });
    if (AM_I_HOST == "true") {
        var open_announcement_modal =document.getElementById("open_announcement_modal");
        var close_HOST_announcement_modal_btn = document.getElementById("close_HOST_announcement_modal_btn");
        open_announcement_modal.addEventListener("click",send_announcement_modal);
        close_HOST_announcement_modal_btn.addEventListener("click",()=>{
            open_announcement_modal.removeEventListener("click",send_announcement_modal);
        });
    }
}
function send_announcement_modal() {
    var HOST_send_announcement_btn = document.getElementById("HOST_send_announcement_btn");

    var all_mention = {
        "ALL_badge":false,
        "MODS_badge":false,
    };
    for (let i = 0; i < (visible_for_div.children).length; i++) {
        var element = (visible_for_div.children)[i];
        all_mention[element.id] = true;

    };
    var announcement_visibility_select_div = document.getElementById("announcement_visibility_select_div");
    announcement_visibility_select_div.addEventListener("change",()=>{
        var value = announcement_visibility_select_div.value;
        if (value != "") {
            var first_letter = (value[0]).toUpperCase();
            var remaining_letter = value.slice(1);
            var text_mention = "@"+ first_letter + remaining_letter;
            var visible_for_div = document.getElementById("visible_for_div");
            var can_add = true;
            if (all_mention[value.toUpperCase() + "_badge"] == true) {
                can_add = false;
            }
            if (can_add) {
                all_mention[value.toUpperCase()+ "_badge"] = true;
                var first_div = document.createElement("div");
                $(first_div).addClass("col");
                first_div.setAttribute("style","max-width: fit-content;");
                first_div.id = value.toUpperCase()+ "_badge";
                var basic_html = `
                    <div class="w-100 h-100 rounded-2 bg-secondary p-1 row m-auto">
                        <div class="col p-0 m-0" style="max-width: fit-content">
                            <div class="w-100 h-100 d-flex justify-content-center align-items-center">
                                <button class="btn p-0 m-0 fw-bold" id="`+value.toUpperCase()+`_badge_remove_btn">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col p-0 m-0">
                            `+text_mention+`
                        </div>
                    </div>
                `;
                first_div.innerHTML = basic_html;
                visible_for_div.append(first_div);
                document.getElementById(value.toUpperCase()+"_badge_remove_btn").addEventListener("click",()=>{
                    all_mention[value.toUpperCase()+ "_badge"] = false;
                    first_div.remove();
                })
            }
            announcement_visibility_select_div.value = "";
        }
    });
    HOST_send_announcement_btn.addEventListener("click",()=>{
        var text_announcement = document.getElementById("text_announcement");
        if (text_announcement.value != "") {
            $(text_announcement).removeClass("border border-2 border-danger");
            var have_mention = [false,[]];
            var mention_keys = Object.keys(all_mention);
            for (let i = 0; i < mention_keys.length; i++) {
                if (all_mention[mention_keys[i]] == true) {
                    have_mention[0] = true;
                    have_mention[1].push(mention_keys[i]);
                }
            }
            if (have_mention[0]) {
                $(announcement_visibility_select_div).removeClass("border border-2 border-danger");
                var all_found = have_mention[1].find((elm)=>elm =="ALL_badge");
                var mod_found = have_mention[1].find((elm)=>elm =="MODS_badge");
                if (all_found != undefined) {
                    send_announcement_All(text_announcement.value,undefined);
                    all_mention["ALL_badge"] = false;
                    document.getElementById("ALL_badge_remove_btn").click();

                }else if (mod_found != undefined) {
                    send_announcement_only_mods(text_announcement.value,undefined);
                    all_mention["MODS_badge"] = false;
                    document.getElementById("MODS_badge_remove_btn").click();

                }
                text_announcement.value = "";
            }else{
                $(announcement_visibility_select_div).addClass("border border-2 border-danger");
            }
        }else{
            $(text_announcement).addClass("border border-2 border-danger");
        }
    })
}

function send_announcement_All(text,id) {
    var date = new Date();
    var full_string_date = date.getFullYear() + "-" + date.getMonth() + "-"+date.getDay() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
    var log_for_peer = {"sender_id":HOST_ID,"sender_name":HOST_NAME,"text":text,"mention":"@members","room_uuid":ROOM_UUID,"date":full_string_date};

    if (id == undefined) {
        var first_div_announcement = announcement_front_div(log_for_peer);
        announcement_ajax_log(log_for_peer,first_div_announcement,false);
    }else{
        log_for_peer.id = id;
        var all_user = Object.keys(PEER_INFO.CONNECTION);
        for (let i = 0; i < all_user.length; i++) {
            var conn = PEER_INFO.CONNECTION[all_user[i]];
            console.log("MEMBERS " , conn);
            if (conn != undefined && conn[1] == true) {
                conn[0].send({"HOST_announcement":log_for_peer});
                console.log("sended " , log_for_peer);
            }
        }
    }

}

function send_announcement_only_mods(text,data,id) {

    if (data == undefined) {
        $.ajax({
            type: "POST",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:'/mR/get_mods_info',
            data: {
                _token : $('meta[name="csrf-token"]').attr('content'),
                my_info:[USER_NAME,USER_ID,USER_TOKEN],
                room_info:[ROOM_UUID,ROOM_ID,ROOM_PERMISSION],
            },
            success: function(data) {
                send_announcement_only_mods(text,data,undefined);
            }
        });
    }else{
        var date = new Date();
        var full_string_date = date.getFullYear() + "-" + date.getMonth() + "-"+date.getDay() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
        var log_for_peer = {"sender_id":HOST_ID,"sender_name":HOST_NAME,"text":text,"mention":"@mods","room_uuid":ROOM_UUID,"date":full_string_date};
        if (id == undefined) {
            var first_div_announcement = announcement_front_div(log_for_peer);
            announcement_ajax_log(log_for_peer,first_div_announcement,true,data);
        }else{
            log_for_peer.id = id;
            if (data != "false") {
                for (let i = 0; i < data.length; i++) {
                    var mod_peer_id = (data[i]+"_"+ROOM_ID);
                    var conn = PEER_INFO.CONNECTION[mod_peer_id];
                    if (conn != undefined && conn[1] == true) {
                        conn[0].send({"HOST_announcement":log_for_peer});
                    }
                }
            }
        }

    }

}
function announcement_ajax_log(log_for_peer,first_div_announcement,ALL_MEMBER,mods_data){
    $.ajax({
        type: "POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/mR/announcement_log',
        data: {
            _token : $('meta[name="csrf-token"]').attr('content'),
            my_info:[USER_NAME,USER_ID,USER_TOKEN],
            room_info:[ROOM_UUID,ROOM_ID,ROOM_PERMISSION],
            text:log_for_peer.text,
            log_for_peer:log_for_peer
        },
        success: function(data) {
            first_div_announcement.id = data + "_announcement_div";
            if (ALL_MEMBER) {
                send_announcement_only_mods(log_for_peer.text,mods_data,data);
            }else{
                send_announcement_All(log_for_peer.text,data);
            }
            return data;
        }
    });
}
export function announcement_front_div(log_for_peer,notification) {
    var color = (log_for_peer.mention == "@mods")?"bg-danger":"bg-success";
    var first_div = document.createElement("div");
    $(first_div).addClass(("card " + color + " text-light my-2"));
    var basic_HTML_front = `
        <div class="card-header fw-bold text-center">
            `+log_for_peer.sender_name+`
        </div>
        <div class="card-body text-center">
            `+log_for_peer.text+`
        </div>
        <div class="card-footer fw-bold">
            `+log_for_peer.mention+`
        </div>
    `;
    first_div.innerHTML = basic_HTML_front;
    var announcement_saver = document.getElementById("announcement_saver");
    announcement_saver.append(first_div);
    if (log_for_peer.id != undefined) {
        first_div.id = log_for_peer.id + "_announcement_div";
    }
    if (notification == true) {
        document.getElementById("notification_audio").volume="0.6";
        document.getElementById("notification_audio").play();
    }
    return first_div;
}
function load_old_announcement() {
    $.ajax({
        type: "POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/mR/load_old_announcement',
        data: {
            _token : $('meta[name="csrf-token"]').attr('content'),
            my_info:[USER_NAME,USER_ID,USER_TOKEN],
            room_info:[ROOM_UUID,ROOM_ID,ROOM_PERMISSION],
        },
        success: function(data) {
            if (data != "false") {
                var old_announcement = JSON.parse(data);
                var all_date = Object.keys(old_announcement);
                for (let i = 0; i < all_date.length; i++) {
                    var detail = old_announcement[all_date[i]];
                    var first_div = announcement_front_div(detail);
                    first_div.id = detail.id;
                }
            }
        },
        error:function(error){
            console.log(error);
        }
    });
}

function change_front_record_audio(show_hide) {
    var voice_recorder_div = document.getElementById("voice_recorder_div");
    var voice_chat_message = document.getElementById("voice_chat_message");
    var button_overview_form = document.getElementById("button_overview_form");

    if (show_hide) {
        if (button_overview_form == null) {
            button_overview_form = document.createElement("button");
            var width = voice_chat_message.offsetWidth;
            var height = voice_chat_message.offsetHeight;
            var paddingTop = voice_chat_message.style.paddingTop;
            var paddingLeft = voice_chat_message.style.paddingLeft;
            // button_overview_form.style.maxWidth = width + "px";
            // button_overview_form.style.maxHeight = height + "px";
            button_overview_form.style.minWidth = width + "px";
            button_overview_form.style.minHeight = height + "px";
            button_overview_form.style.paddingTop = paddingTop;
            button_overview_form.style.paddingLeft = paddingLeft;
            button_overview_form.innerHTML = voice_chat_message.innerHTML;
            button_overview_form.style.backgroundColor = "#fff";
            button_overview_form.id = "button_overview_form";
            $(button_overview_form).addClass("btn border-0 rounded-0 text-dark text-center z-2 my-auto");
            $(button_overview_form).addClass("position-absolute bottom-0 start-0");
            var C_M_M_form =document.getElementById("C_M_M_form");
            C_M_M_form.append(button_overview_form);
        }else{
            $(button_overview_form).removeClass("d-none");

        }
        dynamic_audio_animation(width,height);
        button_overview_form.style.animation = "dynamic_audio_animation 1s forwards ";
        setTimeout(()=>{
            button_overview_form.innerHTML = "";
        },500);
        setTimeout(()=>{
            $(voice_recorder_div).removeClass("d-none");
            voice_recorder_div.style.animation = "opacity_change 1s forwards";
            setTimeout(()=>{
                voice_recorder_div.style.opacity = "1";
                button_overview_form.style.width = "100%";
                button_overview_form.style.height = "100%";
                button_overview_form.style.backgroundColor = "#212529";

                voice_recorder_div.style.animation = "";
                button_overview_form.style.animation = "";

            },1000)
        },1000);
    }else{
        voice_recorder_div.style.animation = "opacity_change 1s forwards reverse";
        setTimeout(()=>{
            button_overview_form.innerHTML = voice_chat_message.innerHTML;
        },500);
        setTimeout(()=>{
            $(voice_recorder_div).addClass("d-none");
            button_overview_form.style.animation = "dynamic_audio_animation 1s forwards reverse";
            setTimeout(()=>{
                $(button_overview_form).addClass("d-none");
                voice_recorder_div.style.animation = "";
                button_overview_form.style.animation = "";
            },1200);
        },1000);
    }
}




function dynamic_audio_animation(w,h) {
    var animation = `

    @keyframes dynamic_audio_animation{
        0%{
            background-color:#fff;
            width:`+w+`px;
            height:`+h+`px;
        }
        50%{
            width:100%;
            height:`+h+`px;
        }
        100%{
            background-color:#212529;
            width:100%;
            height:100%;
        }
    }
    `;
    if (((document.getElementById("dynamic_animation").innerHTML).search("dynamic_audio_animation")) == -1) {
        document.getElementById("dynamic_animation").innerHTML += animation;
        return animation;
    }

}

async function send_audi_message() {
    var start_holding_recording_mic = document.getElementById("start_holding_recording_mic");
    var audio_tag = document.querySelector("#play_pause_recorded_mic #audio");

    var getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
    var audioChunks =[];
    await getUserMedia({audio: true}, function(stream) {
        var audioRecorder = new MediaRecorder(stream);
        audioRecorder.addEventListener('dataavailable', e => {
            audioChunks.push(e.data);
        });
        if (audio_tag.src != "") {
            audio_tag.removeAttribute("src");
        }
        audio_tag.addEventListener("ended",()=>{
            play_pause.setAttribute("is_played","false")
            play_pause.innerHTML = '<i class="bi bi-play-fill"></i>';
        });
        audioChunks = [];
        audioRecorder.start();
        var timer_id = mic_record_timer(true);
        function stop_recording() {
            audioRecorder.stop();
            mic_record_timer(false,timer_id);
            stream.getTracks().forEach(function(track) {
                track.stop();
            });
            make_ready_to_play(audioChunks);
            start_holding_recording_mic.removeEventListener("mouseup",stop_recording);
        }
        start_holding_recording_mic.addEventListener("mouseup",stop_recording);
    }, function(err) {
        console.log('Failed to get local stream' ,err);
    });
}
function make_ready_to_play(audioChunks) {
    if (audioChunks[0] == undefined) {
        setTimeout(()=>{
            make_ready_to_play(audioChunks);
        },1000);
    }else{
        var audio_tag = document.querySelector("#play_pause_recorded_mic #audio");
        var play_pause = document.querySelector("#play_pause_recorded_mic #play_pause");
        if (audio_tag.src != "") {
            audio_tag.removeAttribute("src");
            play_pause.removeEventListener("click",start_stop_recorded_audio);
            make_ready_to_play(audioChunks);
        }else{
            var blobObj = new Blob(audioChunks, { type: 'audio/webm' });
            var audioUrl = URL.createObjectURL(blobObj);
            send_recorded_audio_mic(audioChunks);
            audio_tag.src = audioUrl;
            play_pause.addEventListener("click",start_stop_recorded_audio);
            $("#play_pause_recorded_mic").removeClass("d-none");
        }
    }
}
function send_recorded_audio_mic(audioChunks) {
    function mic_send() {
        send_file(undefined,audioChunks);
        audioChunks = [];
        document.querySelector("#timer_recorder_mic #sec").innerText = "00";
        document.querySelector("#timer_recorder_mic #min").innerText = "00";
        $("#play_pause_recorded_mic").addClass("d-none");
        document.getElementById("send_recorded_audio").removeEventListener("click",mic_send);
    }
    document.getElementById("send_recorded_audio").addEventListener("click",mic_send);
}
function start_stop_recorded_audio() {
    var audio_tag = document.querySelector("#play_pause_recorded_mic #audio");
    var play_pause = document.querySelector("#play_pause_recorded_mic #play_pause");
    if (play_pause.getAttribute("is_played") == "false") {
        play_pause.setAttribute("is_played","true");
        play_pause.innerHTML = '<i class="bi bi-pause-fill"></i>';
        audio_tag.play();
    }else{
        play_pause.setAttribute("is_played","false");
        play_pause.innerHTML = '<i class="bi bi-play-fill"></i>';
        audio_tag.pause();
    }
}
function mic_record_timer(start_stop,timer_id) {
    if (start_stop) {
        var timer = 0;
        var timer_interval = setInterval(()=>{
            timer++;
            if (timer >= 60) {
                var min_number =  document.querySelector("#timer_recorder_mic #min");
                min_number.innerText = (parseInt(min_number.innerText) + 1).toLocaleString('en-US',{minimumIntegerDigits:2,useGrouping:false});
                timer = 0;
            }
            document.querySelector("#timer_recorder_mic #sec").innerText = (timer).toLocaleString('en-US', {minimumIntegerDigits: 2,useGrouping: false});
        },1000);
        return timer_interval;
    }else{
        clearInterval(timer_id);
    }
}
function send_text_message() {
    var text_message_div = document.getElementById("textarea_for_message_in_room");
    var text = text_message_div.value;
    if (text != "") {
        text = text.replaceAll(/\n+/g, ' ');
        var first_div = file_text_front_div(false,text,USER_NAME,USER_ID);
        send_file_text_ajax(false,text,first_div);
        text_message_div.value = "";
        var message_save_in_room = document.getElementById("message_save_in_room");
        document.getElementById("chat_message_scroll_style").scrollTo(0,message_save_in_room.offsetHeight);
    }
}
function send_file(send_file_input,recorded_mic) {

    var files;
    if (recorded_mic) {
        files = recorded_mic;
    }else{
        files = send_file_input.files;
    }
    if (files.length != 0) {
        for (let i = 0; i < files.length; i++) {
            var file = files[i];
            var file_size = file.size;
            var file_type = file.type;
            var file_name = file.name;
            var formData = new FormData();
            formData.append("file",file);
            formData.append("file_size",file_size);
            formData.append("file_type",file_type);
            formData.append("file_name",file_name);
            formData.append("_token",$('meta[name="csrf-token"]').attr('content'));
            formData.append("my_info",[USER_NAME,USER_ID,USER_TOKEN]);
            formData.append("room_info",[ROOM_UUID,ROOM_ID,ROOM_PERMISSION]);
            var type = (file.type).split("/")[0];
            var first_div = file_text_front_div(true,file,USER_NAME,USER_ID);
            send_file_text_ajax(true,[formData,file_type,type],first_div);
            var message_save_in_room = document.getElementById("message_save_in_room");
            document.getElementById("chat_message_scroll_style").scrollTo(0,message_save_in_room.offsetHeight);
        }
    }
    if (send_file_input != undefined) {
        send_file_input.value = '';
    }
}
function send_file_text_ajax(file_OR_text,media,first_div) {
    if (file_OR_text) {
        $.ajax({
            type: "POST",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:'/mR/upload_formdata',
            data: media[0],
            processData: false,
            contentType: false,
            success: function (data) {
                first_div.id = data;
                $(first_div).removeClass('placeholder');
                file_saved_sendpeer(data,"file",media);
            },
            error: function (error) {
                console.log(error);
            },
            complete:function(){
                var message_save_in_room = document.getElementById("message_save_in_room");
                document.getElementById("chat_message_scroll_style").scrollTo(0,message_save_in_room.offsetHeight);
            }
        });
    }else{
        $.ajax({
            type: "POST",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:'/mR/upload_textmessage',
            data: {
                _token : $('meta[name="csrf-token"]').attr('content'),
                my_info:[USER_NAME,USER_ID,USER_TOKEN],
                room_info:[ROOM_UUID,ROOM_ID,ROOM_PERMISSION],
                text:media
            },
            success: function(data) {
                $(first_div).removeClass('placeholder');
                first_div.id = data;
                file_saved_sendpeer(data,"text",media);
            },
            complete:function(){
                var message_save_in_room = document.getElementById("message_save_in_room");
                document.getElementById("chat_message_scroll_style").scrollTo(0,message_save_in_room.offsetHeight);
            }
        });
    }
}
function file_saved_sendpeer(id,text_file,media) {
    var date = new Date();
    var full_string_date = date.getFullYear() + "-" + date.getMonth() + "-"+date.getDay() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
    var log_for_peer = {"id":id,"sender_id":USER_ID,"sender_name":USER_NAME,"format":text_file,"room_uuid":ROOM_UUID,"date":full_string_date};
    if (text_file == "text") {
        log_for_peer.type = "text";
        log_for_peer[text_file] = media;
    }else{
        log_for_peer.type = media[1];
        log_for_peer[text_file] = id +"."+ (media[1]).split("/")[1];
    }
    var all_connections = PEER_INFO.CONNECTION;
    var peer_id = Object.keys(all_connections);
    for (let i = 0; i < peer_id.length; i++) {
        if ( all_connections[peer_id[i]][1]) {
            var conn = all_connections[peer_id[i]][0];
            conn.send({"media_receive" :log_for_peer});
        }
    }
}



export function file_text_front_div(file_OR_text,media,userName,user_id) {
    var message_save_in_room = document.getElementById("message_save_in_room");
    var base_front=``;
    var color = '#48E8B8'
    if (userName == USER_NAME && user_id == USER_ID) {
        color = '#f4fe61';
    }
    if (file_OR_text) {
        var url = undefined;
        var type;
        if (typeof media == "object") {
                type =(media.type).split("/")[0];
                var detail = media["media_receive"];
                if (detail == undefined) {
                    detail = media;
                    var media_receive = detail.type;
                }else{
                    var media_receive = media["media_receive"][media["media_receive"].format];
                }
            if (detail.id != undefined) {
                var after_dot = media_receive.split('/')[1];
                if ((after_dot).split(";").length != 0) {
                    after_dot = after_dot.split(";")[0];
                }
                var file_name = detail.id + "." + after_dot;
                url = (window.location).origin + "/storage/uploads/"+ROOM_UUID+ "/" +file_name;
            }else{
                url = URL.createObjectURL(media);
            }
        }

        switch (type) {
            case "audio":
                base_front = `
                    <div class="text-start  text-light m-auto p-0 m-0" style="background: none;width:max-content">
                        `+userName+`
                    </div>
                    <div class="card-body m-0 p-0 rounded" style="background: `+color+`;">
                        <div class=" m-1 border-0 text-center">
                            <audio controls class="w-100 rounded-0" src="`+url+`"></audio>
                        </div>
                    </div>
                    <div class="position-absolute start-0 p-0 m-0 bottom-0">
                        <button class="btn p-0 m-0"><i class="bi bi-flag"></i></button>
                    </div>
                `;
                break;
            case "image":
                base_front = `
                    <div class="text-start text-light m-auto p-0 m-0" style="background: none;width:max-content">
                        `+userName+`
                    </div>
                    <div class="card-body m-0 p-0 rounded" style="background: `+color+`;">
                        <div class=" m-1 border-0 text-center">
                            <img class="w-100" src="`+url+`"></img>
                        </div>
                    </div>
                    <div class="position-absolute start-0 p-0 m-0 bottom-0">
                        <button class="btn p-0 m-0"><i class="bi bi-flag"></i></button>
                    </div>
                `;
                break;
            case "video":
                base_front = `
                    <div class="text-start text-light m-auto p-0 m-0" style="background: none;width:max-content">
                        `+userName+`
                    </div>
                    <div class="card-body m-0 p-0 rounded" style="background:`+color+`;">
                        <div class=" m-1 border-0 text-center">
                            <video controls class="w-100" src="`+url+`"></video>
                        </div>
                    </div>
                    <div class="position-absolute start-0 p-0 m-0 bottom-0">
                        <button class="btn p-0 m-0"><i class="bi bi-flag"></i></button>
                    </div>
                `;
                break;
            case "application":
                base_front = `
                    <div class="text-start text-light m-auto p-0 m-0" style="background: none;width:max-content">
                        `+userName+`
                    </div>
                    <div class="card-body m-0 p-0 rounded" style="background: `+color+`;">
                        <div class=" m-1 border-0 text-center">
                            <div class="row w-100 m-auto">
                                <div class="col text-center m-auto fw-bolder text-break">
                                    `+file_name+`
                                </div>
                                <div class="col">
                                    <a href="`+url+`" class="btn btn-outline-success"><i class="bi bi-download"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="position-absolute start-0 p-0 m-0 bottom-0">
                        <button class="btn p-0 m-0"><i class="bi bi-flag"></i></button>
                    </div>
                `;
                break;
        }
    }else{
        var text = '';
        if (typeof media == "object") {
            var detail = media["media_receive"];
            if (detail == undefined) {
                detail = media;
            }
            text = media.text;
        }else{
            text = media;

        }
        base_front = `
            <div class="text-start  text-light m-auto p-0 m-0" style="background: none;width:max-content">
                `+userName+`
            </div>
            <div class="card-body m-0 p-0 rounded" style="background: `+color+`;">
                <div class=" mx-4 my-0 text-center">
                    `+text+`
                </div>
            </div>
            <div class="position-absolute start-0 p-0 m-0 bottom-0">
                <button class="btn p-0 m-0"><i class="bi bi-flag"></i></button>
            </div>
    `;
    }
    var first_div = document.createElement("div");
    first_div.setAttribute("class","card placeholder  m-1 my-4 border-0 position-relative");
    first_div.setAttribute("style","background: none");
    first_div.innerHTML = base_front;
    message_save_in_room.append(first_div);
    return first_div;
}
async function load_old_messages() {
    $.ajax({
        type: "POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/mR/load_old_message',
        data: {
            _token : $('meta[name="csrf-token"]').attr('content'),
            my_info:[USER_NAME,USER_ID,USER_TOKEN],
            room_info:[ROOM_UUID,ROOM_ID,ROOM_PERMISSION],
        },
        success: function(data) {
            var old_messages = JSON.parse(data);
            var all_date = Object.keys(old_messages);
            for (let i = 0; i < all_date.length; i++) {
                var detail = old_messages[all_date[i]];
                var file_OR_text = (detail.format == "text") ? false:true;
                var user_id = detail.sender_id;
                var userName = detail.sender_name;
                var first_div = file_text_front_div(file_OR_text,detail,userName,user_id);
                first_div.id = detail.id;
                $(first_div).removeClass("placeholder");
            }
        },
    });
}

async function page_load() {
    update_member_list();
    C_M_M_functions();
    $('#loadScreen').addClass("d-none");
    $('#confirm_connect').removeClass("d-none");
}
document.getElementById("dont_connect").addEventListener("click",()=>{
    location.replace("/");
})
document.getElementById("open_connection").addEventListener("click",()=>{
    $('#confirm_connect').addClass("d-none");
    $('#meatingRoom').removeClass("d-none");
    create_connection_room();
    check_afk_status(false);

})
function check_afk_status(k_t) {
    var AFK_status = true;
    function set_afk_t() {
        AFK_status = true;
    }
    function set_afk_f() {
        AFK_status = false;
    }
    window.addEventListener("mousemove",set_afk_f);
    window.addEventListener("blur",set_afk_t);
    window.addEventListener("focus",set_afk_f);
    setTimeout(()=>{
        if (AFK_status) {
            if (k_t) {
                location.replace("/");
            }else{
                document.getElementById("notification_audio").volume="0.6";
                document.getElementById("notification_audio").play();
                var random_test = Math.floor(Math.random()*2);
                if(random_test == 0){
                    AFK_math_test();
                }else{
                    AFK_mouse_test();
                }
                window.removeEventListener("mousemove",set_afk_f);
                window.removeEventListener("blur",set_afk_t);
                window.removeEventListener("focus",set_afk_f);
            }
        }
    },1000)
}

function AFK_math_test() {
    var tow_random_number = [Math.floor(Math.random()*10)+1,Math.floor(Math.random()*10)+1];
    var random_operators = Math.floor(Math.random()*4);
    var operators = ['-','+','*','/'];
    var result = tow_random_number[0] + operators[random_operators] + tow_random_number[1];
    if ((random_operators == 3 && (tow_random_number[0] % tow_random_number[1] == 0))|| eval(result) < 0) {
        AFK_math_test();
    }else{
        var first_div = document.createElement("div");
        var btn_for_afk_test_modal = document.createElement("button");
        $(btn_for_afk_test_modal).addClass("opacity-0 d-none w-0 h-0 invisible")
        btn_for_afk_test_modal.setAttribute("data-bs-toggle","modal");
        btn_for_afk_test_modal.setAttribute("data-bs-target","#math_test_div");
        btn_for_afk_test_modal.id = "btn_for_afk_test_modal";
        var basic_front_html = `
        <div class="modal fade" id="math_test_div" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-light">
                    <div class="modal-header">
                        <h3 class="modal-title">Do Test</h3>
                        <button type="button" id="close_AFK_math_modal_btn" class="d-none w-0 h-0 invisible" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h6 id="timer_AFK_math_test"></h6>
                        
                    </div>
                    <div class="modal-body">
                    <div class="w-100 h-100 d-flex justify-content-center align-item-center">
                        <div class="w-100">
                            <div class="row w-100 m-auto">
                                <div class="col " style="max-width: fit-content">`+tow_random_number[0]+`</div>
                                <div class="col  p-0" style="max-width: fit-content">`+operators[random_operators]+`</div>
                                <div class="col " style="max-width: fit-content">`+tow_random_number[1]+`</div>
                                <div class="col " style="max-width: fit-content">=</div>
                                <div class="col d-flex justify-content-center">
                                    <input class="form-control w-100 h-100 text-center" type="number" style="max-width: fit-content;" id="result_input">
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary w-100" id="submit_result_btn">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        `;
        first_div.append(btn_for_afk_test_modal);
        first_div.innerHTML += basic_front_html;
        document.getElementById("body").append(first_div);
        document.getElementById("btn_for_afk_test_modal").click();
        var timer = 20;
        var Interval_timer = setInterval(()=>{
            if (timer == 0) {
                location.replace("/");
            }
            document.getElementById("timer_AFK_math_test").innerText = timer;
            timer--;
        },1000);
        document.getElementById("submit_result_btn").addEventListener("click",()=>{
            var result_input =document.getElementById("result_input");
            if (eval(result) == parseInt(result_input.value)) {
                document.getElementById("close_AFK_math_modal_btn").click();
                clearInterval(Interval_timer);
            }else{
                location.replace("/");
            }
        })
    }

}
function AFK_mouse_test() {
    var first_div = document.createElement("div");
    $(first_div).addClass("vw-100 vh-100 bg-dark z-3 position-absolute");
    var timer_div = document.createElement("div");
    $(timer_div).addClass("position-absolute z-n1 text-light fs-2 start-50 top-50 translate-middle");
    timer_div.innerText = 30;
    first_div.append(timer_div);
    var timer = 30;
    var Interval_timer = setInterval(()=>{
        if (timer == 0) {
            location.replace("/");
        }
        timer_div.innerText = timer;
        timer--;
    },1000);
    var result = [];
    var screen_size = [window.innerWidth,window.innerHeight];
    for (let i = 0; i < 10; i++) {
        var x = Math.floor(Math.random()*(screen_size[0]-300) + 100);
        var y = Math.floor(Math.random()*(screen_size[1]-300) + 100);
        var color = ['primary','success','info','warning','danger','light'];
        var random_int_color = Math.floor(Math.random()*5);
        var div = document.createElement("div");
        div.style.minWidth = "30px";
        div.style.minHeight = "30px";
        $(div).addClass("position-absolute rounded-circle bg-"+color[random_int_color]+" opacity-75 d-none");
        div.id = i;
        if (i == 0) {
            $(div).removeClass("d-none");
        }
        div.style.left = x;
        div.style.top = y;
        div.style.cursor="pointer";
        first_div.append(div);
        result.push([x,y,div]);
    }
    var clicked_count = 0;
    for (let i = 0; i < result.length; i++) {
        result[i][2].addEventListener("click",()=>{
            result[i][2].remove();
            if (result[i+1] != undefined) {
                $(result[i+1][2]).removeClass("d-none");
            }
            clicked_count++;
            if (clicked_count == 10) {
                clearInterval(Interval_timer);
                first_div.remove();
            }
        });
    }
    document.getElementById("body").insertBefore(first_div,document.getElementById("body").firstChild);
}
function add_event_sidebar_menu() {
    var sidebar_showbtn = document.querySelectorAll("#sidebar_showbtn");
    sidebar_showbtn.forEach((btns)=>{
        btns.addEventListener("click",()=>{
            show_sidebar_btns(btns);
        })
    })
    const all_btn = document.querySelectorAll("[sidebar]");
    all_btn.forEach((btn)=>{
        btn.addEventListener("click",()=>{
            document.querySelector("[sidebar_detail="+btn.getAttribute("sidebar")+"]").disabled = true;
            change_sidebar_details(btn,btn.getAttribute("sidebar"));
        })
    })
}
function show_sidebar_btns(btns) {
    btns.disabled = true;
    var m_c_a = btns.getAttribute("sidebar_detail");
    var active_side = undefined;
    document.querySelectorAll("[sidebar]").forEach(act => {
        if (act.getAttribute("active") != null) {
            active_side = act;
        }
    });
    active_side.removeAttribute("active");
    document.getElementById(m_c_a+"_div").style.animation = "opacity_change 1s forwards reverse";
    var all_btns = document.querySelectorAll("[sidebar]");
    $(all_btns[0]).addClass("animate__animated animate__fadeInUp animate__faster");
    $(all_btns[2]).addClass("animate__animated animate__fadeInDown animate__faster");
    $(all_btns[1]).addClass("animate__animated animate__fadeIn animate__faster");
    setTimeout(()=>{
        document.getElementById(m_c_a+"_div").style.opacity = "0";
        document.getElementById(m_c_a+"_div").style.animation = "";
        $("#"+m_c_a+"_div").addClass('d-none');
        document.getElementById("sidebar_report_activity_leave").style.animation = "sidebar_RAL_animation 1.5s forwards reverse";
        document.getElementById("sidebarmenu_div").style.animation = "sidebar_changesize 1s forwards reverse ";
        setTimeout(()=>{
            document.getElementById("sidebar_report_activity_leave").style.bottom = "0%";
            document.getElementById("sidebar_report_activity_leave").style.animation = "";
            document.getElementById("sidebarmenu_div").style.width= $("#sidebarmenu_div").innerWidth() + "px";
            document.getElementById("sidebarmenu_div").style.animation = "";
            $("#sidebar_btns").removeClass("d-none");
            setTimeout(()=>{
                $(all_btns[0]).removeClass("animate__animated animate__fadeInUp animate__faster");
                $(all_btns[2]).removeClass("animate__animated animate__fadeInDown animate__faster");
                $(all_btns[1]).removeClass("animate__animated animate__fadeIn animate__faster");
                all_btns.forEach((btn)=>{
                    btn.disabled = false;
                });
                setTimeout(()=>{
                    btns.disabled = false;
                },1000);
            },1000);
        },1500);
    },1000);
}
function update_member_list() {
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
        },
        dataType: "json",
        success: function(data) {
            for (let i = 0; i < data.length; i++) {
                add_member_to_list(data[i])
            }
        }
    });
}

export function add_member_to_list(peer_id,data) {
    var id = peer_id.split("_")[0];
    if (data == undefined) {
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
                id:id
            },
            success: function(data) {
                add_member_to_list(peer_id,data)
            }
        });
    }else{
        // switch (ROOM_PERMISSION) {
        //     case "HOST":
        //         modal_option = `
        //         <button class="btn btn-info rounded-top-0"><i class="bi bi-info-circle"></i></button>
        //         <button class="btn btn-success"><i class="bi bi-chat-left-dots"></i></button>
        //         <button class="btn btn-warning"><i class="bi bi-box-arrow-right"></i></button>
        //         <button class="btn btn-danger "><i class="bi bi-exclamation-octagon"></i></button>
        //         <button class="btn btn-dark "><i class="bi bi-person-fill-up"></i></button>
        //         <button class="btn btn-dark rounded-top-0"><i class="bi bi-person-fill-down"></i></button>
        //         `;
        //         break;
        //     case "MOD":
        //         modal_option = `
        //         <button class="btn btn-info rounded-top-0"><i class="bi bi-info-circle"></i></button>
        //         <button class="btn btn-success"><i class="bi bi-chat-left-dots"></i></button>
        //         <button class="btn btn-warning"><i class="bi bi-box-arrow-right"></i></button>
        //         <button class="btn btn-danger rounded-top-0"><i class="bi bi-exclamation-octagon"></i></button>
        //         `;
        //         break;
        //     default:
        //         modal_option = `
        //         <button class="btn btn-info rounded-top-0"><i class="bi bi-info-circle"></i></button>
        //         <button class="btn btn-success"><i class="bi bi-chat-left-dots"></i></button>
        //         `;
        //         break;
        // }
        var basic_fron_member_list = `
        <div class="my-2" id="`+id+`_member_list_div">
            <div class="container w-100">
                <button class="btn btn-info d-flex gap-2 w-100 justify-content-between"data-bs-toggle="modal" data-bs-target="#`+id+`_modal">
                    <div class="m-auto">
                        <div class="text-center w-100">
                            <h6 id="HOST_userName" class="mb-0 fw-bolder">`+data[0]+`</h6>
                            <small>`+id+`</small>
                        </div>
                    </div>
                    <small id="`+id+`_internet_status" class="opacity-100 text-`+((id == USER_ID)?"success":"danger")+` my-auto text-end"><i class="bi bi-circle-fill"></i></small>
                </button>
            </div>
            <div class="modal" id="`+id+`_modal" aria-labelledby="HOST_label" aria-hidden="true" tabindex="-1" >
                <div class="modal-dialog">
                    <div class="modal-content position-relative bg-dark text-light p-0 m-0">
                        <div class="modal-header text-center">
                            <div class="text-center w-100">
                                <h6 id="HOST_userName" class="mb-0 fw-bolder">`+data[0]+`</h6>
                                <small>`+id+`</small>
                            </div>
                        </div>
                        <div class="modal-footer p-0 m-0">
                            <div class="w-100 btn-group my-1 p-2">
                                <button class="btn text-light  rounded-0"><i class="bi bi-volume-up"></i></button>
                                <button class="btn text-light  rounded-0"><i class="bi bi-flag"></i></button>
                            </div>
                            <div class="w-100 p-0 m-0" id="`+id+`_btn_permission"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
        switch (data[1]) {
            case "HOST":
                document.getElementById("hosts_info").innerHTML += basic_fron_member_list;
                break;
            case "MOD":
                document.getElementById("mods_info").innerHTML += basic_fron_member_list;
                
                break;
            default:
                document.getElementById("members_info").innerHTML += basic_fron_member_list;
                
                break;
        }
        // if (data[1] == "MOD") {
        //     document.getElementById("mods_info").innerHTML += basic_fron_member_list;
        // }else{
        //     document.getElementById("members_info").innerHTML += basic_fron_member_list;
        // }
        if (document.getElementById(id+"_btn_permission").innerHTML == "" && id != USER_ID) {
            var modal_option = create_btn_permission(id);
            document.getElementById(id+"_btn_permission").append(modal_option);
        }
    }
}
function change_sidebar_details(btn,m_c_a) {
    var active_side = undefined;
    document.querySelectorAll("[sidebar]").forEach(act => {
        if (act.getAttribute("active") != null) {
            active_side = act;
        }
    });
    if (active_side != btn){
        if (active_side == undefined) {
            btn.setAttribute("active","");
        }else {
            active_side.removeAttribute("active");
            btn.setAttribute("active","");
        }
        var monitorSize = window.innerWidth;
        var percentage = 0;
        if (monitorSize>990) {
            percentage = 30;
        }else if (monitorSize >575){
            percentage = 50;
        }else{
            percentage = undefined;
        }
        animation_sidebar(percentage);
        document.getElementById("sidebar_report_activity_leave").style.animation = "sidebar_RAL_animation 1.5s forwards";
        setTimeout(()=>{
            document.getElementById("sidebar_report_activity_leave").style.bottom = "93.5%";
            document.getElementById("sidebar_report_activity_leave").style.animation = "";
            $("#"+m_c_a+"_div").removeClass("d-none");
            document.getElementById(m_c_a+"_div").style.animation = "opacity_change 1s forwards"
            setTimeout(()=>{
                document.getElementById(m_c_a+"_div").style.opacity = "1";
                document.getElementById(m_c_a+"_div").style.animation = "";
                document.querySelector("[sidebar_detail="+m_c_a+"]").disabled = false;
            },1000)
            $("#sidebar_btns").addClass("d-none");
        },1500)
    }
}
function animation_sidebar(percentage) {
    var width = $("#sidebarmenu_div").innerWidth();
    var all_animation= document.getElementById("dynamic_animation").innerHTML;
    var bool_search = all_animation.search("sidebar_changesize");
    if (bool_search == -1) {
        var animation_css = `
        @keyframes sidebar_changesize {
            0%{
                width:`+width+`px
            }
            100%{
                width:`+percentage+`%
            }
        }
        `;
        document.getElementById("dynamic_animation").innerHTML += animation_css;
    }
    if (document.getElementById("sidebarmenu_div").style.animation != "") {
        document.getElementById("sidebarmenu_div").style.width= $("#sidebarmenu_div").innerWidth() + "px";
        document.getElementById("sidebarmenu_div").style.animation = "";
    }
    document.getElementById("sidebarmenu_div").style.animation = "sidebar_changesize 1s forwards";
    var all_btns = document.querySelectorAll("[sidebar]");
    $(all_btns[0]).addClass("animate__animated animate__fadeOutDown animate__faster");
    $(all_btns[2]).addClass("animate__animated animate__fadeOutUp animate__faster");
    $(all_btns[1]).addClass("animate__animated animate__fadeOut animate__faster");
    all_btns.forEach((btn)=>{
        btn.disabled = true;
    });
    setTimeout(()=>{
        document.getElementById("sidebarmenu_div").style.width= $("#sidebarmenu_div").innerWidth() + "px";
        document.getElementById("sidebarmenu_div").style.animation = "";
        setTimeout(()=>{
            var chat_message_scroll_style = document.getElementById("chat_message_scroll_style");
            var height = document.getElementById("message_save_in_room").offsetHeight;
            chat_message_scroll_style.scrollTo(0,parseInt(height));
        },500)
        setTimeout(()=>{
            $(all_btns[0]).removeClass("animate__animated animate__fadeOutDown animate__faster");
            $(all_btns[2]).removeClass("animate__animated animate__fadeOutUp animate__faster");
            $(all_btns[1]).removeClass("animate__animated animate__fadeOut animate__faster");
        },1000)
    },1000);

}
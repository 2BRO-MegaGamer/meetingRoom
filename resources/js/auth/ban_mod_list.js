function after_load_event() {
    var all_btn = document.querySelectorAll("#remove_from_list");
    var tabel = document.querySelector("table");
    var ban_member = document.querySelectorAll("#ban_member");
    var submit_warn = document.querySelectorAll("#submit_warn");
    ban_member.forEach(btn=>{
        if (btn != null) {
            btn.addEventListener("click",()=>{
                ajax_ban_member(btn)
            })
        }
    })
    submit_warn.forEach(btn=>{
        if (btn != null) {
            btn.addEventListener("click",()=>{
                submit_warning_user(btn);
            })
        }
    })
    all_btn.forEach(btn=>{
        btn.addEventListener("click",(e)=>{
            remove_member_from_ban_modlist(btn.getAttribute("user_id"),tabel);
        })
    })
}
function remove_member_from_ban_modlist(id,ban_mod) {
    $.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/manageRoom/remove_ban_mod',
        data: {
            ban_mod:ban_mod.id,
            room_info:room_id,
            id:id
        },
        success: function(data) {
            document.getElementById(id+"_row").remove();
        }
    });
}
function ajax_ban_member(button) {
    var id = button.getAttribute("user_id");
    $.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/manageRoom/ban_member',
        data: {
            room_info:room_id,
            id:id
        },
        success: function(data) {
            document.getElementById(id+"_row").remove();
            console.log(data);
        }
    });
}
function submit_warning_user(button) {
    var id = button.getAttribute("user_id");
    var warning_text = document.getElementById(id+"_textarea_warning");
    $.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/manageRoom/warning_member',
        data: {
            room_info:room_id,
            id:id,
            warning_text:warning_text.value
        },
        success: function(data) {
            warning_text.value = "";
            document.getElementById(id+"_close_btn").click();
        }
    });
}
window.addEventListener("DOMContentLoaded",after_load_event);
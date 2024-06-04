
var profile_menu_btn = document.getElementById("profile_menu");
var room_menu_btn = document.getElementById("room_menu");
var profile_div = document.getElementById("profile");
var rooms_div = document.getElementById("rooms");
profile_menu_btn.addEventListener("click",show_hide_sides);
room_menu_btn.addEventListener("click",show_hide_sides);
var showed = true;
function show_hide_sides() {
    var set_on_off_btn = [(showed)?room_menu_btn:profile_menu_btn,(showed)?profile_menu_btn:room_menu_btn];
    var set_on_off_div = [(showed)?rooms_div:profile_div,(showed)?profile_div:rooms_div];
    $(set_on_off_div[0]).removeClass("d-none");
    $(set_on_off_div[1]).addClass("d-none");
    $(set_on_off_btn[0]).removeClass("btn-outline-dark");
    $(set_on_off_btn[0]).addClass("text-light bg-dark border border-success border-3");
    $(set_on_off_btn[1]).removeClass("text-light bg-dark border border-success border-3");
    $(set_on_off_btn[1]).addClass("btn-outline-dark");
    (showed)?(showed = false) : (showed = true);
}

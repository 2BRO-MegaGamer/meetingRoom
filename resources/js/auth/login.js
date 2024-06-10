var email_login = document.getElementById("email_login");
var password_login = document.getElementById("password_login");
var stay_login = document.getElementById("remember");
var btn_sub_err = document.getElementById("btn_sub_err");
var div_first = document.getElementById("div_first")
var all_input = [email_login,password_login]
var errors = 0
window.addEventListener("input",()=>{
    login_btn_click();
})
function login_btn_click() {
    errors=0;
    stay_login.value = (stay_login.checked)?"on":"off";
    all_input.forEach(in_check => {
        if (in_check.value == "") {
            $(in_check).addClass("is-invalid");
            errors++;
        }else{
            $(in_check).removeClass("is-invalid");
        }
        if ((in_check.value).split(" ").length > 1) {
            $(in_check).addClass("is-invalid")
            errors++;
        }
    });
    if (errors == 0) {
        btn_sub_err.removeAttribute("disabled");
    }else{
        btn_sub_err.setAttribute("disabled","");
    }
}
if (document.getElementById("UN_check") != null) {
    username_NOT_check()
}else if (document.getElementById("PASS_check")) {
    password_NOT_check()
}
function username_NOT_check() {
    var error_div = document.getElementById("error_div");
    var error_text = document.createElement("span")
    $(error_text).addClass("class","bg-info mx-auto rounded rounded-top-0 p-1")
    error_text.id = error_email;
    error_text.innerText = "The email or password is incorrect"
    error_div.append(error_text)
}
function password_NOT_check() {
    $(div_first).addClass("border border-3 border-danger");
}
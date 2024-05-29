var email_login = document.getElementById("email_login");
var password_login = document.getElementById("password_login");
var stay_login = document.getElementById("remember");
var btn_sub_err = document.getElementById("btn_sub_err");
var form_login_asl = document.getElementById("form_login_asl");
var div_first_kol = document.getElementById("div_first_kol")
var all_input = [email_login,password_login]
var errors = 0

// password_1_singup.setAttribute("class","form-control is-invalid");
addEventListener("input",()=>{

    login_btn_click();


})

function login_btn_click(ravesh) {

    errors=0;
    if (stay_login.checked == false) {
        stay_login.value = "off"
    }else{
        stay_login.value = "on"
    }
    all_input.forEach(in_check => {
        if (in_check.value == "") {
            in_check.setAttribute("class","form-control is-invalid");
            errors++;
            
        }else{
            in_check.setAttribute("class","form-control");
        }
        if ((in_check.value).split(" ").length > 1) {
            in_check.setAttribute("class","form-control is-invalid");
            errors++;
        }

    });
    if (errors == 0) {
        btn_sub_err.removeAttribute("disabled")
    }else{
        btn_sub_err.setAttribute("disabled","")
    }


}


if (document.getElementById("UN_check") != null) {
    username_NOT_check()
}else if (document.getElementById("PASS_check")) {
    password_NOT_check()
}


function username_NOT_check() {
    var UN_check = document.getElementById("UN_check");
    var user_check = UN_check.getAttribute("ch_u_user");
    var error_div = document.getElementById("error_div");
    var error_text = document.createElement("span")
    error_text.setAttribute("class","bg-info mx-auto rounded rounded-top-0 p-1")
    error_text.setAttribute("id","error_email")
    error_text.innerText = "The email or password is incorrect"
    error_div.append(error_text)

}

function password_NOT_check() {
    var same_style = div_first_kol.getAttribute("class");
    var new_style = same_style + " " + "border border-3 border-danger "
    div_first_kol.removeAttribute("class")
    div_first_kol.setAttribute("class",new_style)
}
if (document.getElementById("refresh_most") != null) {
    window.location.replace("/CLJOLE/html/php/login.php");
    window.location.replace("/CLJOLE/html/html.php");
    window.location.replace("/CLJOLE/html/html.php");
    
}

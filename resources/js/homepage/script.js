export function show_hide_pass(div) {
    var TOF = div.getAttribute("is_showed");
    var info = div.getAttribute("for_input");
    var showpass_input = document.getElementById(info);
    if (TOF == "true") {
        showpass_input.setAttribute("type","password");
        div.setAttribute("is_showed","false");
        div.children[0].setAttribute("class","bi bi-eye");
    }else{
        showpass_input.setAttribute("type","text");
        div.setAttribute("is_showed","true");
        div.children[0].setAttribute("class","bi bi-eye-slash");
    }
}



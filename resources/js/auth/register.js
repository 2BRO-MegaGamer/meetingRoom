
function submit_check_errors(username_check,email_check,phone_check) {
    document.getElementById("logbtn_submit_singup").disabled = true;
    const firstname_singup =document.getElementById("firstname_singup");
    const lastname_singup =document.getElementById("lastname_singup");
    const username_singup =document.getElementById("username_singup");
    const email_singup =document.getElementById("email_singup");
    const phone_number =document.getElementById("phone_number");
    const password_0_singup =document.getElementById("password_0_singup");
    const password_1_singup =document.getElementById("password_1_singup");
    const gender_option_singup =document.getElementById("gender_option_singup");
    const all_input_in_form = [firstname_singup,lastname_singup,username_singup,email_singup,phone_number,password_0_singup,password_1_singup,gender_option_singup];
    var empty_input = all_input_in_form.filter((inp=> inp.value == ""));
    var have_value = all_input_in_form.filter((inp=> inp.value != ""));
    var error = 0;
    empty_input.forEach((emp_inp=>{
        $(emp_inp).addClass("border border-2 border-danger");
        error++;
    }));
    have_value.forEach((val_inp=>{
        if (val_inp.id == "firstname_singup" || val_inp.id == "lastname_singup" || val_inp.id == "username_singup") {
            console.log("test lenght ",(val_inp.value).length > 64);
            if ((val_inp.value).length > 64) {
                $(val_inp).addClass("border border-2 border-danger");
                error++;
            }else{
                $(val_inp).removeClass("border border-2 border-danger");
            }
        }
        var is_error;
        switch (val_inp.id) {
            case "phone_number":
                is_error = check_phone_num(val_inp);
                break;
            case "password_0_singup":
                is_error = check_0_password(val_inp);
                break;
            case "password_1_singup":
                is_error = check_both_password(val_inp,password_0_singup);
                break;
            case "email_singup":
                is_error = email_reg_test(val_inp);
                break;
            case "username_singup":
                is_error = username_reg_test(val_inp,username_singup);
                break;
        }
        if (is_error) {
            $(val_inp).addClass("border border-2 border-danger");
            error++;
        }else{
            $(val_inp).removeClass("border border-2 border-danger");
        }
    }))
    if (username_check == "false") {
        show_hide_error_messages("username_not_uniqe");
        $(username_singup).addClass("border border-2 border-danger");
    }else{
        $(username_singup).removeClass("border border-2 border-danger");
    }
    if (email_check == "false") {
        show_hide_error_messages("email_not_uniqe")
        $(email_singup).addClass("border border-2 border-danger");
    }else{
        $(email_singup).removeClass("border border-2 border-danger");
    }
    if (phone_check == "false") {
        show_hide_error_messages("phone_not_uniqe");
        $(phone_number).addClass("border border-2 border-danger");
    }else{
        $(phone_number).removeClass("border border-2 border-danger");
    }
    if (username_check == "true" && email_check == "true" && phone_check == "true") {
        document.getElementById("form_singup").submit();
    }else{
        if (error === 0 && username_check == undefined && email_check == undefined && phone_check == undefined) {
            check_if_unique([username_singup,email_singup,phone_number])
        }else{
            document.getElementById("logbtn_submit_singup").disabled = false;
        }
    }
}

function email_reg_test(val_inp) {
    const emailMessage = document.getElementById('email_singup_error');
    const regexTest = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(val_inp.value);
    if (!regexTest) {
        show_hide_error_messages("email_not_valid")
        return true;
    }else{
        if (val_inp.getAttribute("class").split("border border-2 border-danger").length > 1) {
            val_inp.setAttribute("class",val_inp.getAttribute("class").split("border border-2 border-danger")[0]);
        }
        return false;
    }
}
function username_reg_test(val_inp) {
    const regexTest = /^[a-zA-Z0-9_]*$/.test(val_inp.value);
    if (!regexTest) {
        return true;
    }else{
        return false;
    }
}

function check_0_password(val_inp) {
    const passwordMessage = document.getElementById('password_0_singup_error');
    if ( (val_inp.value).length < 8 ) {
        val_inp.setAttribute("class",(val_inp.getAttribute("class") + " border border-2 border-danger"));
        show_hide_error_messages("password_not_enough")
        return true;
    }else{
        return false;
    }
}



function check_both_password(val_inp,pass_input_0) {
    const passwordMessage = document.getElementById('password_1_singup_error');
    if (val_inp.value != pass_input_0.value) {
        show_hide_error_messages("password_not_same")
        return true;
    }else{
        return false;
    }
}
function check_phone_num(val_inp) {
    const phone_message = document.getElementById('phone_number_error');
    const regexTest_phone_number = /09(1[0-9]|3[1-9]|2[1-9])-?[0-9]{3}-?[0-9]{4}/.test(val_inp.value);
    if (!regexTest_phone_number) {
        show_hide_error_messages("phone_not_valid")
        return true;
    }else{
        return false;
    }
}
function check_if_unique(val_inp) {
    $.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/register/check_if_unique',
        data: {
            userName:val_inp[0].value,
            email:val_inp[1].value,
            phone_number:val_inp[2].value
        },
        success: function(data) {
            submit_check_errors(data.userName,data.email,data.phone_number);
        }
    });
}
document.getElementById("phone_number").addEventListener("input",(e)=>{style_for_phone_number(e,document.getElementById("phone_number"));})
function style_for_phone_number(e,element) {
    var test_regex_number_only = /^[0-9]*$/.test(e.data);
    if (test_regex_number_only === false) {
        if (isNaN((element.value))) {
            element.value = (element.value).substring(0, (element.value).length - 1);
        }
    }
}
document.getElementById("logbtn_submit_singup").addEventListener("click",(event)=>{
    event.preventDefault();
    document.getElementById("logbtn_submit_singup").disabled = true;
    submit_check_errors(undefined,undefined,undefined);
})
